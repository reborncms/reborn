<?php

namespace Reborn\Cores;

use Reborn\Cores\Facade;

/**
 * Error Facade Class
 *
 * @package Reborn\Exception
 **/
class ErrorFacade extends Facade
{

	protected static function getInstance()
	{
		return static::$app['error_handler'];
	}

} // END class ErrorFacade extends Facade
