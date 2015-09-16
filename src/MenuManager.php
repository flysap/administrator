<?php

namespace Flysap\Administrator;

use Flysap\ModuleManager\ModulesCaching;
use Flysap\Support\Traits\ElementAttributes;
use Flysap\Support\Traits\ElementsGroup;
use Flysap\Support\Traits\ElementsTrait;
use Flysap\Users;
use Flysap\Support;

class MenuManager {

    use ElementsTrait, ElementAttributes, ElementsGroup;

    private $modulesCaching;

    protected $modules = [];

    protected $namespaces = [];

    protected $isBuild = false;

    /**
     * @param ModulesCaching $modulesCaching
     */
    public function __construct(ModulesCaching $modulesCaching) {

        $this->modulesCaching = $modulesCaching;
    }


    /**
     * @param null $group
     * @param array $attributes
     * @return mixed
     */
    public function render($group = null, array $attributes = array()) {
        $this->buildMenu();

        if( $attributes )
            $this->setAttributes($attributes);

        $groups = $this->getGroups();

        if(! is_null($group) )
            $groups = [$this->getGroup($group)];


        array_walk($groups, function($group) use(& $result) {

            #@todo ..
            $result  = '<ul';
            $result .= $this->renderAttributes(['class']);
            $result .= '>';

            $menus = $group->getElements();
            array_walk($menus, function($menu) use(& $result) {

                /** Check for permissions . */
                if( isset( $menu['permissions'] ) )
                    if( ! Users\can($menu['permissions']) )
                        return false;

                /** Check for roles . */
                if( isset( $module['roles'] ) )
                    if( ! Users\is($menu['roles']) )
                        return false;

                /** @var Get the variable from view shared . $label */
                $label = $this->detectVariables($menu['label']);

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
     * Prepare namespaces .
     *
     */
    public function buildMenu() {
        if(! $this->isBuild) {
            $defaultPaths = config('administrator.module_namespaces');

            $this->addNamespace($defaultPaths);

            $this->addModules(
                $this->modulesCaching
                    ->toArray()
            );

            $this->setMenu(array_merge(
                $this->getModules(),
                $this->findModules()
            ));

            $this->isBuild = true;
        }

        return $this;
    }


    /**
     * Add module .
     *
     * @param $module
     * @return $this
     */
    public function addModule($module) {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * @param $modules
     * @return $this
     */
    public function addModules($modules) {
        if(! is_array($modules))
            $modules = (array)$modules;

        array_walk($modules, function($module) {
            $this->modules[] = $module;
        });

        return $this;
    }

    /**
     * Get modules
     *
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }

    /**
     * Flush modules .
     *
     * @return $this
     */
    public function flushModules() {
        $this->modules = [];

        return $this;
    }

    /**
     * Find modules .
     *
     * @return array
     */
    public function findModules() {
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
     * Add module namespace .
     *
     * @param $namespace
     * @return $this
     */
    public function addNamespace($namespace) {
        if(! is_array($namespace))
            $namespace = (array)$namespace;

        array_walk($namespace, function($namespace) {
            if( ! Support\is_path_exists(
                app_path('../' . $namespace)
            ))
                return false;

            $this->namespaces[] = $namespace;
        });

        return $this;
    }

    /**
     * Get modules namespaces .
     *
     * @return array
     */
    public function getNamespaces() {
        return $this->namespaces;
    }

    /**
     * Flush all namespaces .
     *
     * @return $this
     */
    public function flushNamespaces() {
        $this->namespaces = [];

        return $this;
    }


    /**
     * Set menu .
     *
     * @param array $modules
     * @return $this
     */
    public function setMenu(array $modules) {
        array_walk($modules, function($module) {
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
     * Get menu
     *
     * @param array $keys
     * @return array
     */
    public function getMenu($keys = array()) {
        return $this->getElements($keys);
    }


    /**
     * Detect dynamic variables .
     *
     * @param $label
     * @return mixed
     */
    private function detectVariables($label) {
        $expression  = "/(:(\\w+))/i";
        $view        = app('view');

        if( preg_match($expression, $label, $matches) )
            $label = preg_replace($expression, $view->shared($matches[2], ''), $label);

        return $label;
    }
}