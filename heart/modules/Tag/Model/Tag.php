<?php

namespace Tag\Model;

class Tag extends \Eloquent
{
    protected $table = 'tags';

    protected $fillable = array('name');

    public $timestamps = false;

}
