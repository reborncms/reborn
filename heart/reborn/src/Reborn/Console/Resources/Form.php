<?php

namespace {module}\Extensions\Form;

class {module}Form extends \FormBuilder
{
    /**
     * Set from element fields
     *
     * @access public
     * @return void
     **/
    public function setFields()
    {
        $this->fields = array(
            // Example
            // 'title' => array(
            // 		'type' => 'text',
            // 		'label' => 'Title',
            // 		'info' => 'Info for title field',
            // 		'rule' => 'required|maxLength:200'
            // )
        );

        $this->submit = array('submit' => array(
            'value' => t('global.save'),
            ));
    }
}
