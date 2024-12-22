<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Lara\User;
use Lara\Person;
use Lara\Section;

class AddInitialBdUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $allBDusers = config('bd_users.initial');

        if ($allBDusers) {
            // creating the persons is tricky. The 'max' query is probably cached, thus we need to manually
            // track the ldap_ids we are handing out. Otherwise many users end up with the same ldap_id
            $ldap_id = DB::table('persons')->select(DB::raw('max(convert( prsn_ldap_id, unsigned integer)) ldap_id'))->first()->ldap_id +1;
            foreach($allBDusers as $user) {
                $user["section"] = Section::query()->where('title', 'bd-Club')->first()->id;
                $user['prsn_ldap_id'] = strval($ldap_id++);
                User::createNew($user);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
