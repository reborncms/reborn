<?php

namespace Admin;

class AdminInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Admin';

	protected $version = '1.0';

	protected $displayName = array(
								'en' => 'Admin Dashboard',
								'my' => 'ထိန်းချုပ်ခန်း မျက်နာစာ'
							);

	protected $description = array(
								'en' => 'Admin Panel Dashboard Module',
								'my' => 'ထိန်းချုပ်ခန်း မျက်နာစာ'
							);

	protected $author = 'Nyan Lynn Htut';

	protected $authorUrl = 'http://reborncms.com';

	protected $authorEmail = 'lynnhtut87@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'admin';

	protected $allowToChangeUriPrefix = false;

}
