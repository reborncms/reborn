<?php

namespace Reborn\MVC\View;

/**
 * ViewData Binding Class
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development
 **/
class ViewData
{
    /**
     * View Binding Data
     *
     * @var array|null
     */
    protected static $bind;

    /**
     * Bind the view event with name
     *
     * @param  string  $name     name to call at view file
     * @param  Closure $callback Callback function
     * @return void
     */
    public static function bind($name, \Closure $callback)
    {
        static::$bind[$name] = $callback;
    }

    /**
     * Check binding name has or not
     *
     * @param  string $name
     * @return void
     */
    public static function has($name)
    {
        return isset(static::$bind[$name]);
    }

    /**
     * Call from view for bind result
     *
     * @param  string $name    Binding name
     * @param  array  $options Options array for binding callback function
     * @return mixed
     */
    public static function make($name, $options = array())
    {
        if ( static::has($name) and is_callable(static::$bind[$name]) ) {
            $result = call_user_func_array(static::$bind[$name], array($options));

            return $result;
        }

        return null;
    }

} // END class ViewEvent
