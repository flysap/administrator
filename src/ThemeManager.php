<?php

namespace Flysap\Application;

use Flysap\Application\Exceptions\ApplicationException;
use Flysap\ThemeManager\ThemeManager as ModuleThemeManager;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;

class ThemeManager {

    const DEFAULT_THEME_FILE = 'default_theme.json';

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
     * @throws ApplicationException
     */
    public function setDefaultTheme(ServiceProvider $serviceProvider) {
        if( ! $defaultTheme = json_decode($this->getDefaultCached()) )
            $defaultTheme = config('administrator.default_theme');

        $this->setTheme(
            $defaultTheme, $serviceProvider
        );

        return $this;
    }

    /**
     * Set theme .
     *
     * @param $theme
     * @param ServiceProvider $serviceProvider
     * @return $this
     * @throws ApplicationException
     * @throws \Flysap\ThemeManager\Exceptions\ThemeUploaderException
     */
    public function setTheme($theme, ServiceProvider $serviceProvider) {
        $fullPath =  app_path('../' . $this->themeManager->getStoragePath() . DIRECTORY_SEPARATOR . $theme);

        if( ! Support\is_path_exists(
            $fullPath
        ) )
            throw new ApplicationException(
                _('Invalid theme')
            );

        if( ! Support\is_folder_empty(
            $fullPath
        ))
            throw new ApplicationException(
                _('Invalid theme')
            );

        $cachePath =  app_path('../' . $this->themeManager->getCachePath());

        Support\dump_file($cachePath . DIRECTORY_SEPARATOR . self::DEFAULT_THEME_FILE, json_encode(
            $theme
        ));

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
            '../' . $this->themeManager->getCachePath() . DIRECTORY_SEPARATOR . self::DEFAULT_THEME_FILE
        );

        $theme = null;
        if( Support\is_path_exists($fullCachePath) )
            $theme = file_get_contents($fullCachePath);

        return $theme;
    }
}