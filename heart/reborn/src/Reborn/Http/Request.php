<?php

namespace Reborn\Http;

/**
 * HTTP Request Class
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Request extends \Symfony\Component\HttpFoundation\Request
{
    /**
     * Variable for request is inner request or not
     *
     * @var boolean
     **/
    public $inner = false;

    /**
     * Variable for active module
     *
     * @var string
     **/
    public $module;

    /**
     * Variable for active controller
     *
     * @var string
     **/
    public $controller;

    /**
     * Variable for active action
     *
     * @var string
     **/
    public $action;

    /**
     * Set the request is inner request.
     * This is use for View's Action calling process.
     *
     * @return void
     **/
    public function setInner()
    {
        $this->inner = true;
    }

    /**
     * Request is Inner Call or not
     *
     * @return boolean
     **/
    public function isInner()
    {
        return $this->inner;
    }

    /**
     * Check the request is Ajax or not
     *
     * @return bool
     **/
    public function isAjax()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Get the Base URL
     *
     * @return string
     */
    public function baseUrl()
    {
        return $this->getSchemeAndHttpHost().$this->getBasePath().'/';
    }

    /**
     * Get the request Url
     *
     * @return string
     **/
    public function requestUrl()
    {
        return $this->getUri();
    }

    /**
     * Get the subdomain from request URI
     *
     * @return string|boolean
     **/
    public function subdomain()
    {
        $host = explode('.', $this->getHost());

        if (count($host) > 2) {
            return $host[0];
        }

        return false;
    }

} // END Request class
