<?php

namespace Comment;

class CommentInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Comment';

	protected $displayName = array(
		'en'	=> 'Comment',
		'my'	=> 'မှတ်ချက်'
	);

	protected $version = '1.0';

	protected $description = array(
		'en'	=> 'Manage Comments',
		'my'	=> 'မှတ်ချက်များစီမံရန်'
	);

	protected $author = 'Naing Lin Aung / Li Jia Li';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupprot = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'comment';

	protected $allowToChangeUriPrefix = true;

	protected $useAsDefaultModule = false;

	protected $roles = array(
		'comment.reply'		=> 'Reply',
		'comment.edit'		=> 'Edit',
		'comment.delete'	=> 'Delete'
	);

}
