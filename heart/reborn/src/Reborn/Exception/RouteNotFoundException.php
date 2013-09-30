<?php

namespace Reborn\Exception;

/**
 * Exception class for Route Not Found.
 *
 * @package Reborn\Exception
 * @author Myanmar Links Professional Web Development Team
 **/
class RouteNotFoundException extends RbException
{

    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

}
