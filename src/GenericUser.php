<?php

namespace Flysap\Application;

use Illuminate\Auth\GenericUser as User;

class GenericUser extends User {

    /**
     * Roles .
     *
     * @return array
     */
    public function roles() {
        return isset($this->attributes['roles']) ? $this->attributes['roles'] : [];
    }

    /**
     * Permissions .
     *
     * @return array
     */
    public function permissions() {
        return isset($this->attributes['permissions']) ? $this->attributes['permissions'] : [];
    }

    /**
     * Has roles user ?
     *
     * @return bool
     */
    public function hasRoles() {
        return !empty($this->roles());
    }

    /**
     * Has permissions user?
     *
     * @return bool
     */
    public function hasPermissions() {
        return !empty($this->permissions());
    }

    /**
     * Can permissions .
     *
     * @param array $permissions
     * @return bool
     */
    public function can(array $permissions = []) {
        if(! $this->hasPermissions())
            return true;

        if( array_intersect($permissions, $this->permissions()) )
            return true;

        return false;
    }

    /**
     * Is roles .
     *
     * @param array $roles
     * @return bool
     */
    public function is(array $roles = []) {
        if(! $this->hasRoles())
            return true;

        if( array_intersect($roles, $this->roles()) )
            return true;

        return false;
    }
}