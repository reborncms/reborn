<?php

namespace Tag\Model;

class TagsRelationship extends \Eloquent
{
    protected $table = 'tags_relationship';

    protected $fillable = array('tag_id', 'object_id', 'object_name');

    public $timestamps = false;

    public function tag()
    {
        return $this->belongsTo('Tag\Model\Tag');
    }

    public function blog()
    {
    	return $this->belongsTo('\Blog\Model\Blog', 'object_id');
    }

}