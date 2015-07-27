<?php

namespace Flysap\Administrator;

use Flysap\ModuleManager\ModulesCaching;
use Flysap\Support\Traits\ElementAttributes;
use Flysap\Support\Traits\ElementsGroup;
use Flysap\Support\Traits\ElementsTrait;

/**
 * Class MenuManager
 * @package Flysap\Administrator
 *
 */
class MenuManager {

    use ElementsTrait, ElementAttributes, ElementsGroup;

    /**
     * @var ModulesCaching
     */
    private $modulesCaching;

    /**
     * @var array
     */
    protected $paths = [];

    public function __construct(ModulesCaching $modulesCaching) {

        $this->modulesCaching = $modulesCaching;

        $menuPaths = config('administrator.module_namespaces');

        $this->addNamespace($menuPaths);

        $modules= $this->modulesCaching
            ->toArray();

        $this->setModules(array_merge(
            $modules,
            $this->getMenuNamespaces()
        ));
    }

    /**
     * Get modules menu .
     *
     * @param string $group
     * @param array $attributes
     * @return array|mixed
     */
    public function render($group = null, array $attributes = array()) {
        if( $attributes )
            $this->setAttributes($attributes);

        $groups = $this->getGroups();

        if(! is_null($group) )
            $groups = [$this->getGroup($group)];

        array_walk($groups, function($group) use(& $result) {

            $result  = '<ul';
            $result .= $this->renderAttributes();
            $result .= '>';

            $menus = $group->getElements();
            array_walk($menus, function($menu) use(& $result) {

                /** Check for permissions . */
                if( isset( $menu['permissions'] ) )
                    if( ! \Flysap\Users\can($menu['permissions']) )
                        return false;

                /** Check for roles . */
                if( isset( $module['roles'] ) )
                    if( ! \Flysap\Users\is($menu['roles']) )
                        return false;

                /** @var Get the variable from view shared . $label */
                $label = $this->detectVariables(
                    $menu['label']
                );

                $url = (isset($menu['route'])) ? route($menu['route']) : ( isset($menu['href']) ? $menu['href'] : '#' );

                $result .= '<li><a href="'. $url .'">'.$label.'</a></li>';
            });

            $result .= '</ul>';

        });

        return $result;
    }

    public function __toString() {
        return $this->render();
    }


    /**
     * Add new namespace .
     *
     * @param $path
     * @return $this
     */
    public function addNamespace($path) {
        if(! is_array($path))
            $path = (array)$path;

        array_walk($path, function($path) {
            $this->paths[] = $path;
        });

        return $this;
    }

    /**
     * Get all namespaces .
     *
     * @return array
     */
    public function getNamespaces() {
        return $this->paths;
    }

    /**
     * Flush namespaces .
     *
     * @return $this
     */
    public function flushNamespaces() {
        $this->paths = [];

        return $this;
    }

    /**
     * Get menu paths.
     *
     * @return array
     */
    public function getMenuNamespaces() {
        $menuPaths = $this->getNamespaces();
        $modules   = [];

        array_walk($menuPaths, function($path) use(& $modules) {
            $modules = array_merge($modules, $this->modulesCaching->findModulesConfig(
                app_path('../' . $path)
            ));
        });

        return $modules;
    }


    /**
     * Prepare menu .
     *
     * @param array $modules
     * @return $this
     */
    public function setModules(array $modules) {
        $menus = [];

        array_walk($modules, function($module) use(&$menus) {
            if( isset($module['menu']) )
                array_walk($module['menu'], function($menu)  {
                    $this->addGroup(
                        $menu['section'], [$menu['label'] => $menu]
                    );

                    $this->elements[$menu['section']. '_' . $menu['label']] = $menu;
                });
        });

       return $this;
    }

    /**
     * Get modules .
     *
     * @param array $keys
     * @return array
     */
    public function getModules($keys = array()) {
        return $this->getElements($keys);
    }


    /**
     * Render attributes .
     *
     * @return string
     */
    protected function renderAttributes() {
        $result = '';

        foreach ($this->getAttributes() as $attribute => $value) {
            $result .= " {$attribute}=\"{$value}\"";
        }

        return $result;
    }

    /**
     * Detect variables from label menu .
     *
     * @param $label
     * @return mixed
     */
    private function detectVariables($label) {
        $expression = "/(:(\\w+))/i";
        $view       = app('view');

        if( preg_match($expression, $label, $matches) )
            if( $replacement = $view->shared($matches[2]) )
                $label = preg_replace($expression, $replacement, $label);

        return $label;
    }
}