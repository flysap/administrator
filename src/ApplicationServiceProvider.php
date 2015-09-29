<?php

namespace Flysap\Application;

use Flysap\ModuleManager\ModuleServiceProvider;
use Flysap\Scaffold\ScaffoldServiceProvider;
use Flysap\TableManager\TableServiceProvider;
use Flysap\ThemeManager\ThemeServiceProvider;
use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;
use Auth;
use Laravel\Settings\SettingsServiceProvider;

class ApplicationServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadRoutes()
            ->loadViews();

        $this->publishes([
            __DIR__.'/../configuration' => config_path('yaml/application'),
        ]);

        /** On bootstrap set active theme . */
        app('admin-theme-manager')
            ->setDefaultTheme(
                $this
            );

        /**
         * Register new file auth driver to serve for initial authentication without using
         *  database driver ..
         *
         */
        Auth::extend('file', function($app) {
            $users = config('administrator.auth');

            return new Guard(
                new FileUserProvider($users, $app['hash'], $app['cache']),
                $app['session.store']
            );
        });

        $this->registerBladeExtensions();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->registerDependencies()
            ->loadConfiguration();

        /** Register administrator theme manager .. */
        $this->app->singleton('admin-theme-manager', function($app) {
            return new ThemeManager(
                $app['theme-manager']
            );
        });

        /** Register administrator menu .. */
        #@todo create for future new package which will take care about menu ..
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

        Support\merge_yaml_config_from(
            config_path('yaml/application/general.yaml') , 'administrator'
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
        $dependencies = [
            ModuleServiceProvider::class,
            ThemeServiceProvider::class,
            ScaffoldServiceProvider::class,
            SettingsServiceProvider::class,
            TableServiceProvider::class,
        ];

        array_walk($dependencies, function($dependency) {
            app()->register($dependency);
        });

        return $this;
    }

    /**
     * Register blade extensions
     */
    protected function registerBladeExtensions() {
        #@todo ..
    }

}