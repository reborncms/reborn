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

    /**
     * Scope for post is active
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'live')->where('created_at', '<=', date('Y-m-d H:i:s'));
    }

}
