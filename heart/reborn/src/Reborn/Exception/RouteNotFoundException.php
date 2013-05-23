<?php

namespace Reborn\Exception;

class RouteNotFoundException extends RbException
{

    public function __construct($message, $code=NULL)
    {
        parent::__construct($message, $code);
    }

}
