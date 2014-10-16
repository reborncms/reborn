<?php

namespace Reborn\Routing;

use Reborn\Cores\Application;
use Reborn\Http\Response;
use Reborn\Exception\HttpNotFoundException;

/**
 * ControllerResolver Class for Reborn
 *
 * @package Reborn\Routing
 * @author Myanmar Links Professional Web Development Team
 **/
class ControllerResolver
{

    /**
     * Application Object
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Request Object
     *
     * @var \Reborn\Http\Request
     **/
    protected $request;

    /**
     * Route Map Object
     *
     * @var \Reborn\Routing\Route
     **/
    protected $route;

    /**
     * Default instance method of ControllerResolver.
     *
     * @param  \Reborn\Cores\Applciation $app
     * @param  \Reborn\Routing\Route     $route
     * @return void
     **/
    public function __construct(Application $app, Route $route)
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

            // First we call the before middlewares for this route
            $response = $this->route->runBeforeMiddlewares($this->request);

            // If Middleware return Response Instance, return this Response
            if ($response instanceof Response) {
                return $response;
            }

            // Make Application Request's Body ID (for customize class or id name in html)
            $this->makeRequestBodyId();

            return $this->callController($module, $controller, $action, $params);
        } else {
            // Class doesn't exit, so we throw HTTPNotFound
            throw new HttpNotFoundException("Request Class is not found!");
        }
    }

    /**
     * Make body id for request action.
     * 
     * @return void
     */
    protected function makeRequestBodyId()
    {
        $module = $this->route->module;
        $ctrl = str_replace('\\', '-', $this->route->controller);
        $action = $this->route->action;

        $id = strtolower($module.'-'.$ctrl.'-'.$action);

        $this->app['var.body_id'] = $id;
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

        // Prevent Direct call the actionCaller
        $this->checkActionIsActionCaller($action);

        // Set current request(module, controller, action and params)
        // to Request Object
        $this->setRequestParameters($module, $controller, $action, $params);

        return array($module, $controller, $action, $params);
    }

    /**
     * Prevent Direct call the actionCaller
     * Because this method is action control for Controller
     *
     * @param  string                     $action Action method name
     * @return HttpNotFoundException|void
     **/
    protected function checkActionIsActionCaller($action)
    {
        // Prevent Direct call the actionCaller
        if ('actionCaller' === $action) {
            throw new HttpNotFoundException("Request Method is not callable method!");
        }
    }

    /**
     * Set request data to Request Object
     *
     * @param  string $module     Request module name
     * @param  string $controller Request controller name
     * @param  string $action     Request action name
     * @param  array  $params     Request params data array
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
     * @param  string                         $module     Request module name
     * @param  string                         $controller Request controller name
     * @param  string                         $action     Request action name
     * @param  array                          $params     Request params data array
     * @return Response|HttpNotFoundException
     **/
    protected function callController($module, $controller, $action, $params)
    {
        // Call the active Module's Boot Method
        \Module::boot($module);

        // Solve the Controller with Illuminate\Container
        $instance = $this->app->make($controller);

        $args = $this->makeRequestParameters($instance, $action, $params);

        // Call action caller method
        return $instance->actionCaller($this->app, $action, $args);
    }

    /**
     * Check requirement arguments for action method and
     * make require arguments
     *
     * @param  \Reborn\MVC\Controller\Controller                 $obj    Controller instance
     * @param  string                                            $method Action method name
     * @param  array                                             $params Request parameters
     * @return array
     * @throws \BadMethodCallException|\InvalidArgumentException
     **/
    protected function makeRequestParameters($obj, $method, $params)
    {
        try {
            $r = new \ReflectionMethod($obj, $method);
        } catch (\ReflectionException $e) {
            // Throw ReflectionException in Development Environment and
            // Throw HttpNotFoundException in Other Environment
            if ($this->app->runInDevelopment()) {
                throw $e;
            } else {
                throw new HttpNotFoundException($e->getMessage());
            }
        }

        if (!$r->isPublic()) {
            throw new \BadMethodCallException("Request action [$method] is not callable!");
        }

        $require = $r->getParameters();

        if (empty($require)) return array();

        $missing = $args = array();

        foreach ($require as $r) {
            $name = $r->getName();
            if (array_key_exists($name, $params)) {
                $args[$name] = $params[$name];
            } elseif ($r->isDefaultValueAvailable()) {
                $args[$name] = $r->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        // Check Require arguments and give arguments
        if (count($require) !== count($args)) {
            $missing = implode(', ', $missing);
            throw new \InvalidArgumentException("Action [$method] required { $missing }' arguments.");
        }

        return $args;
    }

} // END class ControllerResolver
