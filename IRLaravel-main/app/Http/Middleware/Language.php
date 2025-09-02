<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

/**
 * Class Language
 * @package App\Http\Middleware
 */
class Language
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $languages = Helper::getActiveLanguages();

        // If the locale has not been passed through the function
        // it tries to get it from the first segment of the url
        $locale = $request->segment(1);

        if (!empty($locale) && array_key_exists($locale, $languages)) {
            // Next if locale in URL
            // See the package: mcamara/laravel-localization
            return $next($request);
        }

        if (Session::has('locale') && array_key_exists(Session::get('locale'), $languages)) {
            App::setLocale(Session::get('locale'));
        } else {
            // This is optional as Laravel will automatically set the fallback language if there is none specified
            App::setLocale(Config::get('app.fallback_locale'));
        }

        return $next($request);
    }
}