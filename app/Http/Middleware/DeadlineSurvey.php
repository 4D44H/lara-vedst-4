<?php

namespace Lara\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Lara\Http\Controllers\SurveyController;
use Lara\utilities\RoleUtility;
use Lara\Survey;

class DeadlineSurvey
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
        $surveyId = intval($request->route()->parameter('survey'));
        /** @var Survey $survey */
        $survey = Survey::query()->findOrFail($surveyId);
        $deadlineNotPassed = Carbon::now() < Carbon::parse($survey->deadline);
        $authenicated = \Auth::check();
        $isAdmin = $authenicated && \Auth::user()->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR);
        $hasExtendedRoles = $authenicated && \Auth::user()->hasPermissionsInSection($survey->section(),RoleUtility::PRIVILEGE_CL,RoleUtility::PRIVILEGE_MARKETING);
        if($deadlineNotPassed || $isAdmin || $hasExtendedRoles) {
            return $next($request);
        } else {
            return $this->surveyRequestNotPermitted($request, $survey);
        }
        /*

        if(Carbon::now() < Carbon::parse($survey->deadline) && \Auth::check()
            && (\Auth::user()->isAn(RoleUtility::PRIVILEGE_ADMINISTRATOR)
            || \Auth::user()->hasPermissionsInSection($survey->section(),RoleUtility::PRIVILEGE_CL,RoleUtility::PRIVILEGE_MARKETING))) {
            return $next($request);
        } else {
            return $this->surveyRequestNotPermitted($request, $survey);
        } */
    }

    /**
     * @param $request
     * @param $survey
     * @return \Illuminate\Http\RedirectResponse
     */
    private function surveyRequestNotPermitted($request, $survey)
    {
        $request->session()->put('message',
            'Die Deadline ist überschritten, jetzt können nurnoch Clubleitung/Marketing/Admin die Umfrage ausfüllen');
        $request->session()->put('msgType', 'danger');

        return redirect()->action([SurveyController::class, 'show'], [$survey->id]);
    }
}
