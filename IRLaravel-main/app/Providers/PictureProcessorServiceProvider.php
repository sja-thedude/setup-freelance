<?php

namespace App\Providers;
use App\Helpers\PictureProcessor;
use Illuminate\Support\ServiceProvider;

/**
 * Class PictureProcessorServiceProvider
 * @package App\Providers
 */
class PictureProcessorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Helper::class, function ($app) {
            return new PictureProcessor();
        });
    }
}
