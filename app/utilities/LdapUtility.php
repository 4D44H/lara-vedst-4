<?php
/**
 * Created by IntelliJ IDEA.
 * User: fabian
 * Date: 04.04.18
 * Time: 12:32
 */

namespace Lara\utilities;


use Illuminate\Support\Facades\Config;

class LdapUtility
{

    const OUS = ['bc-club-ou', 'bc-cafe-ou'];

    /**
     * @return resource
     */
    public static function connect()
    {
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

        return $ldapConn;
    }

    /**
     * @param $ldapConn
     * @param $userId
     * @return bool|resource
     */
    public static function getEntry($ldapConn, $userId)
    {
        foreach (self::OUS as $ou) {
            // Search for a bc-Club user with the uid number entered
            $search = ldap_search($ldapConn,
                config('bcLDAP.' . $ou) .
                config('bcLDAP.base-dn'),
                '(uid=' . $userId . ')');
            $userEntry = ldap_first_entry($ldapConn, $search);
            if ($userEntry !== false) {
                return $userEntry;
            }
        }
        return false;
    }

    /**
     * @param $userId
     * @param $encoded_newPassword
     * @return bool
     */
    public static function changePassword($userId, $encoded_newPassword)
    {
        $entry = array();
        $entry["userpassword"] = "$encoded_newPassword";

        return self::modify($userId, $entry);
    }

    /**
     * @param $userId
     * @param array $entry
     * @return bool
     */
    public static function modify($userId, array $entry)
    {
        $ldapConn = self::connect();
        $userEntry = self::getEntry($ldapConn, $userId);
        if ($userEntry === false) {
            self::disconnect($ldapConn);
            return false;
        }
        $userDn = ldap_get_dn($ldapConn, $userEntry);

        if (!ldap_modify($ldapConn, $userDn, $entry)) {
            \Log::error("ldap change not worked");
            self::disconnect($ldapConn);
            return false;
        }
        self::disconnect($ldapConn);
        return true;
    }

    public static function disconnect($ldapConn)
    {
        ldap_unbind($ldapConn);
    }
}
