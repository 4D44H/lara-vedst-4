<?php

namespace Lara\Http\Middleware;

use Closure;
use Auth;
use Lara\Utilities;
use Lara\utilities\RoleUtility;


class ManagingUsersOnly
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

        if (!Auth::user()->isAn(RoleUtility::PRIVILEGE_MARKETING, RoleUtility::PRIVILEGE_ADMINISTRATOR)) {
            Utilities::error(trans('auth.missingPermissions'));
            return redirect('/');
        }

        return $next($request);
    }
}
