<?php

namespace Lara\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('language') && array_key_exists(session('language'), config('languages'))) {
            App::setLocale(session('language'));
        } else {
            //Detect Browser Prefered language
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE', "de"), 0, 2);
            if (isset($locale) && array_key_exists($locale, config('languages'))) {
                App::setLocale($locale);
                session(['language' => $locale]);
            } else {
                //Optional as Laravel will automatically set the fallback language if there is none specified
                App::setLocale(config('app.locale'));
            }
        }
        
        return $next($request);
    }
}
