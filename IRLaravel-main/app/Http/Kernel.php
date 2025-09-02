<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Class Kernel
 *
 * @package App\Http
 * Laravel 5.5 Model binding in routes doesn't work
 * When upgraded from 5.2 up to ... some version
 * Laravel 5.3 uses the SubstitueBindings middleware to do the implicit and explicit bindings,
 * it is no longer done via the router before the middleware stack.
 * @link    https://stackoverflow.com/a/47784205/10174865
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\Language::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:700,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                        => \App\Http\Middleware\Authenticate::class,
        'auth.basic'                  => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'                    => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'                         => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'                       => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'                    => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'admin'                       => \App\Http\Middleware\Admin::class,
        'manager'                     => \App\Http\Middleware\Manager::class,
        'manager.expire'              => \App\Http\Middleware\ManagerExpire::class,
        'domain'                      => \App\Http\Middleware\Domain::class,
        'cors'                        => \App\Http\Middleware\Cors::class,
        'jwt.auth'                    => \App\Http\Middleware\AuthJWT::class,
        'jwt.refresh'                 => \Tymon\JWTAuth\Middleware\RefreshToken::class,
        'handleCors'                  => \Barryvdh\Cors\HandleCors::class,
        'nonPost'                     => \App\Http\Middleware\ParseMultipartFormDataInputForNonPostRequests::class,
        /* Locale from Header of request */
        'localization'                => \App\Http\Middleware\Localization::class,
        /* REDIRECTION MIDDLEWARE */
        'localize'                    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
        'localizationRedirect'        => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        'localeSessionRedirect'       => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        'localeViewPath'              => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        'accessGroupManager'          => \App\Http\Middleware\RedirectIfSettingAccessGroup::class,
        'accessKlantenkaartenManager' => \App\Http\Middleware\RedirectIfSettingAccessKlantenkaarten::class,
        'accessNotificationManager'   => \App\Http\Middleware\RedirectIfSettingAccessNotification::class,
        'connector'                   => \App\Http\Middleware\Connector::class,
    ];
}
