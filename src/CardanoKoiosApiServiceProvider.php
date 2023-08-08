<?php

namespace CardanoBlockhouse\CardanoKoiosApi;

use Illuminate\Support\ServiceProvider;

class CardanoKoiosApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->singleton('KoiosApi', function ($app) {
            return new KoiosApi();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
