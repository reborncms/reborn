<?php

namespace Admin\Model;

class Blog extends \Eloquent
{
    protected $table = 'blog';

    public function author()
    {
    	return $this->belongsTo('\Admin\Model\User');
    }

}
