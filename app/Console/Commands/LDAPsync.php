<?php

namespace Lara\Console\Commands;

use Illuminate\Console\Command;
use Lara\LdapPlatform;
use Lara\Person;
use Lara\Section;
use Lara\User;
use Lara\utilities\LdapUtility;
use Log;

class LDAPsync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lara:ldapsync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Overwrite name & status for every Person in Lara DB with the latest state from LDAP.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * Updates status of each Person saved in Lara with the latest state from LDAP
     * Main purpose: data is usually updated when a member logs in. This function targets updates for members who stop visiting Lara.
     *
     *
     * @return mixed
     */
    public function handle()
    {
        // Inform the users
        Log::info('Starting LDAP sync...');
        $this->info('Starting LDAP sync...');

        // get a list of all persons saved in Lara, except ldap-override
        $persons = Person::query()
            ->whereNotNull('prsn_ldap_id')
            ->whereRaw('convert( prsn_ldap_id, unsigned integer) < 9999')
            ->orderByRaw('convert( prsn_ldap_id, unsigned integer) desc')
            ->get();

        // start counting time before processing every person
        $counterStart = microtime(true);

        // Initiate progress bar
        $bar = $this->output->createProgressBar(count($persons) + LdapPlatform::query()->count());

// CONNECTING TO LDAP SERVER

        $ldapConn = ldap_connect(config('bcLDAP.server'), config('bcLDAP.port'));

        // Set some ldap options for talking to AD
        // LDAP_OPT_PROTOCOL_VERSION: LDAP protocol version
        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        // LDAP_OPT_REFERRALS: Specifies whether to automatically follow referrals returned by the LDAP server
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        // Bind as a domain admin
        $ldap_bind = ldap_bind($ldapConn,
            config('bcLDAP.admin-username'),
            config('bcLDAP.admin-password'));
        $allowedSection = (new Section())->whereIn('title', ['bc-Club', 'bc-Café'])->get();

// STARTING THE UPDATE
        $persons->each(function (Person $person) use ($bar, $allowedSection, $ldapConn) {
            $user = $person->user;

            if (!$user) {
                $user = User::createFromPerson($person);
            }
            $bar->advance();
            // skip ldap override
            if ($person->prsn_ldap_id == '9999') {
                return;
            }
            if (!$allowedSection->contains($person->club->section())) {
                return;
            }
// AUTHENTICATING BC-CLUB

            // Search for a bc-Club user with the uid number entered
            $search = ldap_search($ldapConn,
                config('bcLDAP.bc-club-ou').
                config('bcLDAP.base-dn'),
                '(uid='.$person->prsn_ldap_id.')');

            $info = ldap_get_entries($ldapConn, $search);

// AUTHENTICATING BC-CAFE

            // If no such user found in the bc-Club - check bc-Café next.
            if ($info['count'] === 0) {

                // Search for a Café-user with the uid number entered
                $search = ldap_search($ldapConn,
                    config('bcLDAP.bc-cafe-ou').
                    config('bcLDAP.base-dn'),
                    '(uid='.$person->prsn_ldap_id.')');

                $info = ldap_get_entries($ldapConn, $search);

            }

// HANDLING ERRORS

            // If no match found in all clubs - log an error
            if ($info['count'] === 0) {
                Log::info('LDAP sync error: could not authenticate '.$person->prsn_ldap_id.' in LDAP!');
            }

// GETTING USER CREDENTIALS

            // Get user nickname if it exists or first name instead
            $userName = (!empty($info[0]['mozillanickname'][0])) ?
                $info[0]['mozillanickname'][0] :
                $info[0]['givenname'][0];

            // Get user active status
            $userStatus = $info[0]['ilscstate'][0];
            if ($userStatus == "resigned") {
                $userStatus = "ex-member";
            }

            if ($userStatus == "guest") {
                $userStatus = "ex-candidate";
            }

            if (array_key_exists('mail', $info[0])) {
                $userEmail = $info[0]['mail'][0];
            }

            $userGivenName = $info[0]['givenname'][0];
            $userLastName = $info[0]['sn'][0];

// UPDATE AND SAVE CHANGES

            if ($person->prsn_name !== $userName) {
                Log::info('LDAP sync: Changing '.$person->prsn_name." (".$person->prsn_ldap_id.") name from ".$person->prsn_name." to ".$userName.'.');

                $person->prsn_name = $userName;
                $user->name = $userName;
            }


            if ($person->prsn_status !== $userStatus) {
                Log::info('LDAP sync: Changing '.$person->prsn_name." (".$person->prsn_ldap_id.") status from ".$person->prsn_status." to ".$userStatus.'.');

                $person->prsn_status = $userStatus;
                $user->status = $userStatus;
            }

            if (isset($userEmail) && $userEmail != $user->email) {
                if (!User::query()->where('email', '=', $userEmail)->where('id', '<>', $user->id)->exists()) {
                    $user->email = $userEmail;
                }
                {
                    $this->info($person->prsn_ldap_id." ignoring email ".$userEmail." because someone else already use it");
                    Log::warning($person->prsn_ldap_id." ignoring email ".$userEmail." because someone else already use it");
                }
            }
            $user->givenname = $userGivenName;
            $user->lastname = $userLastName;

            try {
                $person->save();
                $user->save();
            } catch (\Exception $e) {
                Log::error('cannot update person '.$person->prsn_ldap_id, [$e]);
                $this->error('cannot update person '.$person->prsn_ldap_id, $e->getMessage());
            }
        });

        foreach (User::all() as $user) {
            /** @var Person $person */
            $person = $user->person;
            $person->prsn_name = $user->name;
            $person->prsn_status = $user->status;
            $person->save();
        }
// FINISH UPDATE

        ldap_unbind($ldapConn);

        $userIds = LdapPlatform::query()->select('user_id')->distinct()->get();
        foreach ($userIds as $userId) {
            $entry = [];
            $entryNames = LdapPlatform::query()->select('entry_name')->where('user_id', '=',
                $userId->user_id)->distinct()->get();
            foreach ($entryNames as $entryName) {
                $bar->advance();
                /** @var LdapPlatform $ldapPlattform */
                $ldapPlattform = LdapPlatform::query()->where('user_id', '=', $userId->user_id)
                    ->where('entry_name', '=', $entryName->entry_name)
                    ->orderByDesc('created_at')->first();
                //remove older entrys, we only apply the newest one
                LdapPlatform::query()->where('user_id', '=', $userId->user_id)
                    ->where('entry_name', '=', $entryName->entry_name)
                    ->where('id', '<>', $ldapPlattform->id)
                    ->delete();
                $entry[$ldapPlattform->entry_name] = $ldapPlattform->entry_value;
            }
            if (LdapUtility::modify($userId->user_id, $entry)) {
                LdapPlatform::query()->where('user_id', '=', $userId->user_id)->delete();
            }
            Log::info("LDAP sync: Changing ".$userId->user_id." ".implode(', ', $entry));
        }

        $bar->finish();

        $counterEnd = microtime(true);

        // report update time
        $this->info('');        // Linebreak
        $this->info('');        // Linebreak
        $this->info('Finished LDAP sync after '.($counterEnd - $counterStart).' seconds.');
        Log::info('Finished LDAP sync after '.($counterEnd - $counterStart).' seconds.');
    }
}
