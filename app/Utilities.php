<?php


namespace Lara;

use Auth;
use Illuminate\Support\Facades\Cache;
use Lara\Http\Controllers\IcalController;



class Utilities
{
    static function surroundLinksWithTags($text)
    {
        $urlMatching = '$((http(s)?:\/\/)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*))$';

        return preg_replace_callback($urlMatching,
            function ($match) {
                $link = $match[0];
                // if the protocol is missing, we have to add it. Assume http in this case
                if ($match[2] !== 'http://' && $match[2] !== 'https://') {
                    $link = 'http://'.$link;
                }

                return sprintf('<a href="%s" target="_blank"> %s </a>', $link, $match[0]);
            }, $text);
    }

    static function getAllCachedIcalKeys()
    {
        return Cache::get(IcalController::ICAL_ACCESSOR, []);
    }

    static function clearIcalCache()
    {
        $keys = self::getAllCachedIcalKeys();
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget(IcalController::ICAL_ACCESSOR);
    }

    /** checks if a user has the permission to do what he want
     * @param string | array $permissions the permissions the user is needing
     * @return boolean true or false
     */
    static function requirePermission(...$permissions)
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->isAn(...$permissions);
    }

    static function getEventTypeTranslation($typeNumber)
    {
        switch ($typeNumber) {
            case "0":
                return trans('mainLang.normalProgramm');
            case "2":
                return trans('mainLang.special');
            case "3":
                return trans('mainLang.LiveBandDJ');
            case "5":
                return trans('mainLang.utilization');
            case "4":
                return trans('mainLang.internalEvent');
            case "6":
                return trans('mainLang.flooding');
            case "7":
                return trans('mainLang.flyersPlacard');
            case "8":
                return trans('mainLang.preSale');
            case "9":
                return trans('mainLang.others');
            case "10":
                return trans('mainLang.outsideEvent');
            case "11":
                return trans('mainLang.buffet');
            case "1":
                return trans('mainLang.information');
            default :
                return trans('mainLang.others');
        }
    }

    static function error($message)
    {
        if (!session()->has('message')) {
            session(['message' => $message, 'msgType' => 'danger']);
        }
    }

    static function success($message)
    {
        if (!session()->has('message')) {
            session(['message'=> $message, 'msgType' => 'success']);
        }
    }
}
