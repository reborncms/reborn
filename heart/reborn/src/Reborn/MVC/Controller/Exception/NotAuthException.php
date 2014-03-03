<?php

namespace Reborn\MVC\Controller\Exception;

use Reborn\Exception\RbException;

/**
 * Exception for Not Authenticate Access
 *
 * @package Reborn\MVC\Controller
 * @author MyanmarLinks Professional Web Development Team
 **/
class NotAuthException extends RbException
{

    public function __construct($code=NULL)
    {
        parent::__construct("You don't have permission for this process!", 401);
    }

} // END class NotAuthException extends RbException
