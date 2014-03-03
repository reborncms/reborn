<?php

namespace Reborn\Auth;

use Reborn\Cores\Facade;

class GroupFacade extends Facade
{
    /**
     * Get User Group Provider instance
     *
     * @return GroupProviderInstance
     **/
    public static function getInstance()
    {
        return static::$app['usergroup_provider'];
    }
}
