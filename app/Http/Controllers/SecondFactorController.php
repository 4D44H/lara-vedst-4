<?php

namespace Lara\Http\Controllers;


use Illuminate\Http\RedirectResponse;
use Lara\Http\Middleware\RejectGuests;

use PragmaRX\Google2FA\Google2FA;

class SecondFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware(RejectGuests::class);
    }

    public function index()
    {
        return \View::make('secondfactor.index');
    }

    /** verifys current code
     *
     * @return RedirectResponse
     */
    public function verify()
    {
        $request=request();
        $validator = \Validator::make($request->all(), [
            'code' => 'required|digits:6'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }
        $targeturl = session('targeturl', "/");
        if (is_array($targeturl)) {
            $targeturl = $targeturl[0];
        }
        /** @var Google2FA $google2fa */
        $google2fa = app('pragmarx.google2fa');
        $secret = \Auth::user()->google2fa_secret;
        try {
            if ($google2fa->verify($request->input("code"), $secret)) {
                session()->forget('targeturl');
                session()->put('2faVeryfied', true);
                return redirect($targeturl);
            } else {
                return back()->withErrors(['code' => 'invalid code']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }
    }

}
