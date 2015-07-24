<?php

namespace Flysap\Administrator;

use Flysap\Administrator\Exceptions\AdministratorException;
use Flysap\ThemeManager\ThemeManager as ModuleThemeManager;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;

class ThemeManager {

    const DEFAULT_THEME_FILE = '/bootstrap/default_theme.json';

    /**
     * @var ModuleThemeManager
     */
    private $themeManager;

    public function __construct(ModuleThemeManager $themeManager) {

        $this->themeManager = $themeManager;
    }

    /**
     * Set default theme .
     *
     * @param ServiceProvider $serviceProvider
     * @return $this
     * @throws AdministratorException
     */
    public function setDefaultTheme(ServiceProvider $serviceProvider) {
        if( ! $defaultTheme = $this->getDefaultCached() )
            $defaultTheme = config('administrator.default_theme');

        $this->setTheme(
            $defaultTheme, $serviceProvider
        );

        return $this;
    }

    public function setTheme($theme, ServiceProvider $serviceProvider) {
        $fullPath =  app_path('../' . $this->themeManager->getStoragePath() . DIRECTORY_SEPARATOR . $theme);

        if( ! \Flysap\Support\is_path_exists(
            $fullPath
        ) )
            throw new AdministratorException(
                _('Invalid theme')
            );

        if( ! \Flysap\Support\is_folder_empty(
            $fullPath
        ))
            throw new AdministratorException(
                _('Invalid theme')
            );

        $serviceProvider->loadViewsFrom(
            app_path('../themes/' . $theme),
            'themes'
        );

        return $this;
    }

    /**
     * Get default cached theme .
     *
     * @return null|string
     */
    public function getDefaultCached() {
        $fullCachePath = app_path(
            '../' . self::DEFAULT_THEME_FILE
        );

        $theme = null;
        if( Support\is_path_exists($fullCachePath) )
            $theme = file_get_contents($fullCachePath);

        return $theme;
    }
}