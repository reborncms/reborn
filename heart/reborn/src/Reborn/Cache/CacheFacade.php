<?php

namespace Reborn\Cache;

use Reborn\Cores\Facade;

/**
 * Cache Facade class for Reborn
 *
 * @package Reborn\Connector
 * @author Myanmar Links Professional Web Development Team
 **/
class CacheFacade extends Facade
{
	/**
	 * Get config instance.
	 *
	 * @return \Reborn\Config\Config
	 **/
	public static function getInstance()
	{
		return static::$app['cache']->getDriver();
	}
}
