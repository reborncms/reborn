<?php

namespace Reborn\Route;

use Colsure;
use Reborn\Http\Uri;
use Reborn\Util\Str;
use Reborn\File\Directory as Dir;
use Reborn\File\File;
use Module;

/**
 * Route Map Class for Reborn
 *
 * @package Reborn\Route
 * @author Myanmar Links Professional Web Development Team
 **/
class Map
{
	/**
	 * Variable for the route's name
	 *
	 * @var string
	 **/
	protected $name;

	/**
	 * Variable for the route's path (URI)
	 *
	 * @var string
	 **/
	protected $path;

	/**
	 * Variable for the route's Controller File Name
	 *
	 * @var string
	 **/
	protected $file;

	/**
	 * Variable for Route's Closure Method
	 *
	 * @var Colsure
	 **/
	public $closure;

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
	 * Variable for the request method for this route
	 *
	 * @var string
	 **/
	protected $method;

	/**
	 * Variable for Route Pattern Replacer
	 *
	 * @var array
	 **/
	protected $replacer = array(
			'{:any}'	=> '(.*?)',
			'{:int}'	=> '([0-9]+)',
			'{:alpha}'	=> '([a-zA-Z\.\-_]+)',
			'{:alnum}'	=> '([a-zA-Z0-9\.\-_]+)',
			'/{:?}'		=> '(?:/(.*))',
			'{:?}'		=> '(?:(.*))'
		);

	/**
	 * Add the route. (Create the new route map object)
	 *
	 * @param string $name Route name
	 * @param string $path URI Path for this route
	 * @param string $file Controller File for this route
	 * @param string $method Request method for this route,
	 * 					default is ALL
	 * @return Reborn\Route\Map
	 **/
	public function add($name, $path, $file, $method = 'all')
	{
		$this->name = $name;
		$this->path = ($path == '/') ? $path :trim($path, '/');

		// We Test File is Closure Method or not
		if (is_string($file)) {
			$this->file = '\\'.ltrim($file, '\\');
		} else {
			$this->closure = $file;
		}

		$this->method = strtoupper($method);

		return $this;
	}

	/**
	 * Match the route map base on URI.
	 *
	 * @param string $uri URI from request
	 * @return boolean
	 **/
	public function match($uri)
	{
		$verb = \Input::method();

		// Check the HTTP verb is match or not
		if (($this->method != 'ALL') and ($this->method != $verb)) {
			return false;
		}

		// Match from path
		if ($this->matcher($uri)) {
			$this->parseController();
			return true;
		}

		return false;
	}

	/**
	 * Parse the $this->path to Uri string
	 *
	 * @return void
	 **/
	public function parseToUri($data = array())
	{
		if (empty($data)) {
			return $this->path;
		}

		if (preg_match_all('/\{(:(.*?))?\}/', $this->path, $matches)){
			$search = $repalce = array();
			foreach ($matches[0] as $k => $m) {
				$search[] = $m;
				$replace[] = isset($data[$k]) ? $data[$k] : $m;
			}
		} else {
			return $this->path;
		}

		return str_replace($search, $replace, $this->path);
	}

	/**
	 * Matcher the uri and this route's pattern(path)
	 *
	 * @param string $uri
	 * @return boolean
	 **/
	protected function matcher($uri)
	{
		if ($this->path == $uri) {
			return true;
		}

		$search = array_keys($this->replacer);
		$replace = array_values($this->replacer);

		$path = str_replace($search, $replace, $this->path);

		if (preg_match('#^'.$path.'?$#', $uri, $matchValue)) {
			$this->params = array_slice($matchValue, 1);
			return true;
		}
		return false;
	}

	/**
     * Return the Regex Pattern base on given type
     *
     * @param string $type Regex Type
     * @return string
     */
    protected function regexPattern($type)
    {
        $pattern = '';
        switch (strtolower($type))
        {
            case "int":
                $pattern = "([0-9]+)";
                break;
            case "alpha":
                $pattern = "([a-zA-Z\.\-_]+)";
                break;
            case "alnum":
                $pattern = "([0-9a-zA-Z\.\-_]+)";
                break;
            case "optional":
            	$pattern = "(?:/(.*))";
            	break;
            default:
                $pattern = "(.*)";
                break;
        }

        return $pattern;
    }

    /**
     * Parse the this->file to Module, Controller, Action
     *
     * @return void
     **/
	protected function parseController()
	{
		// If Match Route's File is null, Don't make parese Controller
		if (is_null($this->file)) {
			return true;
		}

		if (false != strpos($this->file, '::')) {
			$arr = explode('::', $this->file);
			$namespace = explode('\\', $arr[0], 3);
			$this->module = $namespace[1];
			$this->controller = $namespace[2];
			$this->action = Str::camel($arr[1]);
		} else {
			$namespace = explode('\\', $this->file, 3);
			$this->module = $namespace[1];
			$this->controller = $namespace[2];
			$this->action = 'index';
		}
	}
}
