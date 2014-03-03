<?php

namespace Reborn\Module;

/**
 * Module Bootstrap Abstract Class for Reborn Module
 *
 * @package Reborn\Module
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class AbstractBootstrap
{

    /**
     * Variable for Application (IOC) instance
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    public function __construct(\Reborn\Cores\Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Abstract function when call the module is boot.
     */
    abstract public function boot();

    /**
     * Abstract function for Menu when call the Admin Panel load.
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
     * Abstract function for the module register.
     */
    abstract public function register();

} // End class AbstractBootstrap
