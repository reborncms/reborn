<?php

namespace Comment\Controller;

use Comment\Model\Comments as Comment;
use Reborn\Form\Validation as Validation;
use TijsVerkoyen\Akismet\Akismet;
						    	    		
class CommentController extends \PublicController
{

	public function before()
	{
		\Translate::load('comment::comment');
	}

	/**
	 * Comment In other module
	 *
	 * @return void 
	 **/
	public function show($content_id, $module, $status)
	{
		if (!$this->request->isInner()) {

			return $this->notFound();
		}

		$comment_form = '';

		$restructured = array();

		$comments = Comment::where('content_id', $content_id)
								->where('module', $module)
								->where('status', 'approved')
								->get()
								->toArray();

		$total_comments = count($comments);

		if ($total_comments > 0) {
			foreach ($comments as $comment) {
				$com[$comment['id']] = $comment;
			}

			foreach ($com as $c) {
				if (array_key_exists($c['parent_id'], $com)) {
					$com[$c['parent_id']]['children'][] =& $com[$c['id']];
				}
				if ($c['parent_id'] == 0) {
					$restructured[] =& $com[$c['id']];
				}
			}
		}

		$this->template->setPartial('index')
						->set('comments', $restructured)
						->set('total_comments', $total_comments)
						->set('status', $status)
						->set('module', $module)
						->set('content_id', $content_id);

		if (\Setting::get('use_default_style') == 1) {
			$this->template->style('front-comment.css', 'comment');
		}

		if ($status == 'open') {
			$comment_form = $this->template->partialRender('commentForm');
		} else if ($status == 'close' and $total_comments > 0) {
			$comment_form = "Comment closed.";
		} 

		$this->template->set('comment_form', $comment_form);

	}

	/**
	 * Save Comment
	 *
	 * @return void
	 **/
	public function post()
	{
		if (\Input::isPost()) {
			$referer = \Input::server('HTTP_REFERER');
			if (\Security::CSRFvalid('comment') and \Input::get('d0ntF1ll') == '') {
				$val = self::validate();
				if ($val->valid()) {
					$comment = new Comment;
					if (\Sentry::check()) {
						$user = \Sentry::getUser();
						$comment->status = 'approved';
						$comment->user_id = $user->id;
					} else {
						$comment->status = 'pending';
						$comment->name = \Input::get('name');
						$comment->email = \Input::get('email');
						$comment->url = \Input::get('url');
					}

					if (self::checkSpam($referer)) {
						$comment->status = 'spam';
					}

					if (\Input::get('parent_id') !== '') {
						$comment->parent_id = \Input::get('parent_id');
					}

					$comment->value = \Input::get('message');
					$comment->module = \Input::get('module');
					$comment->content_id = \Input::get('content_id');
					$comment->ip_address = \Input::ip();
					$save = $comment->save();
					if ($save) {
						if (\Sentry::check()) {
							\Flash::success(t('comment::comment.message.success'));
						} else {
							\Flash::success(t('comment::comment.message.wait_approve'));
						}
					} else {
						\Flash::error(t('comment::comment.message.error'));
					}	
				} else {		
					//validation Error
					\Flash::error('Validation Error!!!');
					$this->template->set('comment_errors', $val->getErrors);
					$this->template->set('comment_info', \Input::get('*'));
				}
			} else {
				\Flash::error("You are probably a bot.");
			}
		}

		return \Redirect::to($referer);
	}

	protected function checkSpam($referer)
	{
		try {
			$url = rbUrl();
			$apiKey = \Setting::get('akismet_api_key');
			$akismet = new Akismet($apiKey, $url);

			if (\Sentry::check()) {
				$user = \Sentry::getUser();
				$name = $user->first_name . ' ' . $user->last_name;
				$email = $user->email;
				$url = rbUrl('user/profile/'.$user->id);
			} else {
				$name = \Input::get('name');
				$email = \Input::get('email');
				$url = \Input::get('url');
			}

			$isSpam = $akismet->isSpam(\Input::get('message'), $name, $email, $url, $referer);

			return $isSpam;

		} catch (\Exception $e) {

			return false;
		}
	}

	protected function validate()
	{
		if (\Sentry::check()) {
			$rule = array(
				'message' => 'required',
			);	
		} else {
			$rule = array(
				'name' => 'required|maxLength:20',
				'email' => 'required|maxLength:20',
				'message' => 'required'
			);
		}

		$v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

		return $v;
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
