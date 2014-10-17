<?php
namespace Reborn\Routing;

use Reborn\Http\Request;

/**
 * Route Middleware Interface
 *
 * @package Reborn\Routing
 * @author Reborn CMS Development Team
 **/
interface MiddlewareInterface
{
	/**
	 * Run method for middleware.
	 * 
	 * @param \Rebron\Http\Request $request
	 * @param \Reborn\Routing\Route $route
	 * @param array $params Parameters from middleware setter.
	 * @return mixed
	 */
	public function run(Request $request, Route $route, array $params = array());
}