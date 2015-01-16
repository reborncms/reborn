<?php

namespace Contact\Extensions\Form;

/**
 * Contact Admin Email Template Form 
 *
 * @author RebornCMS Development Team
 * @package Contact\Extensision\Form
 * @link http://www.reborncms.com
 **/
class EmailTemplateForm extends \FormBuilder
{

    protected $model;

    protected $data;

    protected $method;

    /**
     * Set from element fields
     *
     * @access public
     * @return void
     **/
    public function setFields()
    {
        $this->fields = array(

            'name' => array(
                    'type' => 'text',
                    'label' => t('label.name'),
                    'rule' => 'required',
            ),

            'description' => array(
                    'type' => 'text',
                    'label' => t('label.desc'),
            ),

            'body'  => array(
                    'type' => 'ckeditor',
                    'label' => t('contact::contact.labels.body'),
                    'rule' => 'required',
            ),
            
            'id'  => array(
                    'type' => 'hidden',
            ),

            'slug'  => array(
                    'type' => 'hidden',
            ),
        );

        $this->submit = array('submit' => array(
            'value' => t('global.save'),
            ));

        $this->cancel = array(
            'url' => 'admin/contact/email-template',
            'class' => 'btn btn-blue'
            );
    }

    public function setToSave($model, $data, $method = 'create')
    {
        $this->method = $method;
        $this->data = $data;
        $this->model = $model;
        
    }

    public function save()
    {
        if ('create' == $this->method) {
            $result = $this->model->create($this->data);
        } else {
            $id = $this->data['id'];
            $result = $this->model->update($id, $this->data);
        }

        if ($result) {
            return $result;
        }

        return false;
    }
}
