<?php

namespace Navigation\Model;

class Navigation extends \Eloquent
{
    protected $table = 'navigation';

    public $timestamps = false;

    public function links()
	{
		return $this->hasMany('Navigation\Model\NavigationLinks');
	}

}
