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
     * CSRF key name to get from config
     *
     * @var string
     **/
    protected static $csrf_key;

    /**
     * Class Init
     *
     * @return void
     **/
    public function __construct()
    {
        static::$csrf_key = Config::get('app.security.csrf_key');
    }

    /**
     * CSRF hidden field
     *
     * @param string $key
     * @param boolean $refresh Make refresh the token key
     * @return string
     **/
    public static function CSRField($key = null, $refresh = false)
    {
        if (is_null($key)) {
            $csrf_key = Config::get('app.security.csrf_key');
        } else {
            $csrf_key = $key.'_'.Config::get('app.security.csrf_key');
        }
        $token =  self::CSRFtoken($key, $refresh);
        return Form::hidden($csrf_key, $token);
    }

    /**
     * Return CSFR Key Only
     *
     * @param string $key
     * @param boolean $refresh Make refresh the token key
     * @return string
     **/
    public static function CSRFKeyOnly($key = null, $refresh = false)
    {
        return static::CSRFtoken($key, $refresh);
    }

    /**
     * Generate CSRFToken
     *
     * @param string $key
     * @param boolean $refresh Make refresh the token key
     * @return string
     **/
    protected static function CSRFtoken($key = null, $refresh = false)
    {
        if (is_null($key)) {
            $csrf_key = Config::get('app.security.csrf_key');
        } else {
            $csrf_key = $key.'_'.Config::get('app.security.csrf_key');
        }

        $session = Registry::get('app')->session;

        if ($refresh) {
            static::refreshToken($session, $csrf_key);
        }

        return $session->get($csrf_key);
    }

    /**
     * CSRF check token
     *
     * @return boolean
     **/
    public static function CSRFvalid($key = null)
    {
        $session = Registry::get('app')->session;
        $request = Registry::get('app')->request;

        if (is_null($key)) {
            $csrf_key = Config::get('app.security.csrf_key');
        } else {
            $csrf_key = $key.'_'.Config::get('app.security.csrf_key');
        }

        if ($request->isAjax() && \Input::isPost()) {
            $ckey = $request->headers->get('X-CSRF-Token');
        } else {
            $ckey = \Input::get($csrf_key);
        }

        if ($session->has($csrf_key) && $session->get($csrf_key) == $ckey) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Refresh the CSRF Token
     *
     * @param Symfony\Component\HttpFoundation\Session\Session $session
     * @param string $key CSRF-Key
     * @return void
     **/
    protected static function refreshToken($session, $key)
    {
        $session->remove($key);
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

        $session->set($key,$token);
    }
}
