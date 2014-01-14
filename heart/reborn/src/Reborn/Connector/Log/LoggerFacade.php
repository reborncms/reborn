<?php

namespace Reborn\Connector\Log;

use Reborn\Cores\Facade;

/**
 * Logger Facade class for Reborn
 *
 * @package Reborn\Connector
 * @author Myanmar Links Professional Web Development Team
 **/
class LoggerFacade extends Facade
{
	/**
	 * Get config instance.
	 *
	 * @return \Reborn\Config\Config
	 **/
	public static function getInstance()
	{
		return static::$app['log'];
	}
}
