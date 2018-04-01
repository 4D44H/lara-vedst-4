<?php

namespace Lara\Http\Controllers;

use Config;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Input;

use Lara\Club;
use Lara\Person;
use Lara\Settings;
use Lara\Section;
use Lara\User;
use Lara\utilities\RoleUtility;

use Log;
use Redirect;
use Session;

/*
--------------------------------------------------------------------------
    Copyright (C) 2015  Maxim Drachinskiy
                        Silvi Kaltwasser
                        Nadine Sobisch
                        Robert Utnehmer

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details (app/LICENSE.txt).

    Any questions? Mailto: maxim.drachinskiy@bc-studentenclub.de
--------------------------------------------------------------------------
*/

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Logout current user, delete relevant session data.
     *
     * @return RedirectResponse
     */
    public function doLogout()
    {
        Session::flush();
        if (Auth::user()) {
            return $this->logout();
        }
        return Redirect::to('/');
    }

    /**
     * ############################################
     * #             bc-LDAP Server               #
     * ############################################
     *
     *        ONLY accessible from bc-Club
     *        and with a valid config file.
     *
     */

    /**
     * Authenticates a user and saves credentials with Laravels native "AuthenticatesUsers" trait.
     * First we check whether the user has an account in Lara by either matching the given login
     * to a username or email stored in the users table.
     *
     * If this failes, we try to authenticate against the bcLDAP.
     *
     * CONFIG is stored in \app\config\bcLDAP.php
     * For the purpose of securing personal data of club members
     * this config will not be shared via git.
     * Ask the current maintainers for a working copy if you absolutely need to use bc-LDAP.
     *
     * Connects to bcLDAP server using data from config.
     *
     * Binds as "replicator" (read-only rights) and searches for a user with uid that matches input.
     * If found, compares that user's password with input.
     * On success returns relevant infos in session data.
     * Informs user about errors or success.
     *
     * ToDo: 'No LDAP-Link' message for lost connection.
     *
     * @param  string $userName (as form input) can be either the name of a Lara user
     *                          an email of a lara user or an LDAP id of the bc-LDAP
     * @param  string $password (as form input)
     *
     * @return RedirectResponse
     */
    public function doLogin()
    {
        $someLoginWorked = $this->attemtLoginViaDevelop() || $this->attemptLoginWithCredentials(request(), 'name')
            || $this->attemptLoginWithCredentials(request(), 'email')
            || $this->attemptLoginViaLDAP();

        if ($someLoginWorked) {
            $user = Auth::user();
            if (!$user) {
                return;
            }

            $userSettings = Settings::where('userId','=', $user->person->prsn_ldap_id)->first();
            if ($userSettings) {
                Session::put('applocale', $userSettings->language);
            }
            return Redirect::back();
        }
        return $this->loginFailed();
    }

    private function attemtLoginViaDevelop(){
        if (!App::environment('development')) {
            return false;
        }
        $userGroup = request('userGroup');
        /** @var Person $person */
        $clubIdsOfSections = Section::all()->map(function(Section $s) {
            return $s->club()->id;
        });
        $person = Person::whereIn('clb_id', $clubIdsOfSections)->inRandomOrder()->first();
        /** @var User $user */
        $user = $person->user();
        $user->roles()->detach();
        RoleUtility::assignPrivileges($user, $user->section, $userGroup);
        $person->user()->fill(["group" => $userGroup])->save();
        $this->loginPersonAsUser($person);

        return true;
    }

    protected function attemptLoginViaLDAP()
    {
            if (Input::get('username') === "1708") {
                Session::put('message', 'Ne ne ne, nicht mit dieser Clubnummer, sie ist ja nur fur bc-Wiki zu benutzen ;)');
                Session::put('msgType', 'danger');

                Log::warning('bc-Wiki login used (1708), access denied.');

                return false;
            }

            // CONNECTING TO LDAP SERVER
            $ldapConn = ldap_connect(Config::get('bcLDAP.server'), Config::get('bcLDAP.port'));

            // Set some ldap options for talking to AD
            // LDAP_OPT_PROTOCOL_VERSION: LDAP protocol version
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            // LDAP_OPT_REFERRALS: Specifies whether to automatically follow referrals returned by the LDAP server
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

            // Bind as a domain admin
            $ldap_bind = ldap_bind($ldapConn,
                Config::get('bcLDAP.admin-username'),
                Config::get('bcLDAP.admin-password'));


// INPUT VALIDATION AND ERROR HANDLING


            // Request UID if none entered
            if (Input::get('username') === '') {
                ldap_unbind($ldapConn);

                Log::info('Auth fail: empty userID given.');

                return false;
            }

            // Request numeric UID if something else is entered
            if (!is_numeric(Input::get('username'))) {
                ldap_unbind($ldapConn);

                Log::info('Auth fail: not a number given as userID (username: ' . Input::get('username') . ').');

                return false;
            }
// AUTHENTICATING BC-CLUB

            // Search for a bc-Club user with the uid number entered
            $search = ldap_search($ldapConn,
                Config::get('bcLDAP.bc-club-ou') .
                Config::get('bcLDAP.base-dn'),
                '(uid=' . Input::get('username') . ')');

            $info = ldap_get_entries($ldapConn, $search);

            // Set default user access group to "bc-Club member"
            if ($info['count'] != 0) {
                $userGroup = "bc-Club";
            }


// AUTHENTICATING BC-CAFE


            // If no such user found in the Club - check Café next.
            if ($info['count'] === 0) {

                // Search for a Café-user with the uid number entered
                $search = ldap_search($ldapConn,
                    Config::get('bcLDAP.bc-cafe-ou') .
                    Config::get('bcLDAP.base-dn'),
                    '(uid=' . Input::get('username') . ')');

                $info = ldap_get_entries($ldapConn, $search);

                // If found - set user access group to "bc-Café member"
                if ($info['count'] != 0) {
                    $userGroup = "bc-Café";
                }
            }


// HANDLING ERRORS


            // If no match found in all clubs - throw an error and quit
            if ($info['count'] === 0) {
                ldap_unbind($ldapConn);

                Session::put('message', Config::get('messages_de.uid-not-found'));
                Session::put('msgType', 'danger');

                Log::info('Auth fail: wrong userID given (username: ' . Input::get('username') . ').');

                return false;
            }


// SETTING ACCESS GROUP


            // get full user DN
            $userDn = $info[0]['dn'];

            if ($userGroup === "bc-Club") {
                // Check if user has MARKETING rights in bc-CLub
                $searchGroup = ldap_search($ldapConn,
                    Config::get('bcLDAP.bc-club-group-ou') .
                    Config::get('bcLDAP.base-dn'),
                    Config::get('bcLDAP.bc-club-marketing-group'));

                $infoGroup = ldap_get_entries($ldapConn, $searchGroup);

                for ($i = 0; $i < $infoGroup[0]['member']['count']; $i++) {
                    if ($infoGroup[0]['member'][$i] == $userDn) {
                        $userGroup = "marketing";
                    }
                }


                // Check if user has MANAGEMENT rights in bc-CLub
                $searchGroup = ldap_search($ldapConn,
                    Config::get('bcLDAP.bc-club-group-ou') .
                    Config::get('bcLDAP.base-dn'),
                    Config::get('bcLDAP.bc-club-management-group'));

                $infoGroup = ldap_get_entries($ldapConn, $searchGroup);

                for ($i = 0; $i < $infoGroup[0]['member']['count']; $i++) {
                    if ($infoGroup[0]['member'][$i] == $userDn) {
                        $userGroup = "clubleitung";
                    }
                }
            } elseif ($userGroup === "bc-Café") {
                // Check if user has MARKETING rights in bc-Café
                $searchGroup = ldap_search($ldapConn,
                    Config::get('bcLDAP.bc-cafe-group-ou') .
                    Config::get('bcLDAP.base-dn'),
                    Config::get('bcLDAP.bc-cafe-marketing-group'));

                $infoGroup = ldap_get_entries($ldapConn, $searchGroup);

                for ($i = 0; $i < $infoGroup[0]['member']['count']; $i++) {
                    if ($infoGroup[0]['member'][$i] == $userDn) {
                        $userGroup = "marketing";
                    }
                }


                // Check if user has MANAGEMENT rights in bc-Café
                $searchGroup = ldap_search($ldapConn,
                    Config::get('bcLDAP.bc-cafe-group-ou') .
                    Config::get('bcLDAP.base-dn'),
                    Config::get('bcLDAP.bc-cafe-management-group'));

                $infoGroup = ldap_get_entries($ldapConn, $searchGroup);

                for ($i = 0; $i < $infoGroup[0]['member']['count']; $i++) {
                    if ($infoGroup[0]['member'][$i] == $userDn) {
                        $userGroup = "clubleitung";
                    }
                }
            }


// SETTING ADMIN CREDENTIALS


            // Checks if user LDAP ID is among hardcoded admin LDAP IDs from the config file
            if (in_array($info[0]['uidnumber'][0], Config::get('bcLDAP.admin-ldap-id'))) {
                $userGroup = "admin";
            }


// PREPARE USER CREDENTIALS


            // Get user nickname if it exists or first name instead
            $userName = (!empty($info[0]['mozillanickname'][0])) ?
                $info[0]['mozillanickname'][0] :
                $info[0]['givenname'][0];

            // Get user club
            if (substr($info[0]['dn'], 22, -7) === "cafe") {
                $userClub = "bc-Café";
            } elseif (substr($info[0]['dn'], 22, -7) === "bc-club") {
                $userClub = "bc-Club";
            }

            // Get user active status
            $userStatus = $info[0]['ilscstate'][0];


// AUTHENTICATE USER


            // Hashing password input
            $password = '{md5}' . base64_encode(mhash(MHASH_MD5, Input::get('password')));

            // end ldapConnection
            ldap_unbind($ldapConn);

            // Compare password in LDAP with hashed input.
            if ($info[0]['userpassword'][0] === $password) {
                $ldapId = $info[0]['uidnumber'][0];
                $person = Person::where('prsn_ldap_id', $ldapId)->first();
                if (!$person) {
                    $person = Person::create([
                        'prsn_name' => $userName,
                        'prsn_ldap_id' => $ldapId,
                        'prsn_status' => $userStatus,
                        'clb_id' => Club::where('clb_title', $userClub)->first()->id,
                        'prsn_uid' => hash("sha512", uniqid())
                    ]);
                    User::createFromPerson($person);
                }

                Auth::login($person->user());
                $user = $person->user();

                if ($user->email === "") {
                    $user->email = $info[0]['email'][0];
                }

                // this is the internally used hashing
                $user->password = bcrypt(Input::get('password'));
                $user->status = $userStatus;
                $user->save();

                if (in_array($userGroup,RoleUtility::ALL_PRIVILEGES)){
                    RoleUtility::assignPrivileges($user, $user->section()->first(), $userGroup);
                }

                Log::info('Auth success: ' .
                    $info[0]['cn'][0] .
                    ' (' .
                    $info[0]['uidnumber'][0] .
                    ', "' .
                    (!empty($info[0]['mozillanickname'][0]) ? $info[0]['mozillanickname'][0] : $info[0]['givenname'][0]) .
                    '", ' .
                    $userGroup .
                    ') just logged in.');

                return true;
            }

            Log::info('Auth fail: ' . $info[0]['cn'][0] . ' (' . $info[0]['uidnumber'][0] . ', ' . $userGroup . ') used wrong password.');

            return false;
    }

    protected function attemptLoginWithCredentials($request, $userIdentifier = 'email')
    {
        $credentials = [
            $userIdentifier => request('username'),
            'password' => request('password')
        ];
        return $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );
    }

    /**
     * @return mixed
     */
    protected function loginFailed()
    {
        Session::put('message', Config::get('messages_de.login-fail'));
        Session::put('msgType', 'danger');

        return Redirect::back();
    }

    /**
     * @param $ldapId
     */
    protected function loginPersonAsUser(Person $person)
    {
        Auth::login($person->user());
    }

}

/*      This is what the returned bcLDAP object looks like (only useful fields are shown here).

        Array (
            [count] => 1
            [0] => Array (
                [uidnumber] => Array (
                    [count] => 1
                    [0] => 1000 )                               // UID number

                [uid] => Array (
                    [count] => 1
                    [0] => 1000 )                               // Clubnumber

                [cn] => Array (
                    [count] => 1
                    [0] => Dummy Dumminson )                    // Full name

                [userpassword] => Array (
                    [count] => 1
                    [0] => {md5}somethinghashedhere== )         // Hashed password

                [ilscmember] => Array (
                    [count] => 1
                    [0] => 20110110000000Z )                    // Member since 10. Jan 2011

                [sn] => Array (
                    [count] => 1
                    [0] => Dumminson )                          // Last name

                [birthday] => Array (
                    [count] => 1 [0] => 19990101000000Z )       // Birthday

                [givenname] => Array (
                    [count] => 1
                    [0] => Dummy )                              // First name

                [mozillanickname] => Array (
                    [count] => 1
                    [0] => Dummer )                             // Clubname (nickname)

                [mail] => Array (
                    [count] => 1
                    [0] => dummy@mail.com )                     // Email

                [candidate] => Array (
                    [count] => 1
                    [0] => 20101011000000Z )                    // Candidate since 11. Oct 2010

                [ilscstate] => Array (
                    [count] => 1
                    [0] => veteran )                            // Club status (active/candidate/veteran/resigned)

                [dn] => uid=1000,ou=People,ou=bc-club,o=ilsc )  // Full DN
            )

/*      bc-clubcl & bcMarketing Group object:

        array(2) { ["count"]=> int(1)
        [0]=> array(10) {
            ["cn"]=> array(2) {
                ["count"]=> int(1)
                [0]=> string(9) "bc-clubcl"
            }

            ["member"]=> array(6) {
                ["count"]=> int(5)
                [0]=> string(36) "uid=9999,ou=People,ou=bc-club,o=ilsc"
                [1]=> string(36) "uid=9998,ou=People,ou=bc-club,o=ilsc"
                [2]=> string(36) "uid=9997,ou=People,ou=bc-club,o=ilsc"
                [3]=> string(36) "uid=9996,ou=People,ou=bc-club,o=ilsc"
                [4]=> string(36) "uid=9995,ou=People,ou=bc-club,o=ilsc"
            }

*/

