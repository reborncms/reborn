<?php

namespace Reborn\Routing;

use Closure;
use Reborn\Util\Str;
use Reborn\Http\Request;

/**
 * Route Class for Reborn CMS
 *
 * @package Reborn\Route
 * @author MyanmarLinks Professional Web Development Team
 **/
class Route
{

	/**
	 * Regex Pattern Constant for route path check
	 *
	 * @var string
	 **/
	const REGEX = '`(\\\?(?:/|\.|))\{((.*?):(.*?))?\}(\?|)`';

	/**
	 * Variable for the route's name
	 *
	 * @var string
	 **/
	public $name;

	/**
	 * Variable for the route's Callback Function
	 *
	 * @var string|Closure
	 **/
	public $callback;

	/**
	 * Variable for route's Callback is closure
	 *
	 * @var boolean
	 **/
	public $isClosure = false;

	/**
	 * Variable for the module name for this route
	 *
	 * @var string
	 **/
	public $module;

	/**
	 * Variable for the controller name for this route
	 *
	 * @var string
	 **/
	public $controller;

	/**
	 * Variable for the action name for this route
	 *
	 * @var string
	 **/
	public $action;

	/**
	 * Variable for the param lists for this route
	 *
	 * @var array
	 **/
	public $params = array();

	/**
	 * Variable for the route's path (URI)
	 *
	 * @var string
	 **/
	protected $path;

	/**
	 * Variable for the request HTTP methods for this route
	 *
	 * @var array
	 **/
	protected $methods;

	/**
	 * Host name string for this route. (Optional)
	 *
	 * @var string
	 **/
	protected $host;

	/**
	 * Default param value array
	 *
	 * @var array
	 **/
	protected $defaults = array();

	/**
	 * Skip CSRF Auto Check for this route
	 *
	 * @var boolean
	 **/
	protected $skip_csrf = false;

	/**
	 * Default instance method
	 *
	 * @param string $path Uri path
	 * @param string|Closure $callback Callback Controller::action string or Closure
	 * @param string|null $name Route name
	 * @param string|array $method Request method
	 * @return void
	 **/
	public function __construct($path, $callback, $name = null, $methods = 'ALL')
	{
		$path = ($path == '/') ? $path : trim($path, '/');

		// Replace Admin Panel Links
		if (false !== strpos($path, '@admin')) {
			$admin = \Config::get('app.adminpanel');
			$this->path = str_replace('@admin', $admin, $path);
		} else {
			$this->path = $path;
		}

		if ($callback instanceof Closure) {
			$this->isClosure = true;
		}

		$this->callback = $callback;

		$this->method($methods);

		$this->name = $this->generateName($name);
	}

	/**
	 * Set boolean to skip CSRF autocheck.
	 *
	 * @param boolean $skip Set true to skip csrf autocheck
	 * @return \Reborn\Routing\Route
	 **/
	public function csrf($skip = true)
	{
		$this->skip_csrf = (boolean) $skip;

		return $this;
	}

	/**
	 * Set request method for this route
	 *
	 * @param string|array $method Request method (eg: get, post)
	 * @return \Reborn\Routing\Route
	 **/
	public function method($methods)
	{
		$this->methods = array();

		foreach ((array) $methods as $method) {
			$this->methods[] = strtoupper($method);
		}

		return $this;
	}

	/**
	 * Get the method array from this route
	 *
	 * @return array
	 **/
	public function getMethod()
	{
		return $this->methods;
	}

	/**
	 * Set Host name for this route
	 *
	 * @param string $host Host name for path (eg: john.example.com)
	 * @return \Reborn\Routing\Route
	 **/
	public function host($host)
	{
		$this->host = rtrim($host, '/').'/';

		return $this;
	}

	/**
	 * Get the host string from this route
	 *
	 * @return string|null
	 **/
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set Default Param for this route
	 *
	 * @param array $param Default param with array(key => value)
	 * @return \Reborn\Routing\Route
	 **/
	public function defaults(array $param)
	{
		$this->defaults = $param;

		return $this;
	}

	/**
	 * Check this route skip for CSRF auto checking
	 *
	 * @return boolean
	 **/
	public function skipCSRF()
	{
		return $this->skip_csrf;
	}

	/**
	 * Check request is match with this route.
	 * If match, return this route
	 *
	 * @param string $path Request Info Path
	 * @param \Reborn\Http\Request $request Request instance
	 * @return boolean|\Reborn\Routing\Route
	 **/
	public function match($path, Request $request)
	{
		// First we check host
		if ( ! is_null($this->host) ) {
			if ($request->baseUrl() != $this->host) {
				return false;
			}
		}

		// Next we check HTTP Verb if require
		if (! in_array('ALL', $this->methods)) {
			$verb = $request->getMethod();

			if (! in_array($verb, $this->methods)) {
				return false;
			}
		}

		// If $path is equal with $path (or) $path is '', return this route
		if ($this->path == $path || ($this->path == '/' and $path == '')) {

			$this->compiling();

			return $this;
		}

		// Make Regular Expression Pattern for this route's path
		$pattern = $this->makeRegexPattern();

		if (preg_match($pattern, $path, $match)) {

			$this->compiling($match);

			return $this;
		}

		return false;
	}

	/**
	 * Comliling route data.
	 * Make Module, Controller, Action, etc..
	 *
	 * @param array|null $matche Match values from preg_match
	 * @return \Reborn\Routing\Route
	 **/
	public function compiling($match = null)
	{
		if (false === $this->isClosure) {
			$this->parseCallable();
		}

		if (!is_null($match)) {
			// Parse Parameter values
			$this->parseParameter($match);
		}

		return $this;
	}

	/**
	 * Parse Callback String (Controller File) to module, controller, action
	 *
	 * @return void
	 **/
	protected function parseCallable()
	{
		if (false != strpos($this->callback, '::')) {
			$arr = explode('::', $this->callback);
			$namespace = explode('\\', $arr[0], 2);
			$this->module = $namespace[0];
			$this->controller = $namespace[1];
			$this->action = Str::camel($arr[1]);
		} else {
			$namespace = explode('\\', $this->callback, 2);
			$this->module = $namespace[0];
			$this->controller = $namespace[1];
			$this->action = 'index';
		}
	}

	/**
	 * Parse parameter values for this route
	 *
	 * @param array $params Parameter array from route path mathch
	 * @return void
	 **/
	protected function parseParameter($params)
	{
		foreach ($params as $key => $value) {
			if (!is_integer($key)) {
				$this->params[$key] = rawurlencode($value);
			}
		}

		// Merge default value and request param
		$this->params = array_merge($this->defaults, $this->params);
	}

	/**
	 * Make Regular Expression Pattern for this route's path
	 *
	 * @return string
	 **/
	protected function makeRegexPattern()
	{
		if (preg_match_all(self::REGEX, $this->path, $matches, PREG_SET_ORDER)) {

			$find = $replace = array();

			foreach ($matches as $m) {
				$regex = $this->getPattern($m[3]);
				$key = $m[4];
				$find[] = $m[0];
				$optional = ($m[5] == '') ? null : '?';
				$prefix = ($m[1] == '') ? null : $m[1];
				$replace[] = '(?:'.$prefix.'(?P<'.$key.'>'.$regex.'))'.$optional;
			}

			$route = str_replace($find, $replace, $this->path);

			return '@^'.$route.'$@';
		}

		return '@^'.$this->path.'$@';
	}

	/**
	 * Get regular expression pattern
	 *
	 * @param string $type Regex type
	 * @return string
	 **/
	protected function getPattern($type)
    {
    	// For custom regex pattern
    	if (false !== strpos($type, '@')) {
    		return substr($type, 1);
    	}

        $pattern = '';
        switch (strtolower($type))
        {
            case "int":
                $pattern = "[0-9]++";
                break;
            case "hex":
            	$pattern = "[0-9][A-F][a-f]++";
            	break;
            case "str":
            	$pattern = "[0-9a-zA-Z\-_]+";
            	break;
            case "alpha":
                $pattern = "[a-zA-Z]+";
                break;
            case "alnum":
                $pattern = "[0-9a-zA-Z]+";
                break;
            case "*":
                $pattern = ".+?";
                break;
            case "p":
                $pattern = "(page-[0-9]++)";
                break;
            default:
                $pattern = "[^/]+?";
                break;
        }

        return $pattern;
    }

	/**
	 * Genrate Route Name
	 *
	 * @param string|null $name
	 * @return string
	 **/
	protected function generateName($name)
	{
		if (!is_null($name)) return $name;

		$prefix = '';
		if (!in_array('ALL', $this->methods)) {
			foreach ($this->methods as $method) {
				$prefix .= strtolower($method).'_';
			}
		}

		$name = preg_replace('/({(.*)})/', '', $this->path);
		$name = str_replace(array('/', '|', '-'), '_', $name);
		$name = preg_replace('/[^a-z0-9A-Z_.]+/', '', $name);
		$name = rtrim($name, '_');

		return $prefix.$name;
	}

	/**
	 * PHP Magic method __toString().
	 * Return route name only
	 *
	 * @return string Route name
	 **/
	public function __toString()
	{
		return $this->name;
	}

} // END class Route
