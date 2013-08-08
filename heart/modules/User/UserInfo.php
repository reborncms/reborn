<?php

namespace User;

class UserInfo extends \Reborn\Module\AbstractInfo
{
	protected $name = 'User';

	protected $displayName = array(
		'en' => 'User', 
		'my' => 'အသုံးပြုသူများ'
	);

	protected $version = '1.0';

	protected $description = array(
		'en' => 'User, Group, and Permission managament with Cataclyst\Sentry.', 
		'my' => 'အသုံးပြုသူများ၊ အဖွဲ့နေရာစီမံ ခန့်ခွဲခြင်း နှင့် မော်ဂျူး ခွင့်ပြုချက်များအား လုပ်ဆောင်နိုင်ပါသည်။ Cataclyst\Sentry ကို အသုံးပြုထားသည်။'
	);

	protected $description = 'User, Group, and Permission managament with Cataclyst\Sentry.';

	protected $author = 'K';

	protected $authorUrl = 'http://khaynote.com';

	protected $authorEmail = 'khayusaki@gmail.com';

	protected $frontendSupprot = true;

	protected $backendSupport = true;

	protected $uriPrefix = 'user';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = true;

	protected $roles = array(
		'user.create' => 'Create',
		'user.edit' => 'Edit',
		'user.delete' => 'Delete',
		'user.group' => 'Groups',
		'user.group.create' => 'Create Group',
		'user.group.edit' => 'Edit Group',
		'user.group.delete' => 'Delete Group',
		'user.permission' => 'Permission',
		'user.permission.edit' => 'Edit Permission',
	);

}
