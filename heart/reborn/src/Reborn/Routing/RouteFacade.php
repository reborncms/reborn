<?php

namespace Reborn\Routing;

/**
 * Route Facade Class for Static Call to add route
 *
 * @package Reborn\Routing
 * @author MyanmarLinks Professional Web Development Team
 **/
class RouteFacade extends \Facade
{

	/**
	 * Get RouteCollection instance
	 *
	 * @return \Reborn\Routing\RouteCollection
	 **/
	public static function getInstance()
	{
		return static::$app['route_collection'];
	}

	/**
	 * Register Route Middleware Callback
	 *
	 * @param string $name Middleware name
	 * @param Closure|string $callback Callback for middleware
	 * @return void
	 **/
	public static function middleware($name, $callback)
	{
		Middleware::register($name, $callback);
	}

} // END class RouteFacade extends \Facade
