<?php

namespace User\Model;
use Reborn\Connector\Sentry\Sentry;

class User extends \Eloquent
{
    protected $table = 'users';

     /**
     * Get full name of user
     */
    public function getFullnameAttribute()
    {
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

}
