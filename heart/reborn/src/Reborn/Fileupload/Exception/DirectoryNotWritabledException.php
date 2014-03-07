<?php

namespace Reborn\Fileupload\Exception;

/**
 * Exception for no writabled directory
 *
 * @package Fileupload\Exception
 * @author RebornCMS Development Team
 **/
class DirectoryNotWritabledException extends IOException
{

    /**
     * Exception message
     *
     * @var string
     **/
    protected $message = 'Directory is not writabled!';

} // END class DirectoryNotWritabledException extends IOException
