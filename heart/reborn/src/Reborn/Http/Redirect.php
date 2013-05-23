<?php

namespace Reborn\Http;

use Reborn\Route\Route;
use \Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponse;

/**
 * Redirect class for Reborn
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Redirect
{
    /**
     * Redirect to the given url
     *
     * @param string $url
     * @param int $status Response status code
     * @param array $headers Header properties for the Response
     * @return void
     */
    public static function to($url, $status = 302, $headers = array())
    {
        if (0 == strpos($url, "://")) {
            $url = Uri::create($url);
        }

        return static::send($url, $status, $headers);
    }

    /**
     * Redirect to the url with admin panel url
     *
     * @param string $url
     * @param int $status Response status code
     * @param array $headers Header properties for the Response
     * @return void
     **/
    public static function toAdmin($url = '', $status = 302, $headers = array())
    {
        $admin = \Setting::get('adminpanel_url');
        $url = ltrim($url, '/');
        $url = Uri::create($admin.'/'.$url);

        return static::send($url, $status, $headers);
    }

    /**
     * Redirect to the route by name.
     *
     * @return void
     **/
    public static function route($name,
                $data = array(),
                $status = 302,
                $headers = array())
    {
    	$url = Route::getByName($name, $data);

        if (is_null($url)) {
            return null;
        }

    	return static::to($url, $status, $headers);
    }

    /**
     * Redirect to the 404 Route
     *
     * @return void
     **/
    public static function notFound()
    {
        $router = \Registry::get('app')->router;

        return $router->notFound();
    }

    /**
     * Send the Redirect
     *
     * @param string $url
     * @param int $status Response status code
     * @param array $headers Header properties for the Response
     * @return void
     */
    protected static function send($url, $status = 302, $headers = array())
    {
    	$redirect = new RedirectResponse($url, $status, $headers);
        return $redirect->send();
    }

} // END class Redirect
