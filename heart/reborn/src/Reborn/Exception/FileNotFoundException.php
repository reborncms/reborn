<?php

namespace Reborn\Exception;

class FileNotFoundException extends RbException
{

    public function __construct($file, $path, $code=NULL)
    {
        $message = sprintf("{ %s } file doesn't exits in given %s.", $file, $path);
        parent::__construct($message, $code);
    }

}
