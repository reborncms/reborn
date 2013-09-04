<?php

namespace Comment\Model;

class Comments extends \Eloquent
{
    protected $table = 'comments';

    /**
     * Relationship with Author
     */
    public function author()
    {
    	return $this->belongsTo('\User\Model\User', 'user_id');
    }

    /**
     * Comment User Name
     */
    public function getAuthorNameAttribute()
    {
        return $this->author->first_name.' '.$this->author->last_name;
    }
        
}
