<?php

namespace Flysap\Administrator;

use Illuminate\Support\ServiceProvider;

class FlysapServiceProvider extends ServiceProvider {

    public function boot() {

        /** Register routes . */
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes.php';
        }


        /** Register view . */
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'administrator');

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/administrator'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // TODO: Implement register() method.
    }
}