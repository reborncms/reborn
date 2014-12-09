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
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Cartalyst\Sentry\Users\UserNotActivatedException;

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
     * Logged in is request from api.
     * 
     * @var boolean
     */
    protected $api_loggedin = false;

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
        if ( $this->api_loggedin )
        {
            $this->apiLogout();
        }
        else
        {
            $this->auth->logout();
        }
    }

    /**
     * Check user is loggedin or not
     *
     * @return boolean
     **/
    public function check()
    {
        if ( $this->auth->check()) {
            return true;
        }

        if ( $this->checkWithHeaderKeyRequest()) {
            $this->api_loggedin = true;

            return true;
        }

        return false;
    }

    /**
     * Check user is loggedin or not with Header key
     * @return boolean
     */
    public function checkWithHeaderKeyRequest()
    {
        $token = $this->app->request->headers->get(static::AUTH_HEADER_KEY);

        if ( is_null($token)) {
            return false;
        }

        $user = $this->getUserProvider()->findBy('auth_api_token', $token);

        if ( is_null($user) || ! $user->isActivated()) {
            return false;
        }

        // If throttling is enabled we check it's status
        if( $this->auth->getThrottleProvider()->isEnabled())
        {
            // Check the throttle status
            $throttle = $this->auth->getThrottleProvider()->findByUser( $user );

            if( $throttle->isBanned() or $throttle->isSuspended())
            {
                return false;
            }
        }

        $this->auth->setUser($user);

        return true;
    }

    /**
     * Login for api request
     * 
     * @return boolean
     * @throws \Cartalyst\Sentry\Throttling\UserBannedException
     * @throws \Cartalyst\Sentry\Throttling\UserSuspendedException
     * @throws \Cartalyst\Sentry\Users\LoginRequiredException
     * @throws \Cartalyst\Sentry\Users\PasswordRequiredException
     * @throws \Cartalyst\Sentry\Users\UserNotFoundException
     * @throws \Cartalyst\Sentry\Users\UserNotActivatedException
     */
    public function loginForApi(array $credentials)
    {
        if ( $this->check()) {
            return $this->auth->getUser();
        }

        // We'll default to the login name field, but fallback to a hard-coded
        // 'login' key in the array that was passed.
        $loginName = $this->getUserProvider()->getEmptyUser()->getLoginName();
        $loginCredentialKey = (isset($credentials[$loginName])) ? $loginName : 'login';

        if (empty($credentials[$loginCredentialKey]))
        {
            throw new LoginRequiredException("The [$loginCredentialKey] attribute is required.");
        }

        if (empty($credentials['password']))
        {
            throw new PasswordRequiredException('The password attribute is required.');
        }

        // If the user did the fallback 'login' key for the login code which
        // did not match the actual login name, we'll adjust the array so the
        // actual login name is provided.
        if ($loginCredentialKey !== $loginName)
        {
            $credentials[$loginName] = $credentials[$loginCredentialKey];
            unset($credentials[$loginCredentialKey]);
        }

        $throttleProvider = $this->auth->getThrottleProvider();

        // If throttling is enabled, we'll firstly check the throttle.
        // This will tell us if the user is banned before we even attempt
        // to authenticate them
        if ($throttlingEnabled = $throttleProvider->isEnabled())
        {
            $ipAddress = $this->auth->getIpAddress();
            $loginName = $credentials[$loginName];

            if ($throttle = $throttleProvider->findByUserLogin($loginName, $ipAddress))
            {
                $throttle->check();
            }
        }

        try
        {
            $user = $this->getUserProvider()->findByCredentials($credentials);
        }
        catch (UserNotFoundException $e)
        {
            if ($throttlingEnabled and isset($throttle))
            {
                $throttle->addLoginAttempt();
            }

            throw $e;
        }

        if ($throttlingEnabled and isset($throttle))
        {
            $throttle->clearLoginAttempts();
        }

        if ( is_null($user)) 
        {
            return false;
        }

        if ( ! $user->isActivated())
        {
            $login = $user->getLogin();
            throw new UserNotActivatedException("Cannot login user [$login] as they are not activated.");
        }

        $user->clearResetPassword();

        $user->recordApiLogin();

        $this->auth->setUser($user);

        $this->api_loggedin = true;

        return $user;
    }

    /**
     * Logout for API login user
     * 
     * @return void
     */
    public function apiLogout()
    {
        $request = $this->app->request;
        $token = $request->headers->get(static::AUTH_HEADER_KEY);

        if ( is_null($token)) {
            return $this->auth->logout();
        }

        $user = $this->getUserProvider()->findBy('auth_api_token', $token);

        if ( ! is_null($user)) {
            $user->cleanApiLoginToken();
        }

        return $this->auth->logout();
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
