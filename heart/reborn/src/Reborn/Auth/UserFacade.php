<?php

namespace Reborn\Auth;

use Reborn\Cores\Facade;

class UserFacade extends Facade
{
    /**
     * Get User Provider instance
     *
     * @return UserProviderInstance
     **/
    public static function getInstance()
    {
        return static::$app['user_provider'];
    }
}
