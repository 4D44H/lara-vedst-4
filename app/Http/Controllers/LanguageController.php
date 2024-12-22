<?php

namespace Lara\Http\Controllers;


use Auth;

use Lara\Settings;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        if (array_key_exists($lang, config('languages'))) {
            session(['language' => $lang]);

            $user = Auth::user();
            if ($user) {
                $userSettings = $user->settings;

                if (!isset($userSettings)) {
                    $userSettings = new Settings();
                }

                $userSettings->language = ''.$lang;
                $userSettings->user_id = $user->id;
                $userSettings->save();
            }
        }

        return back();
    }
}
