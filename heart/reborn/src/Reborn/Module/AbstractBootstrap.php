<?php

namespace Reborn\Module;

abstract class AbstractBootstrap
{

	public function __construct()
	{
		return $this;
	}

	/**
	 * Abstract function when call the module is load.
	 */
	abstract public function boot();

	/**
	 * Abstract function when call the Admin Panel load.
	 */
	abstract public function adminMenu(\Reborn\Util\Menu $menu, $modUri);

	/**
	 * Abstract function when call the Admin Panel's Setting Module load.
	 */
	abstract public function settings();

	/**
	 * Abstract function when call the Admin Panel load and module is active.
	 */
	abstract public function moduleToolbar();

	/**
	 * Abstract function for the event register
	 */
	abstract public function eventRegister();

} // End class AbstractBootstrap
