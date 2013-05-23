<?php

namespace Reborn\Event;

/**
 * Interface for the Event Driver Class
 *
 * @package Reborn\Event
 * @author Myanmar Links Professional Web Development Team
 **/
Interface EventInterface
{
	/**
	 * Add the event to the app
	 *
	 * @param string $name Event name
	 * @param mixed $callback
	 * @return mixed
	 */
	public function on($name, $callback);

	/**
	 * Check the given event name is already exists or not
	 *
	 * @param string $name Event name
	 * @return boolean;
	 */
	public function has($name);

	/**
	 * Remove the event from the register event
	 *
	 * @param string $name Event name
	 * @return void
	 */
	public function off($name);

	/**
	 * Clear the all event from app.
	 *
	 * @return void
	 **/
	public function clear();

	/**
     * Call(Trigger) the event.
     *
     * @param string $name Name of event
     * @param array $data Data array for callback event (optional)
     * @return void
     */
	public function call($name, $data = array());

} // END Interface EventInterface
