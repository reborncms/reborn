<?php

namespace User\Model;
use Cartalyst\Sentry\Users\Eloquent\User as Base;

class User extends Base
{
    /**
     * Get fullname of user by combining First Name and Last Name
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return $this->attributes['first_name'].' '.$this->attributes['last_name'];
    }

}
