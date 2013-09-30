<?php

namespace Reborn\Exception;

/**
 * Exception class for File Not Found.
 *
 * @package Reborn\Exception
 * @author Myanmar Links Professional Web Development Team
 **/
class FileNotFoundException extends RbException
{

    public function __construct($file, $path, $code=NULL)
    {
        $message = sprintf("{ %s } file doesn't exits in given %s.", $file, $path);
        parent::__construct($message, $code);
    }

}
