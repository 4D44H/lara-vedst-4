<?php
/**
 * User: fabian
 * Date: 30.12.18
 * Time: 02:57
 */

namespace Lara\Http\Controllers;


use Illuminate\Validation\Rule;
use Lara\Settings;
use Lara\Utilities;

class ViewModeController extends Controller
{
    public function switchMode($mode)
    {
        $validator = \Validator::make(['mode' => $mode], ['mode' => ['required', Rule::in('dark', 'light')]]);
        if ($validator->failed()) {
            Utilities::error(trans('mainLang.error'));
            
            return redirect()->back();
        }
        $user = \Auth::user();
        if ($user) {
            $userSettings = $user->settings;
            if (!isset($userSettings) && is_null($userSettings)) {
                $userSettings = new Settings();
                $userSettings->user_id = $user->id;
            }
            $userSettings->view_mode = $mode;
            $userSettings->save();
        }
        session()->put('view_mode', $mode);
        
        return redirect()->back();
    }
}
