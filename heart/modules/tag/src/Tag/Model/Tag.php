<?php

namespace Tag\Model;

class Tag extends \Eloquent
{
    protected $table = 'tags';

    protected $fillable = array('name');

    protected $multisite = true;

    public $timestamps = false;

}
