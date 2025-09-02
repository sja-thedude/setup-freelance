<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

/**
 * Class LanguageController
 * @package App\Http\Controllers
 *
 * @link https://gist.github.com/huiralb/30b7e20d4e845e897fa8#file-languagemiddleware-php-L24
 */
class LanguageController extends Controller
{
    /**
     * @param Request $request
     * @param string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang(Request $request, $locale)
    {
        $languages = Helper::getActiveLanguages();

        // Validate lang
        if (array_key_exists($locale, $languages)) {
            Session::put('locale', $locale);

            // Get default guard
            $guard = Auth::guard();

            if (empty($guard) || empty($guard->user())) {
                // If default guard not available, get "client" guard
                $guard = Auth::guard('manager');

                if (empty($guard) || empty($guard->user())) {
                    // If default guard not available, get "admin" guard
                    $guard = Auth::guard('admin');
                }
            }
        }

        // Redirect back URL
        $referer = $request->server('HTTP_REFERER');
        $path = trim(str_replace(url('/'), '', $referer), '/');
        $params = explode('/', $path);

        // Check if has locale string in URL, we will replace to new locale for this URL
        // and redirect to new URL with locale
        // Copy from \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect handle
        if (count($params) > 0 && app('laravellocalization')->checkLocaleInSupportedLocales($params[0])) {
            if ($locale && app('laravellocalization')->checkLocaleInSupportedLocales($locale)
                && !(app('laravellocalization')->getDefaultLocale() === $locale && app('laravellocalization')->hideDefaultLocaleInURL())) {
                // app('session')->reflash();
                $redirection = app('laravellocalization')->getLocalizedURL($locale, $referer);

                return new RedirectResponse($redirection, 302, ['Vary' => 'Accept-Language']);
            }
        }

        return Redirect::back();
    }

}