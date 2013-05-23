<?php

namespace Admin\Model;

class User extends \Eloquent
{
    protected $table = 'users';

    public function getFullnameAttribute()
    {
    	return $this->first_name.' '.$this->last_name;
    }

}
