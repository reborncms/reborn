<?php

namespace Reborn\Fileupload\Exception;

/**
 * Exception for directory cannot be created
 *
 * @package Fileupload\Exception
 * @author RebornCMS Development Team
 **/
class DirectoryNotCreatedException extends IOException
{

    /**
     * Exception message
     *
     * @var string
     **/
    protected $message = 'Directory cannot be created!';

} // END class DirectoryNotCreatedException extends IOException
