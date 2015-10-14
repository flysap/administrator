<?php

namespace Flysap\Application;

use Flysap\Application\Widgets\UsersWidget;
use Flysap\Media\MediaServiceProvider;
use Flysap\ModuleManager\ModuleServiceProvider;
use Flysap\Scaffold\ScaffoldServiceProvider;
use Laravel\Settings\SettingsServiceProvider;
use Parfumix\TableManager\TableServiceProvider;
use Flysap\ThemeManager\ThemeServiceProvider;
use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;
use Auth;

class ApplicationServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadRoutes();

        $this->publishes([
            __DIR__.'/../configuration' => config_path('yaml/application'),
            __DIR__.'/../migrations' => database_path('migrations'),
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

        $this->registerBladeExtensions()
            ->registerWidgets();
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

        /** Register widget manager . */
        $this->app->singleton('widget-manager', WidgetManager::class);
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

        Support\set_config_from_yaml(
            __DIR__ . '/../configuration/settings.yaml' , 'administrator-settings'
        );

        Support\merge_yaml_config_from(
            config_path('yaml/application/settings.yaml') , 'administrator-settings'
        );

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
            TableServiceProvider::class,
            MediaServiceProvider::class,
            SettingsServiceProvider ::class,
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

        return $this;
    }

    /**
     *  Register widgets .
     */
    protected function registerWidgets() {
        $widgets = [
            #@todo add some widgets ..
        ];

        array_walk($widgets, function($widget, $alias) {
            app('widget-manager')
                ->addWidget($alias, $widget);
        });

        return $this;
    }

}