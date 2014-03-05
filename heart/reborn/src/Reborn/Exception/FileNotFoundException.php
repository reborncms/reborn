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

    public function __construct($file, $path = null, $code = null)
    {
        if ( is_null($path) ) {
            $message = sprintf("File doesn't exits in given %s.", $file);
        } else {
            $message = sprintf("{ %s } file doesn't exits in given %s.", $file, $path);
        }

        parent::__construct($message, $code);
    }

}
