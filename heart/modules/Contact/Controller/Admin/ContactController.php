<?php

namespace Contact\Controller\Admin;

use Contact\Model\Mail as Mail;
use Reborn\Util\Pagination as Pagination;

class ContactController extends \AdminController
{
	public function before() {
		$this->menu->activeParent('email');
		$this->template->style('contact.css', 'contact');
	}

	/**
	 * Show all Email to Admin
	 *
	 * @package Contact\Controller
	 * @author RebornCMS Development Team
	 **/
	public function index($id = null)
	{	

		$options = array(
			'total_items'	=> Mail::get()->count(),
			'url'			=> ADMIN_URL.'/contact/index',
			'items_per_page'=> 7,
			'uri_segment'	=> 4,
			);
			
		$pagination = Pagination::create($options);

		$result = Mail::skip(Pagination::offset())
							->take(Pagination::limit())
							->orderBy('id','desc')
							->get();

		$this->template->title(\Translate::get('contact::contact.inbox'))
					->breadcrumb(\Translate::get('contact::contact.all_con'))
					->set('mails', $result)
					->set('pagination',$pagination)
					->setPartial('admin\inbox\index');
	}

	/**
	 * Show detail Email to Admin
	 *
	 * @package Contact\Controller
	 * @author RebornCMS Development Team
	 **/
	public function detail($id)
	{

		$mail = Mail::where('id', '=', $id)->first();
		
		if (count($mail) == 0) return $this->notFound();
		
		$mail->read_mail = 1;
		$mail->save();

		\Event::call('email_receive_detail',array($mail));

		$this->template->title(\Translate::get('contact::contact.title'))
					->breadcrumb($mail->subject)
					->set('mail',$mail)
					->setPartial('admin\inbox\detail');
	}

	/**
	 * Delete Email from Database
	 *
	 * @package Contact\Controller
	 * @author RebornCMS Development Team
	 **/
	public function delete($id = 0)
	{
		$ids = ($id) ? array($id) : \Input::get('action_to');

		$mails = array();

		foreach ($ids as $id) {
			if ($mail = Mail::find($id)) {
				$mail->delete();
				$mails[] = "success";
			}
		}
		
		if (!empty($mails)) {
			if (count($mails) == 1) {
				\Flash::success(\Translate::get('contact::contact.mail_delete'));
			} else {
				\Flash::success(\Translate::get('contact::contact.mails_delete'));
			}
		} else {
			\Flash::error(\Translate::get('contact::contact.template_error'));
		}
		\Event::call('email_receive_delete', array(true));
		return \Redirect::toAdmin('contact');
	}

	
}