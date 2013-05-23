<?php

namespace Reborn\Exception;

class ModuleException extends RbException
{
    public function __construct($msg, $code=NULL)
    {
        parent::__construct($msg, $code);
    }
}
