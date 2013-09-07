<?php

namespace Field\Model;

class Field extends \Eloquent
{
    protected $table = 'fields';

    public $timestamps = false;

    // Validation Rules
    protected $rules = array(
            'field_name' => 'required|maxLength:250',
            'field_slug' => 'required',
            'field_type' => 'required'
        );

}
