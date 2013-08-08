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
     * @return string
     **/
    public static function CSRField($key = null)
    {
        if (is_null($key)) {
            $csrf_key = Config::get('app.security.csrf_key');
        } else {
            $csrf_key = $key.'_'.Config::get('app.security.csrf_key');
        }
        $token =  self::CSRFtoken($key);
        return Form::hidden($csrf_key, $token);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public static function CSRFKeyOnly($key = null)
    {
        return static::CSRFtoken($key);
    }

    /**
     * Generate CSRFToken
     *
     * @return string
     **/
    protected static function CSRFtoken($key = null)
    {
        if (is_null($key)) {
            $csrf_key = Config::get('app.security.csrf_key');
        } else {
            $csrf_key = $key.'_'.Config::get('app.security.csrf_key');
        }

        $csrf_expire = Config::get('app.security.csrf_expiration');
        $session = Registry::get('app')->session;
        $expiretime = time() - $session->getMetadataBag()->getCreated();
        if ($session->has($csrf_key) && ($expiretime < $csrf_expire)) {
            return $session->get($csrf_key);
        }
        else {
            $session->remove($csrf_key);
            $encypt_method = Config::get('app.security.token_encrypt');
            $token = $encypt_method(uniqid(rand(), true));
            $session->set($csrf_key,$token);
            return $token;
        }
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
}
