<?php

namespace Comment\Model;

class Comments extends \Eloquent
{
    protected $table = 'comments';

    protected $softDelete = false;

    protected $multisite = true;

    public function __construct(array $attributes = array()) {

        if (\Module::get('comment', 'db_version') >= 1.1) {

            $this->softDelete = true;

        }

        parent::__construct($attributes);
    }

    public function children()
    {
        return $this->hasMany('Comment\Model\Comments', 'parent_id', 'id');
    }

    /**
     * Relationship with Author
     */
    public function author()
    {
    	return $this->belongsTo('Reborn\Auth\Sentry\Eloquent\User', 'user_id');
    }

    /**
     * Comment User Name
     */
    public function getAuthorNameAttribute()
    {
        return $this->author->first_name.' '.$this->author->last_name;
    }
        
}
