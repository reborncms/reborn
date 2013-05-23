<?php

namespace Reborn\Http;

use Reborn\Cores\Registry;
use Symfony\Component\HttpFoundation\Cookie as SfCookie;

/**
 * Cookie class for Reborn
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Cookie
{

	protected static $path = '/';

	protected static $domain = null;

	protected static $secure = false;

	protected static $httpOnly = true;

	public static function has($key)
	{
		return static::getRequest()->cookies->has($key);
	}

	public static function get($key, $default = null)
	{
		return static::getRequest()->cookies->get($key, $default);
	}

	public static function set($key, $val, $minute = 0)
	{
		$time = ($minute == 0) ? 0 : time() + ($minute * 60);

		return new SfCookie($key, $val, $time, static::$path, static::$domain, static::$secure, static::$httpOnly);
	}

	public static function delete($key)
	{
		return new SfCookie($key, null, 1, static::$path, static::$domain);
	}

	protected static function getRequest()
	{
		return \Registry::get('app')->request;
	}

} // END class Cookie
