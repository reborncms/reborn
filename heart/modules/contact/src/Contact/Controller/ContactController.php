<?php

namespace Contact\Controller;

use Contact\Model\Mail as Mail;
use Contact\Model\EmailTemplate as Etemplate;
use Reborn\Util\Mailer as Mailer;
use Contact\Lib\Helper;

class ContactController extends \PublicController
{
	public function before()
	{
		$this->template->header = \Translate::get('contact::contact.title');
	}

	/**
	 * Received Email from User,guest or other
	 *
	 * @package Contact\Controller
	 * @author RebornCMS Development Team
	 **/
	public function index($widget = false )
	{
		$mail = new \stdClass;
		$errors = new \Reborn\Form\ValidationError();
		if (\Input::isPost()) {
			$referer = \Input::server('HTTP_REFERER');

			$v = $this->validate();
			$widget = \Input::get('widget');
			if ($v->valid()) {

				$data = \Input::get('*');

				$data['ip'] = \Input::ip();

				$attach = \Input::file('attachment');

				$temp = Helper::getTemplate($data,'contact_template');
				
				$config = array(
					'to'		=> array(\Setting::get('site_mail')),
					'from'		=> $data['email'],
					'name'		=> $data['name'],
					'subject'	=> $data['subject'],
					'body'		=> $temp,
					'attachment'=> array(
						'fieldName'=> 'attachment',
						'value'		=> $attach,
						),
					
					'attachmentConfig'=> array(
						'savePath'		=> UPLOAD.'contact_attachment',
						'createDir'	=> true,
						'allowedExt'=> array('jpg', 'jpeg', 'png', 'gif',
										'txt','pdf','doc','docx','xls','zip','tar',
										'xlsx','ppt','tif','tiff'),
						),
				);
				
				
				$contact = Mailer::send($config);
				
				$attName = Mailer::getAttName();
				if ($attName) {
					$data['attachment'] = UPLOAD.'contact_attachment'.DS.$attName;
				}
				if (isset($contact['success'])) {
					\Flash::success($contact['success']);
					$this->getData($data);
					\Event::call('receive_mail_success',array($data));
					return \Redirect::to($referer);
				}
				if (isset($contact['fail'])) {
					\Flash::error($contact['fail']);
					return \Redirect::to($referer);
				}
				
			} else {
				$errors = $v->getErrors();
				$mail = (object)\Input::get('*');
				if ($widget == true) {
					
						\Flash::error($errors->toArray());
					
					return \Redirect::to($referer);
				}
				
			}

		}
		$this->template->title(\Translate::get('contact::contact.title'))
					->breadcrumb(\Translate::get('contact::contact.p_title'))
					->set('mail',$mail)
					->set('errors',$errors)
					->setPartial('index');
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
		$get->save();
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
			        'name'   => 'required|maxLength:25',
			        'email'  => 'required|email',
			        'subject'=> 'required|maxLength:50',
			        'message'=> 'required'
			    );

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

		return $v;
	}
	
}
