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
	 * Route Group Path string
	 *
	 * @var string|null
	 **/
	protected $group;

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
		// Check Route Group
		if (!is_null($this->group)) {
			if ($path == '') {
				$path = rtrim(implode('/', $this->group), '/');
			} else {
				$path = \Str::endIs(implode('/', $this->group), '/').ltrim($path, '/');
			}
		}

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
     * Make route with default CURD.
     * <code>
     * 		Route::crud('blog', 'Blog\Blog', 'blog');
     *
     * 		// Same with these routes
     * 		Route::get('blog', 'Blog\Blog::index', 'blog_index');
     * 		Route::add('blog/create', 'Blog\Blog::create', 'blog_create')
     * 				->method('GET', 'POST');
     * 		Route::add('blog/edit/{int:id}', 'Blog\Blog::edit', 'blog_edit')
     * 				->method('GET', 'POST');
     * 		Route::get('blog/delete/{int:id}', 'Blog\Blog::delete', 'blog_delete');
     *
     * 		// CURD with middlewares
     * 		Route::crud('blog', 'Blog\Blog', 'blog', [
     * 			'index' => 'index_middlewares',
     *  		'create' => 'create_middlewares',
     *  		'edit' => 'edit_middlewares',
     *  		'delete' => 'delete_middlewares',
     * 		]);
     * </code>
     *
     * @param string $path Main route path
     * @param string $controller Controller name
     * @param string $name_prefix Route name prefix string
     * @param array $middlewares Middlewares array for CRUD.
     * @param string $id_type {id} placehoder's type. Default is "int"
     * @return void
     **/
    public function crud($path, $controller, $name_prefix, $middlewares = array(), $id_type = 'int')
    {
    	if (false !== strpos($path, '@admin')) {
			$path = str_replace('@admin', \Config::get('app.adminpanel'), $path);
		}

		$path = rtrim($path, '/');

		// Add for index (R) with get method
    	$r = $this->add($path, $controller.'::index', $name_prefix.'_index', 'GET');
    	if ( isset($middlewares['index']) ) {
    		$r->before($middlewares['index']);
    	}

    	// Add for create (C) with get and post methods
    	$r = $this->add($path.'/create', $controller.'::create', $name_prefix.'_create', array('GET', 'POST'));
    	if ( isset($middlewares['create']) ) {
    		$r->before($middlewares['create']);
    	}

    	// Add for edit (U) with get and post methods
    	$r = $this->add($path.'/edit/{'.$id_type.':id}', $controller.'::edit', $name_prefix.'_edit', array('GET', 'POST'));
    	if ( isset($middlewares['edit']) ) {
    		$r->before($middlewares['edit']);
    	}

    	// Add for delete (D) with get method
    	$r = $this->add($path.'/delete/{'.$id_type.':id}', $controller.'::delete', $name_prefix.'_delete', 'GET');
    	if ( isset($middlewares['delete']) ) {
    		$r->before($middlewares['delete']);
    	}
    }

    /**
     * Make Route Group for same prefix route.
     * Example ::
     * <code>
     * 		Route::group('blog', function(){
     * 			// For 'blog'
     * 			Route::get('', 'Blog\Blog::index', 'blog_index');
     * 			// For 'blog/create'
     *  		Route::add('create', 'Blog\Blog::create', 'blog_create');
     * 			// For 'blog/edit/{int:id}'
     *   		Route::add('edit/{int:id}', 'Blog\Blog::edit', 'blog_edit');
     * 			// For 'blog/delete/{int:id}'
     *   		Route::get('delete/{int:id}', 'Blog\Blog::delete', 'blog_delete');
     * 		});
     * </code>
     *
     * @param string $prefix
     * @param Closure $callback
     * @return \Reborn\Routing\RouteCollection
     **/
    public function group($prefix, \Closure $callback)
    {
    	if (false !== strpos($prefix, '@admin')) {
			$prefix = str_replace('@admin', \Config::get('app.adminpanel'), $prefix);
		}

    	$this->group[] = $prefix;

    	$callback();

    	// Clear the group path
		$this->group = null;

    	return $this;
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
	 * Get all route lists
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
	 * @param string $uri
	 * @param \Reborn\Http\Request $request
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
