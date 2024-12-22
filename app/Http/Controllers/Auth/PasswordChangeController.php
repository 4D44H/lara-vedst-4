<?php

namespace Lara\Http\Controllers\Auth;

use Auth;
use Hash;
use Input;
use Lara\Http\Controllers\Controller;
use Lara\LdapPlatform;
use Lara\User;
use Lara\Utilities;
use Lara\utilities\LdapUtility;
use Request;
use Validator;
use View;

class PasswordChangeController extends Controller
{
    public function showChangePasswordForm()
    {
        return View::make('auth.passwords.change');
    }

    public function changePassword()
    {
        /** @var Request $request */
        $request=request();
        // Get old password and check that it matches the one stored in the DB
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $oldPassword = $request->input('old-password');

        $user = Auth::user();

        $isPasswordCorrect = Hash::check($oldPassword, $user->password);

        if (!$isPasswordCorrect) {
            Utilities::error(trans('auth.passwordDoesNotMatch'));
            return back();
        }

        $newPassword = $request->input('password');
        $user->password = bcrypt($newPassword);
        $user->save();
/*
        if (!\App::environment('development') && strpos(env('LDAP_SECTIONS', ''), $user->section->title) !== false) {
            try {
                $this->changePasswordInLDAP($user);
            } catch (\Exception $e) {
                \Log::error("ldap broken", [$e]);
            }
        }
*/
        Utilities::success(trans('auth.passwordChanged'));

        return redirect('/');
    }

    public function validator($data)
    {
        return Validator::make($data, [
            'old-password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);
    }

    /**
     * @param $user
     */
    private function changePasswordInLDAP(User $user)
    {
        $encoded_newPassword = '{md5}' . base64_encode(mhash(MHASH_MD5, $request->input('password')));
        $ldapPlatform = new LdapPlatform();
        $ldapPlatform->entry_name = 'userpassword';
        $ldapPlatform->entry_value = $encoded_newPassword;
        $ldapPlatform->user_id = $user->person->prsn_ldap_id;
        $ldapPlatform->save();
        if(LdapUtility::changePassword($user->person->prsn_ldap_id, $encoded_newPassword)) {
            try {
                $ldapPlatform->delete();
            } catch (\Exception $e) {
                \Log::error('could not delete ldapplattform',[$e]);
            }
        }
    }
}
