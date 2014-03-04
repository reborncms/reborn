<?php

namespace Reborn\Auth;

use Reborn\Cores\Application;
use Reborn\Auth\Sentry\SymfonySession;
use Reborn\Auth\Sentry\Eloquent\UserProvider;
use Reborn\Auth\Sentry\Eloquent\GroupProvider;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Hashing\BcryptHasher;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Throttling\Eloquent\Provider;

/**
 * Reborn Auth Provider with Sentry2.
 *
 * @package Reborn\Auth
 * @author Myanmar Links Professional Web Development Team
 **/

class AuthSentryProvider implements AuthProviderInterface, UserInterface
{
    /**
     * Reborn Application (IOC) Container instance.
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Sentry instance.
     *
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $auth;

    /**
     * Sentry User Provider instance
     *
     * @var \Cartalyst\Sentry\Users\ProviderInterface
     **/
    protected $user_provider;

    /**
     * Sentry Group Provider instance
     *
     * @var \Cartalyst\Sentry\Groups\ProviderInterface
     **/
    protected $group_provider;

    /**
     * Create AuthProvider instance.
     *
     * @return void
     **/
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->auth = $this->createSentryInstance();
    }

    /**
     * Get Superuser's email address
     *
     * @return string
     **/
    public function getSuperuserEmail()
    {
        $user = $this->auth->findAllUsersWithAccess(array('superuser'));

        return $user[0]->email;
    }

    /**
     * Get Superuser's full name
     *
     * @return string
     **/
    public function getSuperuserName()
    {
        $user = $this->auth->findAllUsersWithAccess(array('superuser'));

        return $user[0]->fullname;
    }

    /**
     * Registers a user by giving the required credentials
     * and an optional flag for whether to activate the user.
     *
     * @param  array                                 $credentials
     * @param  bool                                  $activate
     * @return \Cartalyst\Sentry\Users\UserInterface
     */
    public function register(array $credentials, $activate = false)
    {
        return $this->auth->register($credentials, $activate);
    }

    /**
     * Attempts to authenticate the given user
     * according to the passed credentials.
     *
     * @param  array                                 $credentials
     * @param  bool                                  $remember
     * @return \Cartalyst\Sentry\Users\UserInterface
     **/
    public function authenticate(array $credentials, $remember = false)
    {
        return $this->auth->authenticate($credentials, $remember);
    }

    /**
     * Logs the current user out.
     *
     * @return void
     **/
    public function logout()
    {
        $this->auth->logout();
    }

    /**
     * Check user is loggedin or not
     *
     * @return boolean
     **/
    public function check()
    {
        return $this->auth->check();
    }

    /**
     * Check loggedin user has access for given permissions.
     *
     * @param  string|array $permissions
     * @param  bool         $all
     * @return boolean
     **/
    public function hasAccess($permissions, $all = true)
    {
        if (! $this->check() ) {
            return false;
        }

        return $this->auth->getUser()->hasAccess($permissions, $all);
    }

    /**
     * Get user provider.
     *
     * @return \Cartalyst\Sentry\Users\ProviderInterface
     **/
    public function getUserProvider()
    {
        if (is_null($this->user_provider)) {
            $this->user_provider = new UserProvider(new BcryptHasher);
        }

        return $this->user_provider;
    }

    /**
     * Get group provider.
     *
     * @return \Cartalyst\Sentry\Groups\ProviderInterface
     **/
    public function getGroupProvider()
    {
        if (is_null($this->group_provider)) {
            $this->group_provider = new GroupProvider;
        }

        return $this->group_provider;
    }

    /**
     * Get loggedin user data model.
     *
     * @return mixed
     **/
    public function getUser()
    {
        if ($this->check()) {
            return $this->auth->getUser();
        }

        return null;
    }

    /**
     * Get loggedin user's "id" data value.
     *
     * @return integer|null
     **/
    public function getUserId()
    {
        if ($this->check()) {
            return $this->auth->getUser()->id;
        }

        return null;
    }

    /**
     * Get loggedin user's "fullname" data value.
     * Fullname format is "first_name last_name".
     *
     * @param  \Closure|null $callback Callback function for username.
     * @return string|null
     **/
    public function getUserName(\Closure $callback = null)
    {
        if ($this->check()) {
            if ( is_null($callback) ) {
                return $this->auth->getUser()->fullname;
            }

            return $callback($this->auth->getUser());
        }

        return null;
    }

    /**
     * Get loggedin user's "email" data value.
     *
     * @return string|null
     **/
    public function getUserEmail()
    {
        if ($this->check()) {
            return $this->auth->getUser()->email;
        }

        return null;
    }

    /**
     * Get Sentry Auth instance.
     *
     * @return \Cartalyst\Sentry\Sentry
     **/
    protected function getSentryInstance()
    {
        if ($this->auth === null) {
            $this->auth = $this->createSentryInstance();
        }

        return $this->auth;
    }

    /**
     * Creates an instance of Sentry.
     *
     * @return \Cartalyst\Sentry\Sentry
     */
    public function createSentryInstance()
    {
        $key = \Config::get('app.sentry_keyname', 'reborn_cms');

        $userProvider     = $this->getUserProvider();
        $throttleProvider = new Provider($userProvider);
        $session          = new SymfonySession($this->app, $key);
        $cookie           = new NativeCookie;

        return new Sentry(
            $userProvider,
            $this->getGroupProvider(),
            $throttleProvider,
            $session,
            $cookie,
            \Input::ip()
        );
    }

    /**
     * Dynamically method from auth package with PHP magic method.
     *
     * @param  string $method
     * @param  array  $params
     * @return mixed
     **/
    public function __call($method, $params)
    {
        switch (count($params)) {
            case 0:
                return $this->auth->$method();

            case 1:
                return $this->auth->$method($params[0]);

            case 2:
                return $this->auth->$method($params[0], $params[1]);

            case 3:
                return $this->auth->$method($params[0], $params[1], $params[2]);

            case 4:
                return $this->auth->$method($params[0], $params[1], $params[2], $params[3]);

            default:
                return call_user_func_array(array($this->auth, $method), $params);
        }
    }
}
