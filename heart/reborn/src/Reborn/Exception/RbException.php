<?php

namespace Reborn\Exception;

/**
 * RbException class. Extend the default Exception class
 *
 * @package Reborn\Exception
 * @author Myanmar Links Professional Web Development Team
 **/
class RbException extends \RuntimeException
{

    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

} // END class RbException extends \RuntimeException
