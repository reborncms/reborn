<?php
namespace Contact\Model;

class Mail extends \Eloquent
{
    protected $table = 'contact';

    protected $multisite = true;

    public $custom_field;


    /**
     * Check Mail
     */
    public function getCheckMailAttribute()
    {
    	if ($this->attributes['read_mail'] == 0) {
    		return $this->attributes['subject'] .' <span class="label label-success">'.t('contact::contact.labels.new').'</span>';
    	}
        return $this->attributes['subject'];
    }
}
