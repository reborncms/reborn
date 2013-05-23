<?php

namespace Reborn\Route;

use Reborn\Exception\HttpNotFoundException;

/**
 * Route Class for Reborn
 *
 * @package Reborn\Route
 * @author Myanmar Links Professional Web Development Team
 **/
class Route
{

    /**
     * Route Colllection variable
     *
     * @var array
     **/
    protected static $collection = array();

    /**
     * 404 Route Variable
     *
     * @var null|Object
     **/
    protected static $notFound;

    /**
     * Get the Map Object
     *
     * @return Reborn\Route\Map
     **/
    public static function getMap()
    {
        return new Map();
    }

    /**
     * Add the 404 route.
     *
     * @param string $caller Module Name (or) Closure Method
     * @param string $controller Controller Name
     * @param string $action Action Name
     * @return void
     **/
    public static function addNotFound($caller, $controller = null, $action = null)
    {
        static::$notFound = new \stdClass();

        if ($caller instanceof \Closure) {
            static::$notFound->closure = $caller;
        } else {
            static::$notFound->closure = null;
            static::$notFound->module = $caller;
            static::$notFound->controller = $controller;
            static::$notFound->action = $action;
        }
    }

    /**
     * Get the 404 route
     *
     * @return array
     **/
    public static function getNotFound()
    {
        // If does not set notFound attributes,
        // Throw the HttpNotFoundException
        if (is_null(static::$notFound)) {
            throw new HttpNotFoundException("Request URL is not found!");
        }

        static::$notFound->params = implode('/',\Uri::segments());
        return static::$notFound;
    }

    /**
     * Add the new route
     *
     * @param string $name Route name
     * @param string $path Route uri path
     * @param string $file Action string(Module\Controller::action)
     *                      [eg: Pages\Pages::index]
     * @param string $method HTTP method (optional)
     * @return void
     **/
    public static function add($name, $path, $file, $method = 'all')
    {
        static::$collection[$name] = static::getMap()->add($name, $path, $file, $method);
        return static::$collection[$name];
    }

    /**
     * Add the new route for HTTP GET method
     *
     * @param string $name Route name
     * @param string $path Route uri path
     * @param string $file Action string(Module\Controller::action)
     *                      [eg: Pages\Pages::index]
     * @return void
     **/
    public static function get($name, $path, $file)
    {
        static::$collection[$name] = static::getMap()->add($name, $path, $file, 'get');
    }

    /**
     * Add the new route for HTTP POST method
     *
     * @param string $name Route name
     * @param string $path Route uri path
     * @param string $file Action string(Module\Controller::action)
     *                      [eg: Pages\Pages::index]
     * @return void
     **/
    public static function post($name, $path, $file)
    {
        static::$collection[$name] = static::getMap()->add($name, $path, $file, 'post');
    }

    /**
     * Add the new route for HTTP PUT method
     *
     * @param string $name Route name
     * @param string $path Route uri path
     * @param string $file Action string(Module\Controller::action)
     *                      [eg: Pages\Pages::index]
     * @return void
     **/
    public static function put($name, $path, $file)
    {
        static::$collection[$name] = static::getMap()->add($name, $path, $file, 'put');
    }

    /**
     * Add the new route for HTTP DELETE method
     *
     * @param string $name Route name
     * @param string $path Route uri path
     * @param string $file Action string(Module\Controller::action)
     *                      [eg: Pages\Pages::index]
     * @return void
     **/
    public static function delete($name, $path, $file)
    {
        static::$collection[$name] = static::getMap()->add($name, $path, $file, 'delete');
    }

    /**
     * Get the all route (route collection)
     *
     * @return array
     **/
    public static function getAll()
    {
        return static::$collection;
    }

    /**
     * Get the route by name.
     *
     * @param string $name Route Name
     * @param array $data Data array for route pattern
     * @return string|null
     **/
    public static function getByName($name, $data = array())
    {
        if (isset(static::$collection[$name])) {
            return static::$collection[$name]->parseToUri($data);
        }

        return null;
    }
}
