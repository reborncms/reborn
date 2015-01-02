<?php

namespace Contact\Controller;

use Contact\Model\Mail as Mail;
use Contact\Model\EmailTemplate as Etemplate;
use Contact\Lib\Helper;
use Event, Flash, Input, Mailer, Redirect, Translate;

class ContactController extends \PublicController
{
    public function before()
    {
        $this->template->header = Translate::get('contact::contact.title');
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
        $hasAttach = \Setting::get('attach_field');
        $mail = Mailer::create(array('type' => \Setting::get('transport_mail')));
        if (Input::isPost()) {
            $referer = Input::server('HTTP_REFERER');

            $v = $this->validate();
            $widget = Input::get('widget');
            if ($v->valid()) {

                $data = Input::get('*');

                $data['ip'] = Input::ip();

                $temp = Helper::getTemplate($data,'contact_template');

                $mail->to(\Setting::get('site_mail'), \Setting::get('site_title'));

                $mail->from($data['email'], $data['name']);
                $mail->subject($data['subject']);
                $mail->body($temp);

                if ($data['attachment']) {
                    $attachment = Helper::mailAttachment('attachment',array('jpg','jpeg','png','gif','txt','pdf','doc','docx','xls','zip','tar','xlsx','ppt','tif','tiff'));

                    if (isset($attachment['error'])) {
                        Flash::error($attachment['error']);

                        return Redirect::to($referer);
                    }

                    $mail->attach($attachment['path'],$attachment['realName']);
                    $data['attachment'] = $attachment['name'];
                }

                if ($mail->send()) {
                    Flash::success(Translate::get('contact::contact.success_mail_send'));
                    $this->getData($data);
                    Event::call('receive_mail_success',array($data));

                    return Redirect::to($referer);
                } else {
                    Flash::error($mail->getError());

                    return Redirect::to($referer);
                }

            } else {
                $errors = $v->getErrors();
                $mail = (object) Input::get('*');
                if ($widget == true) {

                        Flash::error($errors->toArray());

                    return Redirect::to($referer);
                }

            }

        }
        $model = new Mail;
        $fields = array();
        if (\Module::isEnabled('field')) {
            $fields = \Field::getForm('contact', $model);
        }
        $this->template->title(Translate::get('contact::contact.title'))
                    ->breadcrumb(Translate::get('contact::contact.p_title'))
                    ->set('mail',$mail)
                    ->set('errors',$errors)
                    ->set('hasAttach',$hasAttach)
                    ->set('custom_field', $fields)
                    ->view('index');
    }

    /**
     * Store Data
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function getData($result)
    {
        $get = new Mail;
        $get->name = $result['name'];
        $get->email = $result['email'];
        $get->subject = $result['subject'];
        $get->message = $result['message'];
        $get->ip = $result['ip'];
        if (isset($result['attachment'])) {
            $get->attachment = $result['attachment'];
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
