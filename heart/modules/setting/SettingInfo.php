<?php

namespace Setting;

class SettingInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'Setting';

	protected $version = '1.0';

	protected $displayName = array(
								'en' => 'Setting Manager',
								'my' => 'Website ၏ အချက်အလက်များကို စီမံခန့်ခွဲရာနေရာ'
							);

	protected $description = array(
								'en' => 'Manage to your website\'s configuration setting.',
								'my' => 'Website အတွက် လိုအပ်သော အချက်အလက်များကို စီမံခန့်ခွဲရာနေရာ'
							);


	protected $author = 'Nyan Lynn Htut';

	protected $authorUrl = 'http://www.myanmarlinks.net';

	protected $authorEmail = 'lynnhtut87@gmail.com';

	protected $frontendSupport = false;

	protected $backendSupport = true;

	protected $uriPrefix = 'setting';

}
