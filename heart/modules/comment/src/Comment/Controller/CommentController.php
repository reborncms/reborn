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
	 * Save Comment
	 *
	 * @return void
	 **/
	public function post()
	{
		if (\Input::isPost()) {
			$referer = \Input::server('HTTP_REFERER');
			if (\Input::get(\Setting::get('spam_filter')) == '') {
				$val = self::validate();
				if ($val->valid()) {
					$comment = new Comment;
					if (\Auth::check()) {
						$user = \Auth::getUser();
						$comment->user_id = $user->id;
					} else {
						$comment->name = \Input::get('name');
						$comment->email = \Input::get('email');
						$comment->url = \Input::get('url');
					}

					if (!\Auth::check() and \Setting::get('comment_need_approve')) {
						$comment->status = 'pending';
						$msg = t('comment::comment.message.wait_approve');
					} else {
						$comment->status = 'approved';
						$msg = t('comment::comment.message.success.comment_submit');
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
						\Flash::success($msg);
					} else {
						\Flash::error(t('comment::comment.message.error.comment_submit'));
					}	
				} else {		
					//validation Error
					\Flash::error(t('comment::comment.message.error.validation'));
					$this->template->set('comment_errors', $val->getErrors);
					$this->template->set('comment_info', \Input::get('*'));
				}
			} else {
				\Flash::error(t('comment::comment.message.error.bot'));
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

			if (\Auth::check()) {
				$user = \Auth::getUser();
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
		if (\Auth::check()) {
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
