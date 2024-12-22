<?php

namespace Lara\Http\Middleware;

use Closure;
use Lara\Http\Controllers\MonthController;
use Lara\Survey;
use Lara\utilities\RoleUtility;
use PhpParser\Node\Scalar\String_;

class Creator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $classname
     * @return mixed
     */
    public function handle($request, Closure $next, $classpath, $routeParameterName)
    {
        $newObject = new $classpath();
        $object = $newObject->findOrFail($request->route()->parameter($routeParameterName))->first();
        $authenicated = \Auth::check();
        $personId = $authenicated ? \Auth::user()->person->prsn_ldap_id : null;
        $isAdmin = $authenicated && \Auth::user()->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR);
        $hasExtendedRoles = $authenicated && \Auth::user()->hasPermissionsInSection($object->section(),RoleUtility::PRIVILEGE_CL,RoleUtility::PRIVILEGE_MARKETING);
        if($object->creator_id == $personId
            || $isAdmin || $hasExtendedRoles
            ) {
            return $next($request);
        } else {
            $request->session()->put('message', 'Dir fehlt die nötige Berechtigung!');
            $request->session()->put('msgType', 'danger');
            return redirect()->action([MonthController::class, 'currentMonth']);
        }
    }
}
