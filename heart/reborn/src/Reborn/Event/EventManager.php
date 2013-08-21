<?php

namespace Reborn\Event;

use Reborn\Config\Config;
use Reborn\Exception\EventException as EventException;
use Reborn\Module\ModuleManager as Module;

/**
 * Event Manager Class for them Reborn
 *
 * @package Rebor\Event
 * @author Myanmar Links Professional Web Development Team
 **/
class EventManager
{

	/**
	 * Event Instance
	 *
	 * @var string
	 **/
	protected static $instance;

	/**
	 * Event Driver Lists
	 *
	 * @var array
	 **/
	protected static $drivers = array();

	/**
	 * Initialize the Event Manager
	 *
	 */
	public static function initialize()
	{
		$default = Config::get('manager.event.default');
		static::$drivers = Config::get('manager.event.support_drivers');

		$events = require APP.'event'.DS.'register'.EXT;

		if (array_key_exists($default, static::$drivers)) {
			static::$instance = new static::$drivers[$default]($events);
		} else {
			throw new EventException("Evetn driver {$default} is not support!");
		}
	}

	/**
	 * Call the method by PHP Magic method _callStatic
	 *
	 */
	public static function __callStatic($method, $args)
	{
		$args = (array) $args;

		if (is_callable(array(static::$instance, $method))) {
			return call_user_func_array(array(static::$instance, $method), $args);
		}

		throw new \BadMethodCallException("{$method} is not callable");
	}

} // END class EventManager
