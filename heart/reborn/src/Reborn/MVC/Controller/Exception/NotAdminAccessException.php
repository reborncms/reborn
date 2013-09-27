<?php

namespace Reborn\MVC\Controller\Exception;


/**
 * Exception for Not Admin Aceess Permission
 *
 * @package Reborn\MVC\Controller
 * @author MyanmarLinks Professional Web Development Team
 **/
class NotAdminAccessException extends NotAuthException
{

	public function __construct($code=NULL)
    {
        parent::__construct("You don't have permission for admin panel!", 401);
    }

} // END class NotAuthException extends RbException
