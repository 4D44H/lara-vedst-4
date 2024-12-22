<?php

namespace Lara\Http\Middleware;

use Closure;
use Auth;
use Lara\Utilities;

class RejectGuests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }
        Utilities::error(__('auth.notAuthenticated'));
        return redirect('/');
    }
}
