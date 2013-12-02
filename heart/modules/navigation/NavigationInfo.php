<?php

namespace Navigation;

class NavigationInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Navigation';

	protected $version = '1.0';

	protected $displayName = array(
								'en' => 'Navigation Manager',
								'my' => 'Website ၏ navigation များကို စီမံခန့်ခွဲရာနေရာ'
							);

	protected $description = array(
								'en' => 'Manage navigation link and group for your website.',
								'my' => 'Website တွင် တစ်နေရာမှ တစ်နေရာသို့ သွားလာရ အဆင်ပြေစေရန် ညွှန်းထားသော navigation link များကို စီမံခန့်ခွဲသည့်နေရာ'
							);

	protected $author = 'Nyan Lynn Htut';

	protected $authorUrl = 'http://www.myanmarlinks.net';

	protected $authorEmail = 'lynnhtut87@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'navigation';

	protected $allowToChangeUriPrefix = false;

	protected $sharedData = false;

	protected $roles = array(
						'nav.create' => 'Navigation Link Create',
						'nav.edit' => 'Navigation Link Edit',
						'nav.delete' => 'Navigation Link Delete'
						);

}
