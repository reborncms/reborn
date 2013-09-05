<?php

namespace Reborn\Http;

/**
 * Uri Class for Reborn
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Uri
{

    /**
     * Variable for URI segments
     *
     * @var array
     **/
    protected static $segments = array();

    /**
     * Variable for Request Object
     *
     * @var object
     **/
    protected static $request;

    /**
     * undocumented function
     *
     * @return void
     **/
    public static function initialize(Request $request)
    {
        static::$request = $request;

        static::segmentsCompile();
    }

    /**
     * Get the key no from URI segmetns array.
     * If given no is isset in segments array, return $key's value
     * If not isset key, return null
     * example:
     * <code>
     *      // uri is - blog/category/test
     *      var_dump(Uri::segment(1));
     *      // Output is - blog
     *      var_dump(Uri::segment(2));
     *      // Output is - category
     *      var_dump(Uri::segment(4));
     *      // Output is - null
     * </code>
     *
     * @param string $key URI segment array key no
     * @return string|null
     **/
    public static function segment($key)
    {
        $key = $key - 1;
        if (! is_int($key) || $key < 0) return null;

        return isset(static::$segments[$key]) ? static::$segments[$key] : null;
    }

    /**
     * Get the URI segments array
     *
     * @return array
     **/
    public static function segments()
    {
        return static::$segments;
    }

    /**
     * Get First Uri Segment
     *
     * @return string|null
     **/
    public static function first()
    {
        return reset(static::$segments);
    }

    /**
     * Get Last Uri Segment.
     *
     * @param boolean $number If this param is true, return segment number
     * @return string|integer|null
     **/
    public static function last($number = false)
    {
        if ($number) {
            return count(static::$segments);
        }
        return end(static::$segments);
    }

    /**
     * Check given URI segment name has in given segment key.
     * example:
     * <code>
     *      // uri is - blog/category/test
     *      Uri::hasInSegment('blog', 1);
     *      // return true;
     *      Uri::hasInSegment('category', 2);
     *      // return true;
     *      Uri::hasInSegment('blog', 2);
     *      // return false;
     * </code>
     *
     * @param string $name Name of the segment
     * @param int $key Segment array key number
     * @return boolean
     */
    public static function hasInSegment($name, $key)
    {
        if (! isset(static::$segments[$key - 1])) return false;

        $val = static::$segments[$key - 1];

        return ($name == $val);
    }

    /**
     * Convert given array or object to URI string.
     * example:
     * <code>
     *      $data = array('product', 'category', 'mobile');
     *      $uri = convertUri($data);
     *      // output is - product/category/mobile
     *
     *      // If you want to full URL
     *      // Your host name is http://localhost
     *      $fullurl = convertUri($data, true);
     *      // Output is - http://localhost/product/category/mobile
     *      // If your host is sub dir eg: http://localhost/myapp
     *      // Output is - http://localhost/myapp/product/category/mobile
     * </code>
     *
     * @param array|object $array Data Array (or) Object
     * @param boolean $fullURL If this value is true, return full URL (with host name)
     * @return string
     **/
    public static function arrayToUri($array = array(), $fullURL = false)
    {
        if (! is_array($array)) {
            $array = (array)$array;
        }

        $string = implode('/', $array);

        if ($fullURL) {
            return static::$request->baseUrl().$string;
        }

        return $string;
    }

    /**
     * Get Current Uri (Orginal is array) to URI String.
     *
     * @param int $offset URI Segment Offset
     * @param int|null $length Return Length for URI String
     * @return string
     **/
    public static function uriString($offset = 1, $length = null)
    {
        $offset = (int)$offset - 1;

        $uri = array_slice(static::$segments, $offset, $length);

        return static::arrayToUri($uri);
    }

    /**
     * Get the current URL
     *
     * @return string
     **/
    public static function current()
    {
        return static::arrayToUri(static::$segments, true);
    }

    /**
     * Create given path to the full url.
     *
     * @param string $path
     * @return string
     **/
    public static function create($path = '')
    {
        $request = \Registry::get('app')->request;

        $path = trim($path, '/');
        if ($path == '' || $path =='/') {
            $url = $request->baseUrl();
        } else {
            $path = str_replace(' ', '+', $path);
            $url = $request->baseUrl().$path.'/';
        }

        return $url;
    }

    /**
     * Complie URI string to URI segments array
     *
     * @return void
     **/
    protected static function segmentsCompile()
    {
        $url = static::$request->getPathInfo();

        if ($url == '/') {
            static::$segments = array();
        } else {
            $url = rtrim($url, '/');
            $uriArray = explode('/', $url);

            static::$segments = static::setLanguage($uriArray);
        }

        if((isset(static::$segments[0])) and (static::$segments[0] == \Setting::get('adminpanel_url'))) {
            if (! defined('ADMIN')) {
                define('ADMIN', true);
            }
        }
    }

    /**
     * Set the Language for app, if uri's first element is language code
     *
     * @param array $uri
     * @return array
     */
    protected static function setLanguage($uri)
    {
        $supLangs = \Config::get('app.support_langs');

        // Check the uriArray's first element is blank or not
        if ($uri[0] == '') {
            array_shift($uri);
        }

        if (isset($uri[0]) and (2 == strlen($uri[0]))) {
            if(array_key_exists($uri[0], $supLangs)) {
                $lang = $uri[0];
                array_shift($uri);
            } else {
                $lang = \Config::get('app.lang');
            }
        } else {
            $lang = \Config::get('app.lang');
        }

        \Config::set('app.lang', $lang);

        // Start the Translate initialize
        \Translate::initialize(\Config::get('app.lang'));

        return $uri;
    }

} // END class Uri
