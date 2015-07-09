<?php

namespace Flysap\Administrator;

use Illuminate\Support\ServiceProvider;

class FlysapServiceProvider extends ServiceProvider {

    public function boot() {
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