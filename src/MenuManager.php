<?php

namespace Flysap\Administrator;

use Flysap\ModuleManger\ModulesCaching;
use Flysap\Permissions\Permissions;

class MenuManager {


    /**
     * @var ModulesCaching
     */
    private $modulesCaching;

    /**
     * @var Permissions
     */
    private $permissions;

    public function __construct(ModulesCaching $modulesCaching, Permissions $permissions) {

        $this->modulesCaching = $modulesCaching;
        $this->permissions = $permissions;
    }

    /**
     * Get modules menu .
     *
     * @param array $modules
     * @return array|mixed
     */
    public function buildModulesSections(array $modules = array()) {
        $modules = $this->modulesCaching
            ->toArray($modules);

        $menus = [];
        array_walk($modules, function($module) use(& $menus) {
            if( isset($module['menu']) ) {
                if(! isset($module['menu']['section']))
                    return false;

                /** Check if is active . */
                if(! isset($module['menu']['active']) && ! $module['menu']['active'])
                    return false;

                /** Check for permissions . */
                if( isset( $module['menu']['permissions'] ) )
                    if( ! $this->permissions->canAccess(
                        $module['menu']['permissions']
                    ) )
                        return false;

                /** Check for roles . */
                if( isset( $module['menu']['roles'] ) )
                    if( ! $this->permissions->is(
                        $module['menu']['roles']
                    ) )
                        return false;

                $menus[$module['menu']['section']] = array_except($module['menu'], ['section']);
            }
        });

        return $menus;
    }
}