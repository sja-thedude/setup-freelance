<?php

namespace App\Providers;

use Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider as ParentServiceProvider;

/**
 * The LaravelJsLocalizationServiceProvider class.
 *
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LaravelJsLocalizationServiceProvider extends ParentServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        // Bind the Laravel JS Localization command into the app IOC.
        $this->app->singleton('localization.js', function ($app) {
            $app = $this->app;
            $laravelMajorVersion = (int) $app::VERSION;

            $files = $app['files'];

            if ($laravelMajorVersion === 4) {
                $langs = $app['path.base'].'/app/lang';
            } elseif ($laravelMajorVersion >= 5) {
                $langs = $app['path.base'].'/resources/lang';
            }
            $messages = $app['config']->get('localization-js.messages');
            $generator = new \App\Console\Commands\LangJsGenerator($files, $langs, $messages);

            return new \App\Console\Commands\LangJsCommand($generator);
        });

        // Bind the Laravel JS Localization command into Laravel Artisan.
        $this->commands('localization.js');
    }
}
