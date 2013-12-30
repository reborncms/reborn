<?php

namespace Reborn\Auth;

use Reborn\Cores\Facade;

class AuthFacade extends Facade
{
	/**
	 * Get Auth Provider instance
	 *
	 * @return \Reborn\Auth\AuthProviderInterface
	 **/
	public static function getInstance()
	{
		return static::$app['auth_provider'];
	}
}
