<?php

namespace Reborn\Routing;

use Reborn\Http\Request;

/**
 * Route Collection Class for Reborn CMS
 *
 * @package Reborn\Routing
 * @author MyanmarLinks Professional Web Development Team
 **/
class RouteCollection
{

	/**
	 * Route class collection array
	 *
	 * @var array
	 **/
	protected $routes = array();

	/**
	 * Missing Route for Not Found
	 *
	 * @var \Reborn\Routing\Route
	 **/
	protected $missing;

	/**
	 * Current match Route
	 *
	 * @var \Reborn\Routing\Route
	 **/
	protected $current;

	/**
	 * Create and add a route
	 *
	 * @param string $path Uri path
	 * @param string|Closure $callback
	 * @param string|null $name Route name
	 * @param string $method Request method for route
	 * @return \Reborn\Routing\Route
	 **/
	public function add($path, $callback, $name = null, $method = 'ALL')
	{
		$route = new Route($path, $callback, $name, $method);

		$this->addRoute($route, $route->name);

		return $route;
	}

	/**
	 * Create and add a route with GET method
	 *
	 * @param string $path Uri path
	 * @param string|Closure $callback
	 * @param string|null $name Route name
	 * @param string $method Request method for route
	 * @return \Reborn\Routing\Route
	 **/
	public function get($path, $callback, $name = null)
	{
		return $this->add($path, $callback, $name, 'GET');
	}

	/**
	 * Create and add a route with POST method
	 *
	 * @param string $path Uri path
	 * @param string|Closure $callback
	 * @param string|null $name Route name
	 * @param string $method Request method for route
	 * @return \Reborn\Routing\Route
	 **/
	public function post($path, $callback, $name = null)
	{
		return $this->add($path, $callback, $name, 'POST');
	}

	/**
	 * Create and add a route with PUT method
	 *
	 * @param string $path Uri path
	 * @param string|Closure $callback
	 * @param string|null $name Route name
	 * @param string $method Request method for route
	 * @return \Reborn\Routing\Route
	 **/
	public function put($path, $callback, $name = null)
	{
		return $this->add($path, $callback, $name, 'PUT');
	}

	/**
	 * Create and add a route with DELETE method
	 *
	 * @param string $path Uri path
	 * @param string|Closure $callback
	 * @param string|null $name Route name
	 * @param string $method Request method for route
	 * @return \Reborn\Routing\Route
	 **/
	public function delete($path, $callback, $name = null)
	{
		return $this->add($path, $callback, $name, 'DELETE');
	}

	/**
     * REST Resource for Controller.
     * Suport lists -
     *  - GET       {resources}             Controller::index()
     *  - GET       {resources}/{id}        Controller::view($id)
     *  - GET       {respurces}/add         Controller::add()
     *  - POST      {resources}             Controller::create()
     *  - GET       {resources}/{id}/edit   Controller::edit($id)
     *  - PUT       {resources}/{id}        Controller::update($id)
     *  - DELETE    {resources}/{id}        Controller::delete($id)
     *
     * @param string $resource Resourece Uri (eg: user)
     * @param string $controller Controller File Name
     * @return void
     **/
    public function resources($resources, $controller)
    {
        $this->get($resources, $controller.'::index', $resources);
        $this->get($resources.'/add', $controller.'::add', $resources.'_add');
        $this->post($resources, $controller.'::create', $resources.'_create');
        $this->get($resources.'/{int:id}/edit', $controller.'::edit', $resources.'_edit');
        $this->put($resources.'/{int:id}', $controller.'::update', $resources.'_update');
        $this->delete($resources.'/{int:id}', $controller.'::delete', $resources.'_delete');
        $this->get($resources.'/{int:id}', $controller.'::view', $resources.'_view');
    }

    /**
     * Add Missing control route
     *
	 * @param string|Closure $callback
	 * @param string|null $name Route name
	 * @param string $method Request method for route
	 * @param string $pattern Route pattern. Default is {*:slug}
	 * @return \Reborn\Routing\Route
     **/
    public function missing($callback, $name = null, $method = 'ALL', $pattern = null)
    {
    	$pattern = is_null($pattern) ? '{*:slug}' : $pattern;

    	$route = new Route($pattern, $callback, $name, $method);

		$this->missing = $route;

		return $route;
    }

	/**
	 * Add route to collection
	 *
	 * @param \Reborn\Routing\Route\ $route Route Instance
	 * @param string $name Route name
	 * @return void
	 **/
	public function addRoute(Route $route, $name)
	{
		$this->routes[$name] = $route;
	}

	/**
	 * Get the route by name
	 *
	 * @param string $name Route name
	 * @return null|\Reborn\Routing\Route
	 **/
	public function getRoute($name)
	{
		return isset($this->routes[$name]) ? $this->routes[$name] : null;
	}

	/**
	 * Get Url by Route Name
	 *
	 * @param string $name Route name
	 * @param array $data Replace data
	 * @return string|null
	 **/
	public function getUrlByRouteName($name, $data = array())
	{
		$route = $this->getRoute($name);
		if( is_null($route) ) return null;

		return $route->getUrl($data);
	}

	/**
	 * Get all route
	 *
	 * @return array
	 **/
	public function all()
	{
		return $this->routes;
	}

	/**
	 * Get the match route by given uri
	 *
	 * @param string $uri Uri Path
	 * @param \Reborn\Http\Request $request Request instance
	 * @return boolean|\Reborn\Routing\Route
	 **/
	public function match($uri, Request $request)
	{
		foreach ($this->routes as $name => $route) {

			if ($match = $route->match($uri, $request)) {
				$this->current = $match;
				return $match;
			}
		}

		return false;
	}

	/**
	 * Get missing route
	 *
	 * @return boolean|\Reborn\Routing\Route
	 **/
	public function getMissing($uri, Request $request)
	{
		if (!is_null($this->missing)) {
			return $this->missing->match($uri, $request);
		}

		return false;
	}

	/**
	 * Get current route instance
	 *
	 * @return \Reborn\Routing\Route
	 **/
	public function current()
	{
		return $this->current;
	}

} // END class RouteCollection
