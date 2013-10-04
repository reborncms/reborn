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

} // END class RouteFacade extends \Facade
