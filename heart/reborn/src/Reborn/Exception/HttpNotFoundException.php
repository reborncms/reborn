<?php

namespace Reborn\Exception;

/**
 * Exception class for Http Not Found.
 *
 * @package Reborn\Exception
 * @author Myanmar Links Professional Web Development Team
 **/
class HttpNotFoundException extends RbException
{

    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

}
