<?php

namespace Flysap\Application;

use Illuminate\Auth\GenericUser;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class FileUserProvider implements UserProvider {

    /**
     * The array containing the users.
     *
     * @var array
     */
    protected $users;

    /**
     * The cache used for storing connection tokens
     * @var CacheManager
     */
    protected $cache;

    /**
     * Create a new file user provider.
     *
     * @param  array $users
     * @param HasherContract $hasher
     * @param CacheManager $cache
     */
    public function __construct(array $users, HasherContract $hasher, CacheManager $cache) {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->cache = $cache;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier) {
        $users = $this->getUsers();

        foreach ($users as $user) {
            if( isset($user['id']) && $user['id'] == $identifier )
                return $this->getGenericUser(
                    $user
                );
        }

        return;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token) {
        return $this->cache->get('user' . $identifier . '_token_' . $token, NULL);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token) {
        $this->cache->put('user' . $user->getAuthIdentifier() . '_token_' . $token, $user, 60 * 24);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials) {
        $users = $this->getUsers();

        foreach ($users as $key => $user) {
            if ($this->isMatchArray(array_except($credentials, ['password']), $user)) {
                return $this->getGenericUser(
                    $user
                );
            }
        }

        return;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials) {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $this->hasher->make($user->getAuthPassword()));
    }


    /**
     * Check if is fully match the array .
     *
     * @param array $matched
     * @param array $matcher
     * @return bool
     */
    protected function isMatchArray(array $matched, array $matcher) {
        $isMatch = true;

        foreach ($matched as $key => $value) {
            if (! isset($matcher[$key])) continue;

            if ($matcher[$key] != $value) {
                $isMatch = false;
                break;
            }
        }

        return $isMatch;
    }


    /**
     * Get the generic user.
     *
     * @param  mixed $user
     * @return \Illuminate\Auth\GenericUser|null
     */
    protected function getGenericUser($user) {
        if ($user !== null) {
            return new GenericUser((array)$user + ['remember_token' => '']);
        }
    }

    /**
     * Get users .
     *
     * @return array
     */
    protected function getUsers() {
        return $this->users;
    }

    /**
     * Check if has users .
     *
     * @return bool
     */
    protected function hasUsers() {
        return ! empty($this->users);
    }
}