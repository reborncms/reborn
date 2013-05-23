<?php

namespace Reborn\Exception;

class HttpNotFoundException extends RbException
{

    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

}
