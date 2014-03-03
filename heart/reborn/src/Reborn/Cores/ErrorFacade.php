<?php

namespace Reborn\Cores;

/**
 * Error Facade Class
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class ErrorFacade extends Facade
{
    /**
     * Get Error Handler Instance to Bind the Error
     *
     * @return \Reborn\Cores\ErrorHandler
     */
    protected static function getInstance()
    {
        return static::$app['error_handler'];
    }

} // END class ErrorFacade extends Facade
