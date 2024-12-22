<?php

namespace Lara\Http\Middleware;

use Closure;
use Auth;
use Lara\Utilities;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$params)
    {
        if (!Auth::check()) {
            Utilities::error(trans('auth.notAuthenticated'));
            return redirect('/');
        }

        $hasRole = Auth::user()->roles()->whereIn('name',$params)->exists();

        if (!$hasRole) {
            Utilities::error(trans('auth.missingPermissions'));
            return redirect('/');
        }

        return $next($request);
    }
}
