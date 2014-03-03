<?php

namespace Reborn\Cores;

/**
 * Site Manager Class
 *
 * @package Reborn\Cores
 * @author MyanmarLinks Professional Web Development Team
 **/
class SiteManager
{

    /**
     * All register table prefix lists
     *
     * @var array
     **/
    protected $prefixs = array();

    /**
     * Active site address
     *
     * @var string
     **/
    protected $site;

    /**
     * Application IOC container
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Static method for instance
     *
     * @param  \Reborn\Cores\Application $app
     * @return \Reborn\Cores\Application
     **/
    public static function make(Application $app)
    {
        return new static($app);
    }

    /**
     * Default instance method
     *
     * @param  \Reborn\Cores\Application $app
     * @return void
     **/
    public function __construct(Application $app)
    {
        $this->app = $app;

        $domain = $app->request->getHost();
        $path = $app->request->getBaseUrl();

        $this->site = rtrim($domain.$path, '/');
    }

    /**
     * Check Site is maintain mode or not.
     * But always return true for admin access user and
     * adminpanel login, logout request.
     *
     * @return boolean
     **/
    public function isMaintain()
    {
        $maintain = Setting::get('frontend_enabled');

        if ($this->app['auth_provider']->check()) {
            // Allow to access admin only
            if ($this->app['auth_provider']->hasAccess('admin')) return false;
        } else {
            $allow = array(admin_url(), admin_url('login'), admin_url('logout'));
            $current = url(implode('/', \Uri::segments()));

            if (in_array($current, $allow)) {
                return false;
            }
        }

        if ('disable' !== $maintain) {
            return false;
        }

        return true;
    }

    /**
     * Check multi site process on or off.
     *
     * @return boolean
     **/
    public function isMulti()
    {
        if ( $this->app->runInCli() ) {
            return false;
        }

        return (bool) $this->app['sites']['multi_site'];
    }

    /**
     * Get Table prefix with "_"
     *
     * @return string
     **/
    public function tablePrefix()
    {
        $all = $this->getAllPrefixs();

        if (isset($all[$this->site])) {
            return rtrim($all[$this->site], '_').'_';
        } elseif (isset($all['default'])) {
            return rtrim($all[$this->site], '_').'_';
        }

        return '';
    }

    /**
     * Get all regsiter table prefixs
     *
     * @return array
     **/
    public function getAllPrefixs()
    {
        if (empty($this->prefixs)) {
            $this->prefixs = $this->app->sites['prefix'];
        }

        return $this->prefixs;
    }

} // END class SiteManager
