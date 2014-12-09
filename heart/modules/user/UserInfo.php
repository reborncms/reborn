<?php

namespace User;

class UserInfo extends \Reborn\Module\AbstractInfo
{
    protected $name = 'User';

    protected $displayName = array(
        'en' => 'User',
        'my' => 'အသုံးပြုသူများ',
        'tr' => 'Kullanıcılar'
    );

    protected $version = '2.0';

    protected $description = array(
        'en' => 'User, Group, and Permission managament with Cataclyst\Sentry.',
        'my' => 'အသုံးပြုသူများ၊ အဖွဲ့နေရာစီမံ ခန့်ခွဲခြင်း နှင့် မော်ဂျူး ခွင့်ပြုချက်များအား လုပ်ဆောင်နိုင်ပါသည်။ Cataclyst\Sentry ကို အသုံးပြုထားသည်။',
        'tr' => 'Kullanıcılar, Kullanıcı Grupları ve izinleri yönetin'
    );

    protected $author = 'K / Nyan Lynn Htut';

    protected $authorUrl = 'http://www.reborncms.com';

    protected $authorEmail = 'reborncms@gmail.com';

    protected $frontendSupprot = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'user';

    protected $allowToChangeUriPrefix = false;

    protected $useAsDefaultModule = true;

    protected $sharedData = true;

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

    /**
    * Variable for Allow Custom Field.
    * If you allow custom field in your module, set true
    *
    * @var boolean
    **/
    protected $allowCustomfield = true;

}
