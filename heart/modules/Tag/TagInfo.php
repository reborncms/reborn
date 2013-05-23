<?php

namespace Tag;

class TagInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Tag';

	protected $version = '1.0';

	protected $description = 'Manage tags for your content';

	protected $author = 'Nyan Lynn Htut / Li Jia Li';

	protected $authorUrl = 'http://www.reborncms.com';

	protected $authorEmail = 'reborncms@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'tag';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = false;

}
