<?php

namespace Reborn\Fileupload\Exception;

use RbException;

/**
 * Exception for input/output
 *
 * @package Fileupload\Exception
 * @author RebornCMS Development Team
 **/
class IOException extends RbException
{

    /**
     * Constructor method for IOException
     *
     * @return void
     **/
    public function __construct($message = null, $code = null)
    {

        if (is_null($message)) {
            $messge = $this->message;
        }

        parent::__construct($message, $code);

    }

} // END class IOException extends RbException
