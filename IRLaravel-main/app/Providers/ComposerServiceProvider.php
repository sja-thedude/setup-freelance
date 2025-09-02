<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer([
            'web.carts.index',
            'web.partials.bottom_menu',
            'web.carts.partials.modal-product-suggestion'
        ], 'App\Http\ViewComposers\CartComposer');

        view()->composer([
            'web.home.footer'
        ], 'App\Http\ViewComposers\PortalWebsiteComposer');
    }
}
