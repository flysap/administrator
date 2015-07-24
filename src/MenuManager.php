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

    public function __construct(ModulesCaching $modulesCaching) {

        $this->modulesCaching = $modulesCaching;

        $modules = $this->modulesCaching
            ->toArray();

        $this->setModules($modules);
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

                $url = (isset($menu['route'])) ? route($menu['route']) : ( isset($menu['href']) ? $menu['href'] : '#' );

                $result .= '<li><a href="'. $url .'">'.$menu['label'].'</a></li>';
            });

            $result .= '</ul>';

        });

        return $result;
    }

    public function __toString() {
        return $this->render();
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
}