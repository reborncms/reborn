<?php
namespace Contact\Model;

class Mail extends \Eloquent
{
    protected $table = 'contact';

    protected $multisite = true;
    
    public $custom_field;
}
