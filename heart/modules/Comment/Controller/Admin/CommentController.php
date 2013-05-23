<?php

namespace Comment\Controller\Admin;

use Comment\Model\Comments as Comment;
use Reborn\Form\Validation as Validation;
use TijsVerkoyen\Akismet\Akismet;

class CommentController extends \AdminController
{
	private  $adminurl;

	public function before() 
	{
		$this->menu->activeParent('content');
		
		\Translate::load('comment::comment');

		$ajax = $this->request->isAjax();

		if ($ajax) {
			$this->template->partialOnly();
		}

		$this->template->style('comment.css', 'comment');
		
	}

	/**
	 * Comment Index (Comment List)
	 *
	 * @return void
	 **/
	public function index($page = null)
	{
		//Pagination
		$options = array(
		    'total_items'       => Comment::where('status', '!=', 'spam')->count(),
		    'url'               => ADMIN_URL.'/comment/index',
		    'items_per_page'    => \Setting::get('admin_item_per_page'),
		    'uri_segment'		=> 4
		);

		$pagination = \Pagination::create($options);

		$comments = Comment::where('status', '!=', 'spam')
							->orderBy('created_at', 'desc')
							->skip(\Pagination::offset())
							->take(\Pagination::limit())
							->get();

		$akismet_status = self::checkAkismet();

		$this->template->title('Manage Comments')
						->set('comments', $comments)
						->set('pagination', $pagination)
						->set('akismet_status', $akismet_status)
						->script('plugins/jquery.colorbox.js')
						->set('list_type', 'all')
						->setPartial('admin/index');
	}

	/**
	 * Filter comments by Status
	 *
	 * @return void
	 **/
	public function filter($status, $page = null)
	{
		$options = array(
		    'total_items'       => Comment::where('status', $status)->count(),
		    'url'               => ADMIN_URL.'/comment/filter/'.$status,
		    'items_per_page'    => \Setting::get('admin_item_per_page'),
		    'uri_segment'		=> 5
		);

		$pagination = \Pagination::create($options);

		$comments = Comment::where('status', $status)
					->orderBy('created_at', 'desc')
					->skip(\Pagination::offset())
					->take(\Pagination::limit())
					->get();

		$akismet_status = self::checkAkismet();

		$page_title = ucfirst($status).' Comments';

		$this->template->title($page_title)
						->set('comments', $comments)
						->set('pagination', $pagination)
						->set('list_type', $status)
						->set('akismet_status', $akismet_status)
						->script('plugins/jquery.colorbox.js')
						->setPartial('admin/index');
	}

	/**
	 * Change Comment Status
	 *
	 * @return void
	 **/
	public function changeStatus($id, $status = null)
	{
		$comment = Comment::find($id);

		if ($comment->status == 'spam') {
			$spam = true;
		} else {
			$spam = false;
		}

		if ($status != null) {

			$comment->status = $status;
		} else {

			$comment->status = ($comment->status == 'approved') ? 'pending' : 'approved';
		}

		$save = $comment->save();

		if ($save) {
			if ($spam) {
				\Flash::warning(t('comment::comment.message.warning.approve_spam'));
			} else {
				\Flash::success(t('comment::comment.message.success.change_status'));
			}
		} else {
			\Flash::error(t('comment::comment.message.error.general'));
		}

		return \Redirect::to(adminUrl('comment'));

	}

	/**
	 * Reply Comments from Admin Panel
	 *
	 * @return void 
	 **/
	public function reply($id = 0)
	{
		if (\Input::isPost()) {
			$or_comment = Comment::find(\Input::get('id'));

			$comment = new Comment;
			$comment->user_id = \Sentry::getUser()->id;
			$comment->value = \Input::get('message');
			$comment->module = $or_comment->module;
			$comment->content_id = $or_comment->content_id;
			$comment->status = "approved";
			$comment->parent_id = $or_comment->id;
			$comment->ip_address = \Input::ip();
			$save = $comment->save();
			if ($save) {
				return "true";
			} else {
				return "false";
			}
		}

		$comment = Comment::find($id);

		if ($comment == null) {
			return "<div class='ajax-error'>t('comment::comment.message.error.not_exist')</div>";
		}

		$this->template->setPartial('admin/reply-edit')
						->set('comment', $comment)
						->set('method', 'reply');

	}

	public function edit($id = 0)
	{
		if (\Input::isPost()) {
			$comment = Comment::find(\Input::get('id'));
			$comment->value = \Input::get('message');
			$comment->edit_user = \Sentry::getUser()->id;
			$save = $comment->save();
			if ($save) {
				return "true";
			} else {
				return "false";
			}
		}

		$comment = Comment::find($id);

		if ($comment == null) {
			return "<div class='ajax-error'>t('comment::comment.message.error.not_exist')</div>";
		}

		$this->template->setPartial('admin/reply-edit')
						->set('comment', $comment)
						->set('method', 'edit');
	}



	public function multiaction()
	{
		$method = \Input::get('sel_multi_action');

		if ($method == 'delete') {
			self::delete();
		} else {

			$ids = \Input::get('action_to');
			$comments = array();
			foreach ($ids as $id) {
				if ($comment = Comment::find($id)) {
					$comment->status = $method;
					$save = $comment->save();
					if ($save) {
						$comments[] = $id;
					}
				}
			}

			if (!empty($comments)) {
				if (count($comments) == 1) {
					\Flash::success(sprintf(t('comment::comment.message.success.multi_action'), $method));
				} else {
					\Flash::success(sprintf(t('comment::comment.message.success.multi_actions'), $method));
				}
			} else {
				\Flash::error(sprintf(t('comment::comment.message.error.multi_action'), $method));
			}
			return \Redirect::to(adminUrl('comment'));

		}
		
	}

	public function delete($id = null)
	{
		$ids = ($id) ? array($id) : \Input::get('action_to');

		$comments = array();

		foreach ($ids as $id) {
			if ($comment = Comment::find($id)) {
				$comment->delete();
				$comments[] = $id;
			}
		}

		if (!empty($comments)) {
			if (count($comments) == 1) {
				\Flash::success(t('comment::comment.message.success.delete'));
			} else {
				\Flash::success(t('comment::comment.message.success.multi_delete'));
			}
		} else {
			\Flash::error(t('comment::comment.message.error.delete'));
		}
		return \Redirect::to(adminUrl('comment'));

	}

	protected function checkAkismet() {

		$apiKey = \Setting::get('akismet_api_key');

		if ($apiKey == null) {
			return "no-key";
		} else {
			$url = rbUrl();
			try {
				$akismet = new Akismet($apiKey, $url);
				return $akismet->verifyKey();	
			} catch (\Exception $e) {
				return "no-connection";
			}
			
		}
	}
	
}
