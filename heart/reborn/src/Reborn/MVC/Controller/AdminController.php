<?php

namespace Reborn\MVC\Controller;

use Reborn\MVC\Controller\Controller;
use Reborn\Cores\Setting;
use Reborn\Config\Config;
use Reborn\Http\Redirect;
use Reborn\Connector\Sentry\Sentry;

/**
 * Admin controller for Reborn
 *
 * @package Reborn\MVC\Controller
 * @author Myanmar Links Professional Web Development Team
 **/
class AdminController extends Controller
{
    // Active Module
    protected $module;

    // Menu Object
    protected $menu;

    /**
     * Constructor Method
     *
     * @return void
     **/
    public function __construct()
    {
        if (! defined('ADMIN')) {
            define('ADMIN', true);
        }

        if (! defined('ADMIN_URL')) {
            define('ADMIN_URL', $this->getAdminLink());
        }

        parent::__construct();

        $this->checkAuthentication();

        $this->module = \Registry::get('app')->request->module;

        $this->varSetter();

        \Translate::load('label');
        \Translate::load('global');
        \Translate::load('navigation');
    }

    /**
     * After Method for controller.
     * This method will be call after request action.
     */
    public function after($response)
    {
        return parent::after($response);
    }

    /**
     * Get the Admin Panel Link
     *
     * @return string
     */
    protected function getAdminLink()
    {
        $db = Setting::get('adminpanel_url');
        $config = Config::get('app.adminpanel');

        if ($db != $config) {
            Config::set('app.adminpanel', $db);
        }

        return Config::get('app.adminpanel');
    }

    /**
     * Check the Authentication and Permission for Admin Panel
     *
     * @return boolean
     **/
    protected function checkAuthentication()
    {
        $allow = array(ADMIN_URL.'/login', ADMIN_URL.'/logout');
        $current = rtrim(implode('/', \Uri::segments()), '/');

        if (!Sentry::check()) {
            if (in_array($current, $allow)) {
                return true;
            }
            return Redirect::to(ADMIN_URL.'/login');
        } else {
            $user = Sentry::getUser();

            if ( ! $user->hasAccess('admin')) {
                Sentry::logout();
                \Flash::error(t('global.not_ap_access'));
                return Redirect::to(ADMIN_URL.'/login');
            }

            return true;
        }

        return true;
    }

    /**
     * Set the variables for admin panel.
     *
     * @return void
     **/
    protected function varSetter()
    {
        // Set the Admin panel URI key
        $this->template->adminUrl = ADMIN_URL;

        // Set the admin panel menu
        $this->template->adminMenus = $this->getMenu();

        // Set the current User
        $user = Sentry::getUser();
        $this->template->user = $user;

        // Set the Site Title
        $this->template->siteTitle = \Setting::get('site_title');

        // Set the Reborn Version and URL
        $this->template->rebornVersion = \Reborn\Cores\Version::FULL;
        $this->template->rebornUrl = \Reborn\Cores\Version::URL;

        // Set the Reborn Usage
        $this->template->usageTime = \Reborn\Cores\Profiler::getTime();
        $this->template->usageMem = \Reborn\Cores\Profiler::getMemory();

        // Set the active module
        $toolbar = \Module::moduleToolbar($this->module);
        $this->template->set('modToolbar', $toolbar);
        $module = \Module::getData($this->module);
        $this->template->set('module', $module);

        $start_year = '2012';
        $end_year = date('Y');
        if ($start_year == $end_year ) {
            $copyright = 'Copyright &copy; '.$end_year;
        } else {
            $copyright = 'Copyright &copy; '.$start_year.' - '.$end_year;
        }
        // Set the copyright date
        $this->template->copyRight = $copyright;

        // Set Main Stylesheet for Admin Panel
        /*$styles = array('plugins/tipsy.css',
                        'plugins/colorbox/colorbox.css',
                        //'reborn_2.css',
                        //'metro/metro.css',
                        //'metro/table.css',
                        //'metro/form.css',
                        //'metro/button.css',
                        //'dashboard.css'
                    );*/
        //$this->template->style($styles);

        // Set Main JS for Admin Panel
        $js = array('jquery.min.js',
                    'plugins/jquery-ui.min.js',
                    'plugins/jquery.colorbox.js',
                    'plugins/jquery-plugins.js',
                    'reborn.js'
                );
        //$this->template->script($js);
    }

    /**
     * Get the Menu Object
     *
     * @return \Reborn\Util\Menu;
     **/
    protected function getMenu()
    {
        $this->menu = new \Reborn\Util\Menu();

        return $this->menu;
    }

} // END class AdminController extends Controller
