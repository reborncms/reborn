<?php

namespace Comment\Facade;

use Comment\Model\Comments as Model;

/**
 * undocumented class
 *
 * @package default
 * @author
 **/
class Comment extends \Facade
{
	/**
	 * Get Comments.
	 *
	 *
	 * @param int $content_id
	 * @param string $module Module name for Comments
	 * @param int $status Comment status
	 * @return string
	 **/
	protected function get($content_id, $module, $status)
	{
		$app = static::$app;

		if ($status === 1) {
			$status = 'open';
		} else if ($status === 0) {
			$status = 'close';
		}

		$comment_form = '';

		$restructured = array();

		$comments = Model::with('author')->where('content_id', $content_id)
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

		$app->template->set('comments', $restructured)
						->set('total_comments', $total_comments)
						->set('status', $status)
						->set('module', $module)
						->set('content_id', $content_id);

		if (\Setting::get('use_default_style') == 1) {
			$app->template->style('front-comment.css', 'comment');
		}

		if ($status == 'open') {
			$comment_form = $app->template->partialRender('comment::commentForm');
		} else if ($status == 'close' and $total_comments > 0) {
			$comment_form = "Comment closed.";
		}

		$app->template->set('comment_form', $comment_form);

		return $app->template->partialRender('comment::index');
	}

	protected static function getInstance()
	{
		return new static();
	}

} // END class Comment extends \Facade
