<?php

namespace Comment\Model;

class Comments extends \Eloquent
{
    protected $table = 'comments';

    protected $softDelete = false;

    public function __construct(array $attributes = array()) {

        if (\Module::getData('comment', 'dbVersion') >= 1.1) {

            $this->softDelete = true;

        }

        parent::__construct($attributes);
    }

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
