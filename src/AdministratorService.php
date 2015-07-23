<?php

namespace Flysap\Administrator;

use Flysap\Administrator\Contracts\AdministratorServiceContract;
use Flysap\ModuleManager\ModulesCaching;

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
}