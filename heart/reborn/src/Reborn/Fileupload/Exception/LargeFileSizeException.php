<?php

namespace Reborn\Fileupload\Exception;

use RbException;

/**
 * Exception for huge file size
 *
 * @package Fileupload\Exception
 * @author RebornCMS Development Team
 **/
class LargeFileSizeException extends RbException
{

    /**
     * Exception message
     *
     * @var string
     **/
    protected $message = 'File size is too large to upload';

} // END class LargeFileSizeException extends RbException
