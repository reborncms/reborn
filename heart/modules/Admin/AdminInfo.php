<?php

namespace Admin;

class AdminInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Admin';

	protected $version = '1.0';

	protected $description = 'Admin Panel Dashboard Module';

	protected $author = 'Nyan Lynn Htut';

	protected $authorUrl = 'http://reborncms.com';

	protected $authorEmail = 'lynnhtut87@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'admin';

	protected $allowToChangeUriPrefix = false;

}
