<?php

namespace Blog\Model;

class Blog extends \Eloquent
{
    protected $table = 'blog';

    public $timestamps = false;

    protected $fillable = array('view_count');

    public function category()
    {
        return $this->belongsTo('Blog\Model\BlogCategory');
    }

    public function author()
    {
    	return $this->belongsTo('\User\Model\User');
    }

}
