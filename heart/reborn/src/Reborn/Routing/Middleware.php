<?php

namespace Reborn\Routing;

use Reborn\Http\Request;
use Reborn\Exception\NotImplementedException;

/**
 * Route Middleware Class
 *
 * @package Reborn\Routing
 * @author MyanmarLinks Professional Web Development Team
 **/
class Middleware
{
    /**
     * Registered middleware collection array
     *
     * @var array
     **/
    protected static $middlwares = array();

    /**
     * Register the route middleware
     *
     * @param  string         $name     Middleware name
     * @param  Closure|string $callback Callback for middleware
     * @return void
     **/
    public static function register($name, $callback)
    {
        static::$middlwares[$name] = $callback;
    }

    /**
     * Run the middleware callback by give name
     *
     * @param  string                $name
     * @param  \Reborn\Http\Request  $request
     * @param  \Reborn\Routing\Route $route
     * @return void
     **/
    public static function run($name, Request $request, Route $route)
    {
        list($name, $param) = static::callbackAndParams($name);

        if (! isset(static::$middlwares[$name]) ) {
            return null;
        }

        $callback = static::$middlwares[$name];

        // If callback is string, this is object and call the run() method
        if (is_string($callback)) {
            $ins = new $callback;

            if ($ins instanceof MiddlewareInterface) {
                return $ins->run($request, $route, $param);                
            } else {
                $msg = "Middleware instance [$callback] must be implement of MiddlewareInterface";
                throw new NotImplementedException($msg);
            }

            return $ins->run($request, $route, $param);
        }

        // Callback is Closure
        return $callback($request, $route, $param);
    }

    /**
     * Parse callback name to callback
     *
     * @param  string $name Middleware callback name
     * @return array
     **/
    protected static function callbackAndParams($name)
    {
        if (false === strpos($name, ':')) {
            return array($name, array());
        }

        $lists = explode(':', $name, 2);

        $data = explode(',', $lists[1]);
        $params = array();
        foreach ($data as $d) {
            list($k, $v) = explode('=', $d);
            $params[$k] = $v;
        }

        return array($lists[0], $params);
    }

} // END class Middleware
