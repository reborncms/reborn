<?php

namespace Reborn\Exception;

/**
 * Exception class for Module.
 *
 * @package Reborn\Exception
 * @author Myanmar Links Professional Web Development Team
 **/
class ModuleException extends RbException
{
    public function __construct($msg, $code=NULL)
    {
        parent::__construct($msg, $code);
    }
}
