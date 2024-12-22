<?php

namespace Lara\Http\Middleware;

use Closure;
use Lara\Utilities;
use Lara\utilities\RoleUtility;
use Auth;

class AdminOnly
{
    /**
     * Deny access for guest and users without admin privileges
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            Utilities::error(__('auth.notAuthenticated'));
            return redirect('/');
        }

        if (!Auth::user()->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR)) {
            Utilities::error(__('auth.missingPermissions'));
            return redirect('/');
        }

        return $next($request);
    }
}
