<?php

namespace App\Providers;

use App\Helpers\Helper;
use App\Models\Workspace;
use Illuminate\Support\ServiceProvider;
use Blade;
use URL;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('pushonce', function ($expression) {
            $isDisplayed = '__pushonce_' . trim(substr($expression, 1, -1));
            return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush({$expression}); ?>";
        });

        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });

        View::share('appTitle', config('app.name'));

        /**
         * @link https://stackoverflow.com/questions/29549660/get-laravel-5-controller-name-in-view#answer-29549985
         */
        app('view')->composer('*', function ($view) {
            $guard = 'web';
            $auth = null;
            $tmpUser = null;
            $tmpWorkspace = null;
            $activeWorkspace = config('workspace.active');
            $webWorkspace = null;

            if(!empty(app('request')) && !empty(app('request')->route()) && !empty(app('request')->route()->computedMiddleware)) {
                $middlewares = app('request')->route()->computedMiddleware;

                if(!empty($middlewares)) {
                    if(in_array(config("module.backend"), $middlewares)) {
                        $guard = config("module.backend");
                    } elseif (in_array(config("module.manager"), $middlewares)) {
                        $guard = config("module.manager");
                    } else {
                        $guard = 'web';
                    }
                }
            }

            $auth = session('current_auth');
            $tmpUser = session('auth_temp');
            $tmpWorkspace = session('tmp_workspace');

            if(empty($auth)) {
                $auth = auth($guard)->user();
            }

            if (empty($tmpWorkspace) && !empty($tmpUser) && $guard == 'manager') {
                $tmpWorkspace = Workspace::with('workspaceExtras')->find($tmpUser->workspace->id);
            }

            $domain = parse_url(config('app.url'), PHP_URL_HOST);
            $host = $this->app->request->server->all()["HTTP_HOST"];
            $workspaceSlug = Helper::getSubDomainOfRequest($host);

            if ($guard == 'web') {
                $webWorkspace = session('workspace_'.$workspaceSlug);
            }

            $view->with(compact('auth', 'guard', 'activeWorkspace', 'tmpUser', 'tmpWorkspace', 'webWorkspace', 'domain', 'workspaceSlug'));
        });

        app('view')->composer('layouts.admin', function ($view) {
            $this->bootBaseParams($view);
        });

        app('view')->composer('layouts.manager', function ($view) {
            $this->bootBaseParams($view);
        });

        app('view')->composer('layouts.web', function ($view) {
            $this->bootBaseParams($view);
        });

        /*-------------------- DEBUG --------------------*/

        if (config('app.debug')) {
            // Debug query in a request
            \DB::listen(function ($query) {
                // Way 1: log tracking query
                if (config('app.debug_dump_query_tracking')) {
                    $output = [$query->sql, $query->bindings, $query->time];
                    file_put_contents(storage_path('logs/dump_db_queries.txt'),
                        var_export($output, true)
                        . PHP_EOL . '--------------------------------------------------------' . PHP_EOL,
                        FILE_APPEND);
                }

                // Way 2: log query string
                if (config('app.debug_dump_query_string')) {
                    $output = str_replace_array('?', $query->bindings, str_replace('?', "'?'", $query->sql)) . ';';
                    file_put_contents(storage_path('logs/dump_db_queries.sql'),
                        $output
                        . PHP_EOL,
                        FILE_APPEND);
                }

            });
        }

        /*-------------------- /DEBUG --------------------*/

    }

    public function bootBaseParams($view) {
        $request = app('request');
        $route = $request->route();
        $routeName = $route->getName();
        $prefix = $route->getPrefix();
        $action = $route->getAction();
        $params = $route->parameters();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);
        $baseUrl = URL::to('/') . '/';
        $view->with(compact('prefix', 'controller', 'action', 'params', 'baseUrl', 'routeName', 'request'));
    }
}
