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
    'no_comment' 			=> 'There is no {:type} comment.',
    'message' 				=> array(
        'success' 			=> array(
            'change_status' => 'Successfully changed comment status',
            'multi_action'	=> 'Comment successfully {:method}',
            'multi_actions'	=> 'Comments successfully {:method}',
            'delete'		=> 'Comment successfully delete',
            'multi_delete'	=> 'Comments successfully delete',
            'comment_submit'=> 'Comment successfully submited',
        ),
        'error'				=> array(
            'general' 		=> 'Error Occur. Something went wrong.',
            'not_exist' 	=> 'Sorry this comment is not appear to exist',
            'multi_action'	=> 'Error occur cannot {:method} comment',
            'delete'		=> 'Error occur cannot delete comment',
            'comment_submit'=> 'Comment cannot be submited',
            'bot'			=> 'This is probably a bot.',
            'validation'	=> 'Validation Error!!!',
            'parent_not_approved' => 'Sorry, you can\'t approve this comment because it\'s parent comment ID {:id} is not approved.',
        ),
        'warning' 			=> array(
            'approve_spam'	=> 'You approved the spam comment.',
            'no-akismet-key'=> 'Please Fill out akismet api key in comment settings to work the spam filter.',
            'wrong-akismet'	=> 'Wrong akismet api key.',
            'muti-action-note' => '*** Note : All the actions to parent comments will also effect child comments.(Change comment status, delete)',
        ),
        'wait_approve' 		=> 'Your Comment is waiting for approval.',
        'no_comment' 		=> 'No Comment for this content.',
        'single_comment' 	=> '1 Comment for this content.',
        'multi_comment' 	=> '{:num} Comments for this content.',
    ),
    'multi' 				=> array(
        'approve' 			=> 'Comments have been approved',
        'unapprove' 		=> 'Comments have been unapproved',
        'delete' 			=> 'Comments have been deleted',
    ),

    'menu'					=> array(
        'approved'			=> 'Approved',
        'pending'			=> 'Pending',
    ),

    'info'					=> array(
        'approve'			=> 'Approve',
        'unapprove'			=> 'Unapprove',
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
        'edit_info'			=> 'Last edited by {:name}',
        'reply'				=> 'Reply',
        'mark_as_spam'		=> 'Mark as spam',
        'apply_selected'	=> 'Apply action to Selected',
        'select_action'		=> '-- Select action --',
        'post_comment'		=> 'Post a Comment',
        'reply_to'			=> 'Reply to {:name}\'s comment',
        'edit_comment'		=> 'Edit {:name}\'s comment',
        'post_comment_as'	=> 'Post comment as {:name}.',
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
