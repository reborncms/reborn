<?php

namespace Reborn\Route;

use ReflectionClass;
use Reborn\Cores\Registry;
use Reborn\Filesystem\File;
use Reborn\Http\Uri;
use Reborn\Util\Str;
use Reborn\Module\ModuleManager as Module;
use Reborn\Exception\HttpNotFoundException;

/**
 * Router Class for Reborn
 *
 * @package Reborn\Route
 * @author Myanmar Links Professional Web Development Team
 **/
class Router
{
	/**
	 * Variable for request obj
	 *
	 * @var \Reborn\Http\Request
	 **/
	protected $request;

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
    public function __construct()
    {
        $this->request = Registry::get('app')->request;

        $this->admin = \Setting::get('adminpanel_url');

        $this->mapper = new ControllerMap();
        $this->mapper->make();

        // If routes file have in the user's content folder, load this route file
        if (File::is(CONTENT.'routes.php')) {
            require CONTENT.'routes.php';
        }

        // Load the Application Main Route File
        require APP.'routes.php';
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

        // If uri is empty, set the default
        if ($uri == '') {
            $uri = '/';
            $module = Module::getData(\Setting::get('default_module'));
        } else {
            if ($this->admin == $uri) {
                $module = Module::getByUri(Uri::segment(2));
            } else {
                $module = Module::getByUri($uri);
            }
        }

        $modulePath = $module['path'];

        // Add the route file from module
        $this->addModuleRoute($modulePath);

        $routes = Route::getAll();

        // Search from the Route Collection
        foreach ($routes as $route) {
            if ($route->match(implode('/', Uri::segments()))) {
                if($route->closure instanceof \Closure) {
                    return call_user_func_array($route->closure,
                                                $route->params);
                }

                return $this->callbackController($route);
            }
        }

        // If not found in Route Controller, find the controller
        if ($route = $this->detectController($uri)) {
            return $this->callbackController($route);
        } else {
            // We call the 404.
            return $this->notFound();
        }

        // Not found Route and Controller,
        // throw the HttpNotFoundException
        throw new HttpNotFoundException("Request URL is not found!");
    }

    /**
     * Method for Page Not Found Route
     *
     * @return void
     **/
    public function notFound()
    {
        // We call the 404 Route.
        $route = Route::getNotFound();

        // Call route not found event
        \Event::call('reborn.app.routeNotFound', $route);

        if($route->closure instanceof \Closure) {
            return call_user_func_array($route->closure, (array)$route->params);
        }

        return $this->callbackController($route);
    }

    /**
     * Find the Controller and action when not found in
     * Route Collection
     *
     * @param string $uri
     * @return array|boolean
     */
    protected function detectController($uri)
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

        $route = new \stdClass();
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
     * @param Reborn\Route\Map
     * @return void
     **/
    protected function callbackController($route)
    {
        $module = $route->module;
        $controller = '\\'.$module.'\Controller\\'.$route->controller.'Controller';
        $action = $route->action;
        $params = $route->params;

        // Prevent Direct call the callByMethod
        if ('callByMethod' === $action) {
            throw new HttpNotFoundException("Request Method is not callable method!");
        }

        // Set the active at request
        $this->request->module = $module;
        $this->request->controller = $controller;
        $this->request->action = $action;
        $this->request->params = $params;

        if (!Module::isEnabled($module)) {
            return $this->notFound();
        }

        Module::load($module);

        if (class_exists($controller)) {

            // Call the active Module's Boot Method
            Module::boot($module);

            $reflect = new ReflectionClass($controller);

            $controllerClass = $reflect->newInstance();

            // Method (Action) Not Found
            if (! $reflect->hasMethod($action) ) {
                throw new HttpNotFoundException("Request Method is not found!");
            }

            $method = $reflect->getMethod($action);

            // Check method is public or not
            if (! $method->isPublic()) {
                throw new HttpNotFoundException("Request Method is not public method!");
            }

            \Event::call('reborn.controller.process.starting');

            // Call the Controller class's before method.
            $reflect->getMethod('before')->invoke($controllerClass);

            // Call the action method
            $args = array($action, $params);
            $response = $reflect->getMethod('callByMethod')
                                    ->invokeArgs($controllerClass, (array)$args);

            // Call the Controller class's after method.
            $response = $reflect->getMethod('after')
                                ->invoke($controllerClass, $response);

            \Event::call('reborn.controller.process.ending', array($response));

            return $response;
        } else { // Class doesn't exit, so we throw HTTPNotFound
            throw new HttpNotFoundException("Request Class is not found!");
        }
    }

    /**
     * Add the route file from the module
     *
     * @param string $path Module path
     * @return void
     */
    protected function addModuleRoute($path)
    {
        if (is_readable($path.'routes.php')) {
            require $path.'routes.php';
        }
    }
}
