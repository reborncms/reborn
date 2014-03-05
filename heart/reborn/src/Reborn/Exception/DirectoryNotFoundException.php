<?php

namespace Reborn\Exception;

/**
 * Exception class for Directory Not Found.
 *
 * @package Reborn\Exception
 * @author Myanmar Links Professional Web Development Team
 **/
class DirectoryNotFoundException extends RbException
{

    public function __construct($dir, $path = null, $code = null)
    {
        if ( is_null($path) ) {
            $message = sprintf("Directory doesn't exits in given %s.", $dir);
        } else {
            $message = sprintf("{ %s } directory doesn't exits in given %s.", $dir, $path);
        }

        parent::__construct($message, $code);
    }

}
