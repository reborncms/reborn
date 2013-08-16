<?php

namespace Reborn\Util;

use Symfony\Component\HttpFoundation\Session\Session;
use Reborn\Config\Config;
use Reborn\Form\Form;
use Reborn\Cores\Registry;

/**
 * Security class for Reborn
 *
 * @package Util
 * @author Myanmar Links Professional Web Development Team
 **/
class Security
{

    /**
     * Make CSRF Token
     *
     * @return void
     **/
    public static function makeCSRFToken($key = null)
    {
        $csrf_key = static::getKey($key);

        $token = static::getCSRFToken($csrf_key);

        $session = static::getSession();

        $session->set($csrf_key, $token);
    }

    /**
     * Refresh the CSRF Token
     *
     * @param string $key CSRF-Key
     * @return void
     **/
    public static function refreshToken($key = null)
    {
        static::makeCSRFToken($key);
    }

    /**
     * CSRF hidden field
     *
     * @param string $key
     * @param boolean $refresh Make refresh the token key
     * @return string
     **/
    public static function CSRField($key = null)
    {
        $csrf_key = static::getKey($key);

        $token =  self::getCSRFToken($csrf_key);

        return Form::hidden($csrf_key, $token);
    }

    /**
     * CSRF check token
     *
     * @return boolean
     **/
    public static function CSRFvalid($key = null)
    {
        $request = Registry::get('app')->request;

        $session = static::getSession();

        $csrf_key = static::getKey($key);

        if ($request->isAjax() && \Input::isPost()) {
            $ckey = $request->headers->get('X-CSRF-Token');
        } else {
            $ckey = \Input::get($csrf_key);
        }

        if ($session->has($csrf_key) && ($session->get($csrf_key) == $ckey) ) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Get CSRF Token
     *
     * @param string $key
     * @return string
     **/
    protected static function getCSRFToken($key)
    {
        $session = static::getSession();

        if ($session->has($key)) {
            $token = $session->get($key);
        } else {
            $token = static::getHaskToken();
        }

        return $token;
    }

    /**
     * Get CSRF Key Name
     *
     * @param string $key CSRF Key Prefix
     * @return string
     **/
    protected static function getKey($key = null)
    {
        // Set csrf_key
        if (is_null($key)) {
            $csrf_key = Config::get('app.security.csrf_key');
        } else {
            $csrf_key = $key.'_'.Config::get('app.security.csrf_key');
        }

        return $csrf_key;
    }

    /**
     * Get Hashing Value for CSRF Token
     *
     * @return string
     **/
    protected static function getHaskToken()
    {
        //Make CSRF Token
        $encypt_method = Config::get('app.security.token_encrypt');

        if ( ! in_array($encypt_method, array('md5', 'sha1', 'random')) ) {
            $encypt_method = 'hash';
        }

        if ('random' == $encypt_method) {
            $token = md5(\Reborn\Util\Str::random(15));
            $uq = explode('.', uniqid(rand(), true));
            $token = substr($uq[0].$token.$uq[1], 5, 48);
        } else {
            $token = $encypt_method(uniqid(rand(), true));
        }

        return $token;
    }

    /**
     * Get Session Object
     *
     * @return Symfony\Component\HttpFoundation\Session\Session
     **/
    protected static function getSession()
    {
        return Registry::get('app')->session;
    }
}
