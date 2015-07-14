<?php

namespace Flysap\Administrator;

use Flysap\Administrator\Contracts\AdministratorServiceContract;
use Flysap\ModuleManger\ModulesCaching;

class AdministratorService implements AdministratorServiceContract {

    /**
     * @var ModulesCaching
     */
    private $modulesCaching;

    public function __construct(ModulesCaching $modulesCaching) {

        $this->modulesCaching = $modulesCaching;
    }

    /**
     * Set framework ..
     *
     * @param $framework
     * @return mixed
     */
    public function setFramework($framework) {
        // TODO: Implement setFramework() method.
    }

    /**
     * Get framework active .
     *
     * @return mixed
     */
    public function getFramework() {
        // TODO: Implement getFramework() method.
    }

    /**
     * Get modules menu .
     *
     * @param array $modules
     * @return array|mixed
     */
    public function getMenu(array $modules = array()) {
        $modules = $this->modulesCaching
            ->toArray($modules);

        $menus = [];
        array_walk($modules, function($module) use(&$menus) {
            if( isset($module['menu']) ) {
                $menus = array_merge(
                    $menus, $module['menu']
                );
            }
        });

        return $menus;
    }
}