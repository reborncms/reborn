<?php

namespace Contact\Controller\Admin;

use Contact\Model\Mail;
use Contact\Lib\Helper;
use Contact\Extensions\Form\SendMailForm;

use Config, Event, Flash, Input, Mailer, Redirect, Setting, Translate;

/**
 * Contat Admin Send Mail Controller 
 *
 * @package Contact\Controller
 * @author RebornCMS Development Team <reborncms@gmail.com>
 */
class SendMailController extends \AdminController
{
    /**
     * Input Data
     * @var array
     */
    protected $data = array();

    /**
     * mail receiver $to
     * @var array
     */
    protected $to = array();

    /**
     * Mailer
     */
    protected $mail;


    /**
     * before function 
     */
    public function before()
    {
        $this->menu->activeParent(\Module::get('contact', 'uri'));
        $this->template->header = Translate::get('contact::contact.title');
        $this->mail = Mailer::create(array('type' => \Setting::get('transport_mail')));
    }   

    /**
     * Sending Email to User,guest or other
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    public function index($id = null)
    {
        if (!user_has_access('contact.reply')) return $this->notFound();

        $form = SendMailForm::create('','default',array('enctype'=>'multipart/form-data'));

        /* check reply mail */
        if ($id) {
            $reply = Mail::where('id', '=', $id)->first();
            $this->data['email'] = $reply->email;
        }

        if ($form->valid()) {

            $this->data = Input::get('*');

            if (!empty($this->data['email']) || $this->data['group'] != 0) {

                if ($this->checkEmail()) {

                    $this->data['name'] = Setting::get('site_title');
                    $this->data['from'] = Setting::get('sever_mail');

                    $temp = Helper::selectTemplate($this->data, Setting::get('reply_template'));

                    $this->mail->to($this->to);
                    $this->mail->from($this->data['from'],$this->data['name']);
                    $this->mail->subject($this->data['subject']);
                    $this->mail->body($temp);

                    $attach = $this->checkAttachment();

                    if (!isset($attach['error'])) {

                        if ($this->mail->send(true)) {

                            Flash::success(t('contact::contact.success_mail_send'));

                            Event::call('reply_email_success' ,array($this->data,$this->to));

                            return Redirect::toAdmin('contact/send-mail');

                        } else {

                            Flash::error($this->mail->getError());

                            return Redirect::toAdmin('contact/send-mail');
                        }
                    } else {

                        Flash::error($attach['error']);
                    }
                    
                } else {

                    Flash::error(t('contact::contact.w_email'));
                }

            } else {

                Flash::error(t('contact::contact.need_email'));
            }
        }

        $form->provider($this->data);

        $this->template->title(Translate::get('contact::contact.s_mail'))
                    ->breadcrumb(Translate::get('contact::contact.p_title'))
                    ->view('admin/form', compact('form'));
    }

    /**
     * check Email is vaid for Sending Mail and add receiver mail
     * @return boolean
     */
    public function checkEmail()
    {
        $toEmail = array();
        $toGroup = array();

        if (!empty($this->data['email'])) {
            $toEmail = explode(",",$this->data['email']);
        }
        
        if ($this->data['group'] != 0) {
            $toGroup = Helper::getEmail($this->data['group']);
        }
        
        $this->to = array_merge($toEmail,$toGroup);

        foreach ($this->to as $key) {

            if (!(filter_var($key,FILTER_VALIDATE_EMAIL) !== false)) {

                    $to_error = $key;
            }
        }

        if (isset($to_error)) {

            return false;
        }

        return true;
    }

    /**
     * Check Attachment for Mail
     * @return array
     */
    public function checkAttachment()
    {
        if ($this->data['attachment']) {

            $attachment = Helper::mailAttachment('attachment', Config::get('contact::contact.attachment_ext'));

            if (isset($attachment['error'])) {

                return array('error' => $attachment['error']);
            }

            $this->mail->attach($attachment['path'],$attachment['realName']);
            $this->data['attachment'] = $attachment['name'];

        }

        return array('success'=>'success');
    }
}
