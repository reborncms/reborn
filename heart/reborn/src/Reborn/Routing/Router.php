<?php

namespace Reborn\Routing;

use ReflectionClass;
use Reborn\Cores\Application;
use Reborn\Filesystem\File;
use Reborn\Http\Uri;
use Reborn\Http\Input;
use Reborn\Util\Str;
use Reborn\Util\Security;
use Reborn\Module\ModuleManager as Module;
use Reborn\Exception\HttpNotFoundException;
use Reborn\Exception\TokenNotMatchException;

/**
 * Router Class for Reborn
 *
 * @package Reborn\Routing
 * @author Myanmar Links Professional Web Development Team
 **/
class Router
{

    /**
     * Applicaion Object
     *
     * @var \Reborn\Core\Application
     **/
    protected $app;

	/**
	 * Variable for request obj
	 *
	 * @var \Reborn\Http\Request
	 **/
	protected $request;

    /**
     * Variable for route collection
     *
     * @var \Reborn\Routing\RouteCollection
     **/
    protected $collection;

    /**
     * Controller Mapper Object
     *
     * @var \Reborn\Route\ControllerMap
     **/
    protected $mapper;

    /**
     * Variable for admin panel URI
     *
     * @var string
     **/
    protected $admin;

    /**
     * Constructor Method
     *
     * @return void
     **/
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->request = $app->request;
        $this->collection = $app->route_collection;

        $this->admin = \Setting::get('adminpanel_url');

        $this->mapper = ControllerMap::create();

        // If routes file have in the user's content folder, load this route file
        if (File::is(CONTENT.'routes.php')) {
            require CONTENT.'routes.php';
        }

        // Load the Application Main Route File
        require APP.'routes.php';
    }

    /**
     * Get RouteCollection instance
     *
     * @return \Reborn\Routing\RouteCollection
     **/
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Dispatch the route for Reborn CMS
     *
     * @return void
     */
    public function dispatch()
    {
        // Call the Event Name Start Routing
        \Event::call('reborn.app.startroute');

        // Get Uri String's 1st Segment
        $uri = Uri::uriString(1, 1);

        $this->addModuleRoute($uri);

        $request_uri = implode('/', Uri::segments());

        $route = $this->collection->match(rawurldecode($request_uri), $this->request);

        if ($route) {

            if (! $route->skipCSRF()) {
                // Check CSRF protection
                $this->checkCSRF();
            }

            // Route with callback function.
            if($route->isClosure) {
                return call_user_func_array($route->callback, $route->params);
            }

            return $this->callbackController($route);
        }

        // If not found in Route Controller, find the controller
        if ($route = $this->detectController()) {
            // Check CSRF protection
            $this->checkCSRF();

            return $this->callbackController($route);
        }

        // Get Missing Route
        if ($route = $this->collection->getMissing($request_uri, $this->request)) {
            // Check CSRF protection
            $this->checkCSRF();

            return $this->callbackController($route);
        }

        // Not found Route and Controller,
        // throw the HttpNotFoundException
        throw new HttpNotFoundException("Request URL is not found!");
    }

    /**
     * Find the Controller and action when not found in
     * Route Collection
     *
     * @param string $uri
     * @return array|boolean
     */
    protected function detectController()
    {
        // Check URI Segment (1) is Admin Panel URI
        if (Uri::segment(1) == $this->admin) {
            // ModuleURI is URI Segment (2).
            // Bcoz URI Segment (1) is AdminPanel URI
            $moduleUri = Uri::segment(2);
            $controller = ucfirst(Str::camel(Uri::segment(3)));
            $admin = true;
        } else {
            $moduleUri = Uri::segment(1);
            $controller = ucfirst(Str::camel(Uri::segment(2)));
            $admin = false;
        }

        // If module is doesn't exists, return false
        if (! $mod = Module::getByUri($moduleUri)) {
            return false;
        }

        if(is_null($controller)) {
            $controller = ucfirst($mod['ns']);
        }

        $module = ucfirst($mod['ns']);

        if ($admin) {
            $file = 'Admin'.DS.$controller.'Controller';
            $action_pos = 4;
        } else {
            $file ='Controller'.DS.$controller.'Controller';
            $action_pos = 3;
        }

        $ctrl_file = $this->mapper->find($moduleUri, $file);

        if (is_null($ctrl_file)) {
            if ($admin) {
                $file = 'Admin'.DS.$module.'Controller';
                $action_pos = 3;
            } else {
                $file ='Controller'.DS.$module.'Controller';
                $action_pos = 2;
            }

            $controller = $module;

            $ctrl_file = $this->mapper->find($moduleUri, $file);
        }

        if (is_null($ctrl_file)) {
            return null;
        }

        $route = new Route('', '');
        // Set Module
        $route->module = $module;
        // Set Controller
        if ($admin) {
            $route->controller = 'Admin\\'.$controller;
        } else {
            $route->controller = $controller;
        }

        // Set Action
        if (is_null(Uri::segment($action_pos))) {
            $route->action = 'index';
        } else {
            $route->action = Str::camel(Uri::segment($action_pos));
        }
        //Set Parameters
        $segments = Uri::segments();
        for($i = 0; $i < $action_pos; $i++) {
            unset($segments[$i]);
        }
        $route->params = $segments;

        return $route;
    }

    /**
     * Call the given controller's action(method).
     *
     * @param Reborn\Route\Route
     * @return \Reborn\Http\Response
     **/
    protected function callbackController($route)
    {
        if (!Module::isEnabled($route->module)) {
             throw new HttpNotFoundException("Request URL is not found!");
        }

        $resolver = new ControllerResolver($this->app, $route);

        return $resolver->resolve();
    }



    /**
     * Add the route file from the module
     *
     * @param string $path Module path
     * @return void
     */
    protected function addModuleRoute($path)
    {
        // If uri is empty, set the default
        if ($path == '') {
            $path = '/';
            $module = Module::getData(\Setting::get('default_module'));
        } else {
            if ($this->admin == $path) {
                $module = Module::getByUri(Uri::segment(2));
            } else {
                $module = Module::getByUri($path);
            }
        }

        if (!Module::has($path)) {
            $module = Module::getData(\Setting::get('default_module'));
        }

        if (!is_null($module)) {
            $path = $module['path'];

            if (is_readable($path.'routes.php')) {
                require $path.'routes.php';
            }

        }
    }

    /**
     * Check the CSRF Token from Request.
     *
     * @return boolean|throw TokenNotMatchException
     **/
    protected function checkCSRF()
    {
        if (Input::isPost()) {
            if ( Security::CSRFvalid() ) {
                return true;
            } else {
                throw new TokenNotMatchException('Request Token doesn\'t match!');
            }
        }

        return true;
    }
}
