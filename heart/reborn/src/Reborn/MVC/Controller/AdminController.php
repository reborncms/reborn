<?php

namespace Reborn\MVC\Controller;

use Auth, Flash, Module, Translate;
use Reborn\Cores\Setting;
use Reborn\Http\Redirect;
use Reborn\MVC\Controller\Exception\NotAuthException;
use Reborn\MVC\Controller\Exception\NotAdminAccessException;

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
     * Initial Method for this contoller
     *
     * @return void
     **/
    protected function init()
    {
        if (! defined('ADMIN')) {
            define('ADMIN', true);
        }

        if (! defined('ADMIN_URL')) {
            define('ADMIN_URL', $this->getAdminLink());
        }

        // Set AdminPanel Lanaguage
        $lang = $this->app->session->get('reborn_dashboard_language', 'en');
        $this->app->setLocale($lang);

        Translate::load('label');
        Translate::load('global');
        Translate::load('navigation');

        // Set the Reborn Version and URL
        $this->template->rebornVersion = \Reborn\Cores\Version::FULL;
        $this->template->rebornUrl = \Reborn\Cores\Version::URL;

        $this->checkAuthentication();

        $this->module = $this->request->module;

        $this->varSetter();

        if (Auth::check()) {
            $user = Auth::getUser();

            // Check for Module Access
            if (!$user->hasAccess(strtolower($this->request->module))) {
                throw new NotAuthException();
            }
        }
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
        $config = $this->app['config']->get('app.adminpanel');

        if ($db != $config) {
            $this->app['config']->set('app.adminpanel', $db);
        }

        return $this->app['config']->get('app.adminpanel');
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

        if (!Auth::check()) {

            if (in_array($current, $allow)) {
                return true;
            }

            // Don't make Redirect. Because Security Problem
            throw new NotAuthException();
        } else {
            $user = Auth::getUser();

            // We are check user hasAccess for Admin Panel
            if ( ! $user->hasAccess('admin')) {
                Auth::logout();
                Flash::error(t('global.not_ap_access'));
                throw new NotAdminAccessException();
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

        // Set Language Code
        $lang = $this->app->session->get('reborn_dashboard_language', 'en');
        $this->template->lang = $lang;

        // Set the current User
        $user = Auth::getUser();
        $this->template->login_user = $user;

        // Set the Site Title
        $this->template->siteTitle = Setting::get('site_title');

        // Set the active module
        $toolbar = Module::moduleToolbar($this->module);
        $this->template->set('modToolbar', $toolbar);
        $module = Module::get($this->module);

        $this->template->set('module', $module);

        $start_year = '2012';
        $end_year = date('Y');
        $cp_right = t('global.rb_copyright');
        if ($start_year == $end_year ) {
            $copyright = $cp_right.$end_year;
        } else {
            $copyright = $cp_right.$start_year.' - '.$end_year;
        }
        // Set the copyright date
        $this->template->copyRight = $copyright;
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
