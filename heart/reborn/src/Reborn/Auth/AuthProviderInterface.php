<?php

namespace Reborn\Auth;

interface AuthProviderInterface
{
    const AUTH_HEADER_KEY = "X-Authorize-Token";
    /**
     * Registers a user by giving the required credentials
     * and an optional flag for whether to activate the user.
     *
     * @param  array $credentials
     * @param  bool  $activate
     * @return mixed
     */
    public function register(array $credentials, $activate = false);

    /**
     * Attempts to authenticate the given user
     * according to the passed credentials.
     *
     * @param  array $credentials
     * @param  bool  $remember
     * @return mixed
     **/
    public function authenticate(array $credentials, $remember = false);

    /**
     * Logs the current user out.
     *
     * @return void
     **/
    public function logout();

    /**
     * Check user is loggedin or not
     *
     * @return boolean
     **/
    public function check();

    /**
     * Login method for API request
     * 
     * @return boolean
     */
    public function loginForApi(array $credentials);

    /**
     * See if a user has access to the passed permission(s).
     * Permissions are merged from all groups the user belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the user must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * Super users have access no matter what.
     *
     * @param  string|array $permissions
     * @param  bool         $all
     * @return bool
     */
    public function hasAccess($permissions, $all = true);

    /**
     * Get user provider.
     *
     * @return mixed
     **/
    public function getUserProvider();

    /**
     * Get user group provider.
     *
     * @return mixed
     **/
    public function getGroupProvider();
}
