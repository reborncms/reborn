<?php 

namespace Contact\Controller\Admin;

use Reborn\Util\Mailer as Mailer;
use Contact\Model\Mail as Mail;
use Contact\Lib\Helper;

class SendMailController extends \AdminController
{

	public function before()
	{
		$this->menu->activeParent('email');
		$this->template->header = \Translate::get('contact::contact.title');
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
		$reply = Mail::where('id', '=', $id)->first();
		if($reply){
			$mail->email = $reply->email;
		}

		if (\Input::isPost()) {

			$v = $this->validate();

			if ($v->valid()) {

				$data = \Input::get('*');

				$to = explode(",",$data['email']);

				foreach ($to as $key) {

					if (!(filter_var($key,FILTER_VALIDATE_EMAIL) !== false)) {
							$to_error = $key;
					}
				}
				
				if (isset($to_error)) {

					\Flash::error(\Translate::get('contact::contact.w_email'));
					$mail = (object)\Input::get('*');

				} else{

					$data['name'] = \Setting::get('site_title');
					$data['from'] = \Setting::get('sever_mail');
					
					$temp = Helper::getTemplate($data,'reply_template');
					
					$attach = \Input::file('attachment');
					
					$config = array(
						'to'		=> $to,
						'from'		=> $data['from'],
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
					
					$sendmail = Mailer::send($config);

					$attName = Mailer::getAttName();
					if ($attName) {
						$data['attachment'] = UPLOAD.'contact_attachment'.DS.$attName;
					}
					if (isset($sendmail['success'])) {
						\Flash::success($sendmail['success']);
						\Event::call('reply_email_success' ,array($data,$to));
						return \Redirect::toAdmin('contact/send-mail');
					}
					if (isset($sendmail['fail'])) {
						\Flash::error($sendmail['fail']);
						return \Redirect::toAdmin('contact/send-mail');
					}
					
				}
			} else {
				$errors = $v->getErrors();
				$this->template->errors = $errors;
				$mail = (object)\Input::get('*');
			}

		}
		$this->template->title(\Translate::get('contact::contact.s_mail'))
					->breadcrumb(\Translate::get('contact::contact.p_title'))
					->style('plugins/jquery.tagsinput_custom.css')
					->script(array(
					 	'plugins/jquery-ui-timepicker-addon.js',
					 	'plugins/jquery.tagsinput.min.js'))
					->set('mail',$mail)
					->setPartial('admin/sendmail/index');
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

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

		return $v;
	}
}