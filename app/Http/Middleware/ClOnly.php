<?php

namespace Lara\Http\Middleware;

use Closure;
use Lara\Utilities;
use Auth;
use Lara\utilities\RoleUtility;

class ClOnly
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
        if (!Auth::check()) {
            Utilities::error(trans('auth.notAuthenticated'));
            return redirect('/');
        }

        if (!Auth::user()->isAn(RoleUtility::PRIVILEGE_CL,RoleUtility::PRIVILEGE_ADMINISTRATOR)) {
            Utilities::error(trans('auth.missingPermissions'));
            return redirect('/');
        }

        return $next($request);
    }
}
