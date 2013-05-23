<?php

namespace Comment;

class CommentInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Comment';

	protected $version = '1.0';

	protected $description = 'Comment Module';

	protected $author = 'Naing Lin Aung / Li Jia Li';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupprot = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'comment';

	protected $allowToChangeUriPrefix = true;

	protected $useAsDefaultModule = false;

}
