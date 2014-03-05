<?php

namespace Contact\Controller\Admin;

use Contact\Model\Mail as Mail;
use Contact\Lib\Helper;
use Event, Flash, Input, Mailer, Redirect, Translate;

class SendMailController extends \AdminController
{

    public function before()
    {
        $this->menu->activeParent(\Module::get('contact', 'uri'));
        $this->template->header = Translate::get('contact::contact.title');
        $this->template->style('contact.css', 'contact');
        $this->template->script('contact.js','contact');
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
        $mail =new \stdClass;
        $sendMail = Mailer::create(array('type' => \Setting::get('transport_mail')));
        $reply = Mail::where('id', '=', $id)->first();
        if ($reply) {
            $mail->email = $reply->email;
        }
        $errors = new \Reborn\Form\ValidationError();
        if (Input::isPost()) {

            $v = $this->validate();

            if ($v->valid()) {

                $data = Input::get('*');

                $to = explode(",",$data['email']);

                foreach ($to as $key) {

                    if (!(filter_var($key,FILTER_VALIDATE_EMAIL) !== false)) {
                            $to_error = $key;
                    }
                }

                if (isset($to_error)) {

                    Flash::error(Translate::get('contact::contact.w_email'));
                    $mail = (object) Input::get('*');

                } else {

                    $data['name'] = \Setting::get('site_title');
                    $data['from'] = \Setting::get('sever_mail');

                    $temp = Helper::getTemplate($data,'reply_template');

                    $sendMail->to($to);
                    $sendMail->from($data['from'],$data['name']);
                    $sendMail->subject($data['subject']);
                    $sendMail->body($temp);

                    if ($data['attachment']) {
                        $attachment = Helper::mailAttachment('attachment',array('jpg','jpeg','png','gif','txt','pdf','doc','docx','xls','zip','tar','xlsx','ppt','tif','tiff'));

                        if (isset($attachment['error'])) {
                            Flash::error($attachment['error']);

                            return Redirect::to($referer);
                        }

                        $sendMail->attach($attachment['path']);
                        $data['attachment'] = $attachment['name'];
                    }

                    if ($sendMail->send()) {
                        Flash::success(Translate::get('contact::contact.success_mail_send'));

                        Event::call('reply_email_success' ,array($data,$to));

                        return Redirect::toAdmin('contact/send-mail');
                    } else {
                        Flash::error($sendMail->getError());

                        return Redirect::toAdmin('contact/send-mail');
                    }

                }
            } else {
                $errors = $v->getErrors();
                $mail = (object) Input::get('*');
            }

        }
        $this->template->title(Translate::get('contact::contact.s_mail'))
                    ->breadcrumb(Translate::get('contact::contact.p_title'))
                    ->style('plugins/jquery.tagsinput_custom.css')
                    ->script(array(
                        'plugins/jquery-ui-timepicker-addon.js',
                        'plugins/jquery.tagsinput.min.js'))
                    ->set('mail',$mail)
                    ->set('errors',$errors)
                    ->view('admin/sendmail/index');
    }

    /**
     * Validation Email
     *
     * @package Contact\Controller
     * @author RebornCMS Development Team
     **/
    protected function validate()
    {
        $rule = array(
                    'email'  => 'required',
                    'subject'=> 'required|maxLength:50',
                    'message'=> 'required'
                );

        $v = new \Reborn\Form\Validation(Input::get('*'), $rule);

        return $v;
    }
}
