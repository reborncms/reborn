<?php

namespace Reborn\Config;

use Reborn\Cores\Facade;

/**
 * Config Facade class for Reborn
 *
 * @package Reborn\Config
 * @author Myanmar Links Professional Web Development Team
 **/
class ConfigFacade extends Facade
{
    /**
     * Get config instance.
     *
     * @return \Reborn\Config\Config
     **/
    public static function getInstance()
    {
        return static::$app['config'];
    }
}
