<?php

namespace Reborn\Cores;

/**
 * Facade Class.
 * This class is 100% knowledge base on Laravel's Facade Design.
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development
 **/
abstract class Facade
{

	/**
	 * Application (IoC) Container instance
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected static $app;

	/**
	 * Set Application Object
	 *
	 * @param \Reborn\Corres\Application $app
	 * @return void
	 **/
	public static function setApplication(Application $app)
	{
		static::$app = $app;
	}

	/**
	 * Get Application Object
	 *
	 * @return \Reborn\Corres\Application
	 **/
	public static function getApplication()
	{
		return static::$app;
	}

	/**
	 * Get Object Instance to solve with static method call.
	 *
	 * @return Object Instance
	 **/
	protected static function getInstance()
	{
		return null;
	}

	/**
	 * Solve static call for object
	 *
	 * @param string $method
	 * @param array $args
	 * @return void
	 **/
	public static function __callStatic($method, $args)
	{
		$ins = static::getInstance();

		if(is_null($ins)) {
			throw new \RbException("Need Object Instance for ".get_called_class().'::'.$method."()");
		}

		switch (count($args))
		{
			case 0:
			return $ins->$method();

			case 1:
			return $ins->$method($args[0]);

			case 2:
			return $ins->$method($args[0], $args[1]);

			case 3:
			return $ins->$method($args[0], $args[1], $args[2]);

			case 4:
			return $ins->$method($args[0], $args[1], $args[2], $args[3]);

			default:
			return call_user_func_array(array($ins, $method), $args);
		}
	}

} // END abstract class Facade
