<?php

namespace Flysap\Application;

use Flysap\ModuleManager\ModuleServiceProvider;
use Flysap\ThemeManager\ThemeServiceProvider;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;

class ApplicationServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadRoutes()
            ->loadConfiguration()
            ->loadViews();

        /** On bootstrap set active theme . */
        app('admin-theme-manager')
            ->setDefaultTheme(
                $this
            );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->registerDependencies();

        /** Register administrator theme manager .. */
        $this->app->singleton('admin-theme-manager', function($app) {
            return new ThemeManager(
                $app['theme-manager']
            );
        });

        /** Register administrator menu .. */
        $this->app->singleton('menu-manager', function($app) {
            return new MenuManager(
                $app['module-cache-manager']
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
        Support\set_config_from_yaml(
            __DIR__ . '/../configuration/general.yaml' , 'administrator'
        );

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

    /**
     * Call protected parent function .
     *
     * @param string $path
     * @param string $namespace
     */
    public function loadViewsFrom($path, $namespace) {
        return parent::loadViewsFrom($path, $namespace);
    }

    /**
     * Register service provider dependencies .
     *
     */
    protected function registerDependencies() {
        $dependencies = [ModuleServiceProvider::class, ThemeServiceProvider::class];

        array_walk($dependencies, function($dependency) {
            app()->register($dependency);
        });
    }

}