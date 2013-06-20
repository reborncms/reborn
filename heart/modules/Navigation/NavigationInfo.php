<?php

namespace Navigation;

class NavigationInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Navigation';

	protected $version = '1.0';

	protected $description = 'Navigation management module';

	protected $author = 'Nyan Lynn Htut';

	protected $authorUrl = 'http://www.myanmarlinks.net';

	protected $authorEmail = 'lynnhtut87@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'navigation';

	protected $allowToChangeUriPrefix = false;

	protected $roles = array(
						'nav.create' => 'Navigation Link Create',
						'nav.edit' => 'Navigation Link Edit',
						'nav.delete' => 'Navigation Link Delete'
						);

}
