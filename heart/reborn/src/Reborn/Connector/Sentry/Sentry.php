<?php
namespace Reborn\Connector\Sentry;

/**
 * Sentry Fecade class for Reborn
 *
 * @package Reborn\Connector\Sentry
 * @author Myanmar Links Professional Web Development Team
 **/

use Reborn\Connector\Sentry\SymfonySession;
use Cartalyst\Sentry\Hashing\BcryptHasher;
use Cartalyst\Sentry\Sessions\NativeSession;
use Cartalyst\Sentry\Cookies\NativeCookie;
use Cartalyst\Sentry\Sentry as BaseSentry;
use Cartalyst\Sentry\Throttling\Eloquent\Provider as ThrottleProvider;
use Cartalyst\Sentry\Users\Eloquent\Provider as UserProvider;
use Cartalyst\Sentry\Groups\Eloquent\Provider as GroupProvider;

class Sentry {

	/**
	 * Sentry instance.
	 *
	 * @var \Cartalyst\Sentry\Sentry
	 */
	protected static $instance;

	/**
	 * Get Instance Method
	 *
	 * @return \Cartalyst\Sentry\Sentry
	 */
	public static function instance()
	{
		if (static::$instance === null)
		{
			static::$instance = static::createSentry();
		}

		return static::$instance;
	}

	/**
	 * Creates an instance of Sentry.
	 *
	 * @return \Cartalyst\Sentry\Sentry
	 */
	public static function createSentry()
	{
		$hasher           = new BcryptHasher;
		$session          = new SymfonySession(\Config::get('app.sentry_keyname', 'reborn_cms'));
		$cookie           = new NativeCookie;
		$groupProvider    = new GroupProvider;
		$userProvider     = new UserProvider($hasher);
		$throttleProvider = new ThrottleProvider($userProvider);

		return new BaseSentry(
			$userProvider,
			$groupProvider,
			$throttleProvider,
			$session,
			$cookie,
			\Input::ip()
		);
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::instance();

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}

}
