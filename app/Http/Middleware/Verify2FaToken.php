<?php

namespace Lara\Http\Middleware;

use Closure;

class Verify2FaToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!($request->is('2fa') or $request->is('lang*'))) {
            if (\Auth::check()) {
                if (!empty(\Auth::user()->google2fa_secret ) and !session("2faVeryfied", false)) {
                    session()->push('targeturl', $request->url());
                    return \Redirect::route('lara.2fa');
                }
            }
        }

        return $next($request);
    }
}
