<?php

namespace Reborn\Http;

use Reborn\Cores\Registry as Registry;

/**
 * Input Class for Reborn
 *
 * You can use to pick up HTTP Request ($_GET, $_POST, $_SERVER).
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Input
{

    /**
     * Get the Request Instance
     *
     * @return Reborn\Http\Request
     */
    public static function getIns()
    {
        return Registry::get('app')->request;
    }

    /**
     * Get request method is POST or GET
     *
     * @return string
     **/
    public static function method()
    {
        return static::getIns()->getMethod();
    }

    /**
     * Check the request method is POST
     *
     * @return boolean
     */
    public static function isPost()
    {
        return ('POST' == static::getIns()->getMethod());
    }

    /**
     * Check the request method is GET
     *
     * @return boolean
     */
    public static function isGet()
    {
        return ('GET' == static::getIns()->getMethod());
    }

    /**
     * Get request param from $_GET (or) $_POST.
     * $_GET (or) $_POST is base on request method.
     * If you want to get all Input Field, use '*' key.
     * example:
     * <code>
     *      // Return all input field
     *      Input::get('*');
     *      // Return input field by key username
     *      Input::get('username');
     *      // Return default value when the key name is null
     *      Input::get('age', 26);
     * </code>
     *
     * @param string $key
     * @param mixed $default Default value if request key is not set
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if ($key == '*') {
            return static::getAllInput();
        }

        return static::getByInputMethod($key, $default);
    }

    /**
     * Return all input value are json format
     *
     * @return string
     **/
    public static function json()
    {
        return json_encode(static::get('*'));
    }

    /**
     * Get request file information. Same with ($_FILES).
     *
     * @param string $key
     * @param mixed $default Default value if request key is not set
     * @return mixed
     **/
    public static function file($key, $default = null)
    {
        return static::getIns()->files->get($key, $default);
    }

    /**
     * Get request server information. Same with ($_SERVER).
     * <code>
     *      // will return "localhost" at local server
     *      echo Input::server('HTTP_HOST');
     *      // OR
     *      echo Input::server('http_host');
     * </code>
     *
     * @param string $key
     * @param mixed $default Default value if request key is not set
     * @return mixed
     **/
    public static function server($key, $default = null)
    {
        if ($key == '*') {
            return $_SERVER;
        }
        $key = strtoupper($key);

        return static::getIns()->server->get($key, $default);
    }

    /**
     * Get client IP
     *
     * @return string
     **/
    public static function ip()
    {
        return static::getIns()->getClientIp();
    }

    /**
     * Get the HTTP schema
     *
     * @return string
     **/
    public static function scheme()
    {
        return static::getIns()->getScheme();
    }

    /**
     * Returns the user form $_SERVER['PHP_AUTH_USER'].
     *
     * @return string|null
     */
    public static function user()
    {
        return static::getIns()->getUser();
    }

     /**
     * Returns the password form $_SERVER['PHP_AUTH_PW'].
     *
     * @return string|null
     */
    public static function password()
    {
        return static::getIns()->getPassword();
    }

    /**
     * Checks whether the request is secure or not.
     *
     * @return boolean
     **/
    public static function isSecure()
    {
        return static::getIns()->isSecure();
    }

    /**
     * Get the all input field from the POST or GET.
     * Doesn't include FILE.
     *
     * @return array
     **/
    protected static function getAllInput()
    {
        $r = array();
        if (static::isPost()) {
            $results = static::getIns()->request;
        } else {
            $results = static::getIns()->query;
        }

        foreach ($results as $k => $v) {
            $r[$k] = static::sanitize($v);
        }

        return $r;
    }

    /**
     * This method decide ($request[$_POST] or $query[$_GET]) base on request method
     *
     * @param string $key
     * @param mixed $default Default value if request key is not set
     * @return mixed
     */
    protected static function getByInputMethod($key, $default = null)
    {
        if (static::isPost()) {
            $v = static::getIns()->request->get($key, $default);
        } else {
            $v = static::getIns()->query->get($key, $default);
        }

        return static::sanitize($v);
    }

    /**
     * Clean the Input Value
     *
     * @param mixed $value
     * @return mixed
     */
    protected static function sanitize($value)
    {
        if (is_string($value)) {
            $value = htmlentities($value, ENT_QUOTES, "UTF-8");
        } elseif (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = static::sanitize($v);
            }
        }

        return $value;
    }
}
