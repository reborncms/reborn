<?php

namespace Reborn\Http;

use Reborn\Route\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     **/
    public static function toAdmin($url = '', $status = 302, $headers = array())
    {
        $admin = \Setting::get('adminpanel_url');
        $url = ltrim($url, '/');
        $url = Uri::create($admin.'/'.$url);

        return static::send($url, $status, $headers);
    }

    /**
     * Redirect to the back url. (HTTP_REFERER)
     *
     * @param int $status Response status code
     * @param array $headers Header properties for the Response
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     **/
    public static function back($status = 302, $headers = array())
    {
        $url = static::getRequest()->headers->get('referer');

        return static::send($url, $status, $headers);
    }

    /**
     * Redirect to the url with module prefix
     *
     * @param string $url
     * @param boolean $admin With admin panel prefix
     * @param int $status Response status code
     * @param array $headers Header properties for the Response
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     **/
    public static function module($url = '', $admin = true, $status = 302, $headers = array())
    {
        $module = \Module::get(static::getRequest()->module, 'uri');
        $url = ltrim($url, '/');

        if ($admin) {
            $admin = \Setting::get('adminpanel_url');
            $url = $admin.'/'.$module.'/'.$url;
        } else {
            $url = $module.'/'.$url;
        }

        $url = Uri::create($url);

        return static::send($url, $status, $headers);
    }

    /**
     * Redirect to the route by name.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     **/
    public static function route($name,
                $data = array(),
                $status = 302,
                $headers = array())
    {
    	$url = \Route::getUrlByRouteName($name, $data);

        if (is_null($url)) {
            return null;
        }

    	return static::to($url, $status, $headers);
    }

    /**
     * Create and Send the Redirect
     *
     * @param string $url
     * @param int $status Response status code
     * @param array $headers Header properties for the Response
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public static function send($url, $status = 302, $headers = array())
    {
    	$redirect = new RedirectResponse($url, $status, $headers);
        return $redirect->send();
    }

    /**
     * Get Request instance
     *
     * @return \Reborn\Http\Request
     **/
    protected static function getRequest()
    {
        return \Facade::getApplication()->request;
    }

} // END class Redirect
