<?php

namespace Reborn\Event;

use Reborn\Exception\EventException as EventException;

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
     * @var \Reborn\Event\EventInterface
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
    public static function initialize($app)
    {
        $default = $app['config']->get('manager.event.default');
        static::$drivers = $app['config']->get('manager.event.support_drivers');

        $events = require APP.'event'.DS.'register'.EXT;

        if (array_key_exists($default, static::$drivers)) {
            static::$instance = new static::$drivers[$default]($events);
        } else {
            throw new EventException("Evetn driver {$default} is not support!");
        }

        // Event from base content folder
        // This event will be work for all site
        if (file_exists(BASE_CONTENT.'events.php')) {
            require BASE_CONTENT.'events.php';
        }

        // Event from current site content folder
        if (file_exists(CONTENT.'events.php')) {
            require CONTENT.'events.php';
        }
    }

    /**
     * Call the method by PHP Magic method _callStatic
     *
     * @param  string $method Method name
     * @param  mixed  $args   Arguments for method
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $args = (array) $args;

        if (is_null(static::$instance)) {
            return null;
        }

        if (is_callable(array(static::$instance, $method))) {
            return call_user_func_array(array(static::$instance, $method), $args);
        }

        throw new \BadMethodCallException("{$method} is not callable");
    }

} // END class EventManager
