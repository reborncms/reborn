<?php

namespace Field\Model;

class FieldData extends \Eloquent
{
    protected $table = 'field_data';

    public $timestamps = false;

    // Validation Rules
    protected $rules = array(
            'field_id' => 'required',
            'post_id' => 'required',
            'group_id' => 'required'
        );

}
