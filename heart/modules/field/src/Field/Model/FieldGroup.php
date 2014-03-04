<?php

namespace Field\Model;

class FieldGroup extends \Eloquent
{
    protected $table = 'field_groups';

    public $timestamps = false;

    protected $multisite = true;

    // Validation Rules
    protected $rules = array(
            'name' => 'required|maxLength:150',
            'relation' => 'required'
        );

    public function getFieldsAttribute()
    {
        return json_decode($this->attributes['fields']);
    }
}
