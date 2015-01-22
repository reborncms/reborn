<?php

namespace Contact\Controller;

use Contact\Model\Mail as Mail;
use Contact\Lib\Helper;
use Config, Event, Flash, Input, Mailer, Redirect, Setting, Translate;

/**
 * Contact Controller
 * @package Contact\Contact
 * @author RebornCMS Developement Team <reborncms@gmail.com>
 */
class ContactController extends \PublicController
{
    /**
     * Mailer
     */
    protected $mail;

    /**
     * Check Attachment field
     */
    protected $hasAttach;

    /**
     * Input Data
     * @var array
     */
    protected $data = array();

    /**
     * Other New Field of Contact
     * @var array
     */
    protected $fields = array();

    public function before()
    {
        $this->template->header = t('contact::contact.title');

        $this->mail = Mailer::create(array('type' => \Setting::get('transport_mail')));

        $this->hasAttach = Setting::get('attach_field');

        $model = new Mail;

        if (\Module::isEnabled('field')) {

            $this->fields = \Field::getForm('contact', $model);

        }
    }

    /**
     * Received Email from User,guest or other
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function index($widget = false)
    {

        $errors = new \Reborn\Form\ValidationError();
        
        

        if (Input::isPost()) {

            $referer = Input::server('HTTP_REFERER');

            $v = $this->validate();

            $widget = Input::get('widget');

            if ($v->valid()) {

                $this->data = Input::get('*');

                $this->data['ip'] = Input::ip();

                $temp = Helper::getTemplate($this->data,'contact_template');

                $this->mail->to(Setting::get('site_mail'), Setting::get('site_title'));

                $this->mail->from($this->data['email'], $this->data['name']);
                $this->mail->subject($this->data['subject']);
                $this->mail->body($temp);

                $attach = $this->checkAttachment();

                if (isset($attach['error'])) {
                    Flash::error($attachment['error']);

                    return Redirect::to($referer);
                }

                if ($this->mail->send()) {

                    Flash::success(t('contact::contact.success_mail_send'));

                    $this->getData();

                    Event::call('receive_mail_success',array($data));

                    return Redirect::to($referer);

                } else {

                    Flash::error($this->mail->getError());

                    return Redirect::to($referer);
                }

            } else {

                $errors = $v->getErrors();

                $this->mail = (object) Input::get('*');

                if ($widget == true) {

                        Flash::error($errors->toArray());

                    return Redirect::to($referer);
                }

            }

        }

        
        $this->template->title(t('contact::contact.title'))
                    ->breadcrumb(t('contact::contact.p_title'))
                    ->set('mail',$this->mail)
                    ->set('errors',$errors)
                    ->set('hasAttach',$this->hasAttach)
                    ->set('custom_field', $this->fields)
                    ->view('index');
    }


    /**
     * Check Attachment for Mail
     * @return array
     */
    public function checkAttachment()
    {
        if (isset($this->data['attachment']) && $this->data['attachment']) {

            $attachment = Helper::mailAttachment('attachment', Config::get('contact::contact.attachment_ext'));

            if (isset($attachment['error'])) {

                return array('error' => $attachment['error']);
            }

            $this->mail->attach($attachment['path'],$attachment['realName']);
            $this->data['attachment'] = $attachment['name'];
            $this->data['attachment_name'] = $attachment['realName'];

        }

        return array('success'=>'success');
    }

    /**
     * Store Data
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function getData()
    {
        $get = new Mail;

        $get->name = $this->data['name'];
        $get->email = $this->data['email'];
        $get->subject = $this->data['subject'];
        $get->message = $this->data['message'];
        $get->ip = $this->data['ip'];

        if (isset($this->data['attachment'])) {

            $get->attachment = $this->data['attachment'];
            $get->attachment_name = $this->data['attachment_name'];
        }

        if ($get->save()) {
            if (\Module::isEnabled('field')) {

                \Field::save('contact', $get);

            }
        }
    }

    /**
     * Validation Email
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public static function validate()
    {
        $rule = array(
                    'name'   => 'required|maxLength:50',
                    'email'  => 'required|email',
                    'subject'=> 'required|maxLength:50',
                    'message'=> 'required'
                );

        $v = new \Reborn\Form\Validation(Input::get('*'), $rule);

        return $v;
    }

}
