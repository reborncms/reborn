<?php

namespace Reborn\Fileupload\Exception;

/**
 * Exception for directory not found
 *
 * @package Fileupload\Exception
 * @author RebornCMS Development Team
 **/
class DirectoryNotFoundException extends IOException
{

    /**
     * Exception message
     *
     * @var string
     **/
    protected $message = 'Directory is not found!';

} // END class DirectoryNotFoundException extends IOException
