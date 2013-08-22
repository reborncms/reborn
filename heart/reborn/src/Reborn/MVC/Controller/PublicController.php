<?php

namespace Reborn\MVC\Controller;

use Reborn\MVC\Controller\Controller;
use Reborn\Cores\Setting;
use Reborn\Cores\Version;
use Reborn\Http\Uri;

/**
 * Public controller for Reborn
 *
 * @package Reborn\MVC\Controller
 * @author Myanmar Links Professional Web Development Team
 **/
class PublicController extends Controller
{
    /**
     * Initial Method for this contoller
     *
     * @return void
     **/
    protected function init()
    {
        if (! defined('PUBLIC')) {
            define('PUBLIC', true);
        }

        $module_layout = strtolower($this->request->module);

        if ($this->theme->hasLayout($module_layout)) {
            $this->setLayout($module_layout);
        }

        $this->template->metadata('canonical', Uri::current(), 'link');
        $this->template->metadata('generator', Version::NAME.' - '.Version::FULL , 'meta');
    }

    /**
     * After Method for controller.
     * This method will be call after request action.
     */
    public function after($response)
    {
        return parent::after($response);
    }

} // END class PublicController extends Controller
