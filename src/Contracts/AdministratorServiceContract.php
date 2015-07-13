<?php

namespace Flysap\Administrator\Contracts;

interface AdministratorServiceContract {

    /**
     * Set framework ..
     *
     * @param $framework
     * @return mixed
     */
    public function setFramework($framework);

    /**
     * Get framework active .
     *
     * @return mixed
     */
    public function getFramework();
}