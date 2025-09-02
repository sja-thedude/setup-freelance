<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $namespaceBackend = 'App\Http\Controllers\Backend';
    protected $namespaceManager = 'App\Http\Controllers\Manager';
    protected $namespaceAPI = 'App\Http\Controllers\API';

    /**
     * Will make sure that the required modules have been fully loaded
     * @return void
     */
    public $admin;
    public $manager;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // For backend
        $this->admin = config("module.backend");
        $this->manager = config("module.manager");

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapBackendRoutes($router);
        $this->mapManagerRoutes($router);
        $this->mapAPIRoutes($router);
        $this->mapWebRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => 'web',
        ], function ($router) {
            require app_path('Http/routes.php');
        });
    }

    /**
     * Define the "backend" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapBackendRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespaceBackend,
            'prefix' => LaravelLocalization::setLocale() . '/' . $this->admin,
            'middleware' => ['web', 'admin', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
        ], function () {
            require app_path('Http/routes/backend.php');
        });
    }

    /**
     * Define the "frontend" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapManagerRoutes(Router $router) {
        $router->group([
            'namespace' => $this->namespaceManager,
            'prefix' => LaravelLocalization::setLocale() . '/' . $this->manager,
            'middleware' => ['web', 'manager', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
        ], function () {
            require app_path('Http/routes/manager.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapAPIRoutes(Router $router)
    {
        $router->group([
            'namespace' => $this->namespaceAPI
        ], function () {
            require app_path('Http/routes/api.php');
        });
    }

}
