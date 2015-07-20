<?php

namespace Flysap\Administrator;

use Flysap\Administrator\Contracts\AdministratorServiceContract;
use Flysap\ModuleManger\ModulesCaching;
use Flysap\Permissions\Permissions;

class AdministratorService implements AdministratorServiceContract {

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
}