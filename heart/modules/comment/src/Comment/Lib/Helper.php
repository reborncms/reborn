<?php 

namespace Comment\Lib;

use Comment\Model\Comments as Comment;

class Helper
{
	public static function single_comment($comment)
	{
		$s_comment = '';
		$user_class = ($comment['user_id'] != null) ? " user_comment" : "";
		$s_comment .= '<li id="comment-'. $comment['id'] .'" class="single_comment'. $user_class .'">';
		if ($comment['user_id'] != null) {
			$user = \Auth::getUserProvider()->findById($comment['user_id']);
			$author_name = $user->first_name . ' ' . $user->last_name; 
			$author_email = $user->email;
			$author_link = rbUrl('user/profile/'.$user->id);
		} else {
			$author_name = $comment['name'];
			$author_email = $comment['email'];
			$author_link = $comment['url'];
		}
		$s_comment .= '<div class="author_info">';
		$default_img = assetPath('img', 'comment').'default_avatar.jpg';

		$s_comment .= '<div class="gravi">';
		if (checkOnline()) {
			$s_comment .= gravatar($author_email, \Setting::get('comment_gravatar_size'), $author_name);
		} else {
			$s_comment .= '<img src="'. $default_img .'" alt="'. $author_name .'" width="'.\Setting::get('comment_gravatar_size').'px" />';
		}
		$s_comment .= '</div>';
		
		$s_comment .= '<span class="author_name" id="comment_'.$comment['id'].'_author_name">';
		if (!empty($author_link)) {
			$s_comment .= '<a href="'. $author_link .'">'. $author_name .'</a>';
		} else {
			$s_comment .= $author_name;
		}
		$s_comment .= '</span>'; //end of author_name
		$s_comment .= '</div>'; //end of author_info
		$s_comment .= '<div class="comment_body">';
		$s_comment .= '<div class="comment_date">'. $comment['created_at'] .'</div>';
		$s_comment .= '<a href="#comment_form_wrapper" class="reply_link" onclick="setParentId('.$comment['id'].')">'. t('comment::comment.reply') .'</a>';
		$s_comment .= '<p class="comment_message">'.$comment['value'].'</p>';
		$s_comment .= '</div>'; //end of comment_body
		$s_comment .= '</li>'; // end of comment_wrapper

		return $s_comment;
	}

	public static function get_children($children)
	{
		$cc = '';
		$cc .= '<ul class="children" style="list-style:none;">';
		foreach ($children as $comment) {
			$cc .= self::single_comment($comment);
			if (isset($comment['children'])) {
				$cc .= self::get_children($comment['children']);
			}
		}
		$cc .= '</ul>';

		return $cc;
	}

	public static function getContentTitle($id, $content_type, $title_field = 'title')
	{
		$title = \DB::table($content_type)->where('id' , $id)->pluck($title_field);
    	return $title;
	}

	public static function userDeleted($user)
	{
		$name = $user->first_name.' '.$user->last_name;
		$email = $user->email;
		$cmt_update = Comment::where('user_id', $user->id)->update(array('user_id' => null, 'name' => $name, 'email' => $email));
		if ($cmt_update) {
			return true;
		} else {
			return false;
		}
	}

	public static function getLatestComments($count) {

		$comments = Comment::with('author')->where('status', '!=', 'spam')
							->orderBy('created_at', 'desc')
							->take($count)
							->get();
		$com = array();
							
		foreach ($comments as $comment) {
			if ($comment->name == null) {
				$comment->name = $comment->author_name;
			}
			$comment->content_title = self::getContentTitle($comment->content_id, $comment->module, $comment->content_title_field);
			$com[] = $comment;
		}
		return $com;
	}

	public static function getUserNameWithLink($comment)
	{
		if ($comment->user_id != null) {
			$name = $comment->author_name;
			$url = rbUrl('user/profile/'.$comment->user_id);
		} else {
			$name = $comment->name;
			$url = $comment->url;
		}
		if ($url) {
			return '<a href="'.$url.'" target="_blank">'.$name.'</a>';
		} else {
			return $name;
		}
	}

	public static function dashboardWidget()
	{
		$widget = array();
		$widget['title'] = t('label.last_comment');
		$widget['icon'] = 'icon-comment';
		$widget['id'] = 'comment';
		$widget['body'] = '';
		$comments = self::getLatestComments(5);
		$widget['body'] .= '<ul>';
		if (!empty($comments)) {
			foreach ($comments as $comment) {
				$widget['body'] .= '<li>
										<span class="date">'.date("d-m-Y", strtotime($comment->created_at)).'</span>
										<span class="commenter">'.self::getUserNameWithLink($comment).'</span>
										commented at
										<span class="cmt-module">'. ucfirst($comment->module) .'</span>
										on
										<span class="cmt-title">'. $comment->content_title .'</span>
										<span class="dashboard_widget_action">
											'.self::commentStatusLabel($comment->id, $comment->status).'
										</span>
									</li>';
			}
		} else {
			$widget['body'] .= '<li><span class="empty-list">'.t('label.last_comment_empty').'</span></li>';
		}
		$widget['body'] .= '</ul>';

		return $widget;
	}

	public static function commentStatusLabel($id, $status)
	{
		if ($status == 'approved') {
			$status_class = 'label-success';
			$change_info = t('comment::comment.info.unapprove'); 
		} elseif ($status == 'pending') {
			$status_class = 'label-warning';
			$change_info = t('comment::comment.info.approve');
		} else {
			$status_class = 'label-error';
			$change_info = t('comment::comment.info.approve'); 
		}

		$content = '<a href="'. adminUrl('comment/change-status/'.$id) .'" class="tipsy-tip" title="'. $change_info .'">
						<span class="label '. $status_class .'">'. t('comment::comment.label.'.$status).'</span>
					</a>';

		return $content;
		
	}

	/**
	 * Get Comment Count
	 *
	 * @return void
	 **/
	public static function commentCount($id, $content_type)
	{
		return Comment::where('content_id', $id)->where('module', $content_type)->count();
	}

	/**
	 * Delete comment if comment delete
	 *
	 * @return boolean
	 **/
	public static function commentDelete($id, $type, $forceDelete = false) 
	{
		if ($forceDelete) {

			$comment_delete = Comment::withTrashed()->where('content_id', $id)->where('module', $type)->forceDelete();

		} else {

			$comment_delete = Comment::where('content_id', $id)->where('module', $type)->delete();

		}
		return true;
	}

	public static function commentRestore($id, $type)
	{
		Comment::withTrashed()->where('content_id', $id)->where('module', $type)->restore();

		return true;
	}

}