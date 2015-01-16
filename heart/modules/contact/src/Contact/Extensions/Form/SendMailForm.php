<?php

namespace Contact\Extensions\Form;

use Contact\Lib\Helper;
/**
 * Contact Admin Send Mail Form 
 *
 * @author RebornCMS Development Team
 * @package Contact\Extensision\Form
 * @link http://www.reborncms.com
 **/
class SendMailForm extends \FormBuilder
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

            'email' => array(
                    'type' => 'tags',
                    'label' => t('label.email'),
                    'js_opts'       => array(
                        'defaultText'=> 'Add Email',
                        'width' => '27%',
                        'height'   => 'auto',
                    ),
            ),

            'group' => array(
                    'type' => 'select',
                    'label' => t('contact::contact.labels.group'),
                    'option' => Helper::getUserGroup()
            ),

            'subject' => array(
                    'type' => 'text',
                    'label' => t('contact::contact.labels.subject'),
                    'rule' => 'required',
            ),

            'message'  => array(
                    'type' => 'textarea',
                    'label' => t('contact::contact.labels.message'),
                    'rule' => 'required',
            ),

            'attachment' => array(
                    'type' => 'file',
                    'label' => t('contact::contact.labels.attachment'),
            ),
        );

        $this->submit = array('submit' => array(
            'value' => t('contact::contact.labels.send'),
            ));

        $this->cancel = array(
            'url' => 'admin/contact',
            'class' => 'btn btn-blue'
            );
    }
}
