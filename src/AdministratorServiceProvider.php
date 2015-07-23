<?php

namespace Flysap\Administrator;

use Flysap\Administrator\Contracts\AdministratorServiceContract;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;

class AdministratorServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadRoutes()
            ->loadConfiguration()
            ->loadViews();

        /** On bootstrap set framework active . */
        app(AdministratorServiceContract::class)
            ->setFramework(
                config('administrator::active_framework')
            );

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        /** Register administrator service .. */
        $this->app->singleton(AdministratorServiceContract::class, function($app) {
            return new AdministratorService(
                $app['module-caching']
            );
        });

        /** Register administrator menu .. */
        $this->app->singleton('menu-manager', function($app) {
            return new MenuManager(
                $app['module-caching']
            );
        });
    }

    /**
     * Load routes .
     *
     * @return $this
     */
    protected function loadRoutes() {
        /** Register routes . */
        if (! $this->app->routesAreCached())
            require __DIR__.'/../routes.php';

        return $this;
    }

    /**
     * Load configuration .
     *
     * @return $this
     */
    protected function loadConfiguration() {
        $array = Yaml::parse(file_get_contents(
            __DIR__ . '/../configuration/general.yaml'
        ));

        $config = $this->app['config']->get('administrator', []);

        $this->app['config']->set('administrator', array_merge($array, $config));

        return $this;
    }

    /**
     * Load views .
     *
     * @return $this
     */
    protected function loadViews() {
        $this->loadViewsFrom(__DIR__ . '/../views', 'administrator');

        $this->publishes([
            __DIR__ . '/../views' => base_path('resources/views/vendor/administrator'),
        ]);

        return $this;
    }
}