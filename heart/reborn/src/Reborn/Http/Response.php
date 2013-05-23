<?php

namespace Reborn\Http;

/**
 * HTTP Response Class
 *
 * @package Reborn\Http
 * @author Myanmar Links Professional Web Development Team
 **/
class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * Check the request is Ajax or not
     *
     * @return bool
     **/
    public function isAjax()
    {
        return $this->isXmlHttpRequest();
    }
} // END Request class
