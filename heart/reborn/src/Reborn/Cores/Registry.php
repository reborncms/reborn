<?php

namespace Reborn\Cores;

/**
 * Registry class for Reborn
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Registry
{

    /**
     * register object variable
     *
     * @var array
     **/
    protected static $register = array();

    /**
     * Set the object for registry
     *
     * @param string $key
     * @param mixed  $obj
     */
    public static function set($key, $obj)
    {
        static::$register[$key] = $obj;
    }

    /**
     * Get the object from registry
     *
     * @param  string $key Key for registry object
     * @return mixed
     */
    public static function get($key)
    {
        if (isset(static::$register[$key])) {
            return static::$register[$key];
        }

        return null;
    }

    /**
     * Remove the object from registry
     *
     * @param  string $key
     * @return void
     */
    public static function remove($key)
    {
        if (isset(static::$regsiter[$key])) {
            unset(static::$register[$key]);
        }
    }

} // END class Registry
