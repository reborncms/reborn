<?php

namespace Reborn\Event;

/**
 * Simple Event Driver Class for Reborn
 *
 * @package Reborn\Event
 * @author Myanmar Links Professional Web Development Team
 **/
class SimpleEvent implements \Reborn\Event\EventInterface
{

    /**
     * Variable for events list
     *
     * @var array
     */
    protected $events = array();

    /**
     * Default Construct method for Event Class
     *
     * @param array $events Events array list
     */
    public function __construct($events = array())
    {
        if (! empty($events)) {
            foreach ($events as $k => $e) {
                $this->on($e['name'], $e['callback']);
            }
        }
    }

    /**
     * Add(Register) the Event.
     * Note: Use prefix to prevent event conflict.
     *
     * @param string $name Event name (eg: blog_post_create)
     * @param string $callback Callback function name.
     */
    public function on($name, $callback)
    {
        $this->events[$name][]['callback'] = $callback;
    }

    /**
     * Check the given event name is have or not
     *
     * @param string $name Event name
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->events[$name]);
    }

    /**
     * Remove(UnRegister) the given event name.
     *
     * @param string $name Name of the event
     * @return void
     */
    public function off($name)
    {
        if (isset($this->events[$name])) {
            unset($this->events[$name]);
        }
    }

    /**
     * Clear the all event from app.
     *
     * @return void
     **/
    public function clear()
    {
        unset($this->events);
    }

    /**
     * Call(Trigger) the event.
     *
     * @param string $name Name of event
     * @param array $params Paramater array for callback event (optional)
     * @param boolean $frist_only Make First event only
     * @return mixed
     */
    public function call($name, $params = array(), $first_only = false)
    {
        $result = array();

        $params = (array)$params;

        if (isset($this->events[$name])) {
            foreach ($this->events[$name] as $call) {
                if (is_callable($call['callback'])) {
                    $res = call_user_func_array($call['callback'], $params);

                    if($first_only and !is_null($res)) {
                        return $res;
                    } else {
                        $result[] = $res;
                    }
                }
            }
        }

        return $result;
    }

} // END class SimpleEvent
