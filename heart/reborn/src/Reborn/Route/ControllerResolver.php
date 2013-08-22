<?php

namespace Reborn\Route;

use Reborn\Cores\Application;
use ReflectionClass;
use Reborn\Exception\HttpNotFoundException;

/**
 * ControllerResolver Class for Reborn
 *
 * @package Reborn\Route
 * @author Myanmar Links Professional Web Development Team
 **/
class ControllerResolver
{

	/**
	 * Application Object
	 *
	 * @var Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * Request Object
	 *
	 * @var Reborn\Http\Request
	 **/
	protected $request;

	/**
	 * Route Map Object
	 *
	 * @var Reborn\Route\Map
	 **/
	protected $route;

	/**
	 * Constructor.
	 *
	 * @return void
	 **/
	public function __construct(Application $app, Map $route)
	{
		$this->app = $app;
		$this->request = $app->request;
		$this->route = $route;
	}

	/**
	 * Resolve the controller and return response.
	 *
	 * @return Response|HttpNotFoundException
	 **/
	public function resolve()
	{
		list($module, $controller, $action, $params) = $this->prepareFromRoute();

        \Module::load($module);

        if (class_exists($controller)) {
        	return $this->callController($module, $controller, $action, $params);
        } else {
        	// Class doesn't exit, so we throw HTTPNotFound
        	throw new HttpNotFoundException("Request Class is not found!");
        }
	}

	/**
	 * Prepare to solve the Controller base on $route
	 *
	 * @return array
	 **/
	protected function prepareFromRoute()
	{
		$module = $this->route->module;
        $controller = '\\'.$module.'\Controller\\'.$this->route->controller.'Controller';
        $action = $this->route->action;
        $params = $this->route->params;

        // Prevent Direct call the callByMethod
        $this->checkActionIsCallByMethod($action);

        // Set current request(module, controller, action and paramas) to Request Object
        $this->setRequestParameters($module, $controller, $action, $params);

        return array($module, $controller, $action, $params);
	}

	/**
	 * Prevent Direct call the callByMethod
	 * Because this method is action control for Controller
	 *
	 * @param string $action Action method name
	 * @return HttpNotFoundException|void
	 **/
	protected function checkActionIsCallByMethod($action)
	{
		// Prevent Direct call the callByMethod
        if ('callByMethod' === $action) {
            throw new HttpNotFoundException("Request Method is not callable method!");
        }
	}

	/**
	 * Set request data to Request Object
	 *
	 * @param string $module Request module name
	 * @param string $controller Request controller name
	 * @param string $action Request action name
	 * @param array $params Request params data array
	 * @return void
	 **/
	protected function setRequestParameters($module, $controller, $action, $params)
	{
		// Set the active at request
        $this->request->module = $module;
        $this->request->controller = $controller;
        $this->request->action = $action;
        $this->request->params = $params;
	}

	/**
	 * Call controller's method and return Response
	 *
	 * @param string $module Request module name
	 * @param string $controller Request controller name
	 * @param string $action Request action name
	 * @param array $params Request params data array
	 * @return Response|HttpNotFoundException
	 **/
	protected function callController($module, $controller, $action, $params)
	{
		// Call the active Module's Boot Method
        \Module::boot($module);

        // Solve the Controller with Illuminate\Container
        $instance = $this->app->make($controller);

        // Call action caller method
        return $instance->actionCaller($this->app, $action, $params);
	}

} // END class ControllerResolver
