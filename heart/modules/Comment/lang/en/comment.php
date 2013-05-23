<?php

return array(
	'comment' 				=> array(
		'sig' 				=> 'Comment',
		'plu' 				=>'Comments'
	),

	'empty' 				=> array(
		'database' 			=> 'too bad it\'s empty',
		'id' 				=> 'invalid id ',
	),

	'title' 				=> 'Comments Module',
	'edit' 					=> 'Edit',
	'reply' 				=> 'Reply',
	'no_comment' 			=> 'There is no %s comment.',
	'message' 				=> array(
		'success' 			=> array(
			'change_status' => 'Successfully changed comment status',
			'multi_acion'	=> 'Comment successfully %s',
			'multi_actions'	=> 'Comments successfully %s',
			'delete'		=> 'Comment successfully delete',
			'multi_delete'	=> 'Comments successfully delete',
		),
		'error'				=> array(
			'general' 		=> 'Error Occur. Something went wrong.',
			'not_exist' 	=> 'Sorry this comment is not appear to exist',
			'multi_action'	=> 'Error occur cannot %s comment',
			'delete'		=> 'Error occur cannot delete comment',
		),
		'warning' 			=> array(
			'approve_spam'	=> 'You approved the spam comment.',
			'no-akismet-key'=> 'Please Fill out akismet api key in comment settings to work the spam filter.',
			'wrong-akismet'	=> 'Wrong akismet api key.',
		),
		'success' 			=> 'Comment successfully submited',
		'wait_approve' 		=> 'Your Comment is waiting for approval.',
		'error' 			=> 'Comment cannot be submited',
		'delete'	 		=> 'Comment have been deleted',
		'approve' 			=> 'Comment have been approved',
		'unapprove' 		=> 'Comment have been unapproved',
		'no_comment' 		=> 'No Comment for this content.',
		'single_comment' 	=> '1 Comment for this content.',
		'multi_comment' 	=> '%s Comments for this content.',
	),
	'multi' 				=> array(
		'approve' 			=> 'Comments have been approved',
		'unapprove' 		=> 'Comments have been unapproved',
		'delete' 			=> 'Comments have been deleted',
	),

	'label'					=> array(
		'all'				=> 'All',
		'approved'			=> 'Approved',
		'unapproved'		=> 'Unapproved',
		'pending'			=> 'Pending',
		'spam'				=> 'Spam',
		'author_email'		=> 'Author Email',
		'message'			=> 'Message',
		'status'			=> 'Status',
		'edit_info'			=> 'Last edited by %s',
		'reply'				=> 'Reply',
		'mark_as_spam'		=> 'Mark as spam',
		'apply_selected'	=> 'Apply action to Selected',
		'select_action'		=> '-- Select action --',
		'post_comment'		=> 'Post a Comment',
		'post_comment_as'	=> 'Post comment as',
		'logout'			=> 'Logout',
	),

	'form' 					=> array(
		'name' 				=> 'Name',
		'email' 			=> 'Email',
		'url' 				=> 'Website',
		'comment' 			=> 'Comments',
		'action' 			=> 'Action',
		'date' 				=> 'Date',
		'pending' 			=> 'Pending',
		'submit' 			=> 'Submit',
		'cancel' 			=> 'Cancel',
		'all' 				=> 'All'
	),
);
