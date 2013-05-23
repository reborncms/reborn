<?php

namespace Reborn\MVC\Controller;

use Reborn\Exception\HttpNotFoundException;

/**
 * Controller Class for Reborn
 *
 * @package Reborn\MVC\Controller
 * @author Myanmar Links Professional Web Development Team
 **/
class Controller extends ControllerContainer
{
    /**
     * Constructor Method
     *
     * @return void
     **/
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Before Method for controller.
     * This method will be call before request action.
     */
    public function before() {}

    /**
     * After Method for controller.
     * This method will be call after request action.
     */
    public function after($response)
    {
        return parent::after($response);
    }

    /**
     * Set the current theme's layout name
     *
     * @param string $name Layout name for current theme
     * @return void
     **/
    protected function setLayout($name)
    {
        $this->template->setLayout($name);
    }

    /**
     * Returh 404 Result
     *
     * @return void
     **/
    protected function notFound()
    {
        $this->setHTTPstatus(404);

        return $this->template->render404();
    }

    /**
     * Set the session flush message
     *
     * @param string $key Flush name
     * @param string $value Flash value
     * @return void
     **/
    protected function flash($key, $value)
    {
        $this->session->getFlashBag()->set($key, $value);
    }

} // END class Controller
