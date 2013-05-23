<?php

namespace Blog;

class BlogInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Blog';

	protected $version = '1.0';

	protected $description = 'Manage your blog';

	protected $author = 'Nyan Lynn Htut / Li Jia Li';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupport = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'blog';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = true;

}
