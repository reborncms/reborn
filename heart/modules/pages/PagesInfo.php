<?php

namespace Pages;

class PagesInfo extends \Reborn\Module\AbstractInfo
{
    protected $name = 'Pages';

    protected $displayName = array(
        'en'    => 'Pages',
        'my'    => 'စာမျက်နှာများ'
    );

    protected $version = '1.1';

    protected $description = array(
        'en'    =>'Manage Pages of your website',
        'my'    =>'သင့် ဝဘ်ဆိုဒ် အတွင်းရှိ စာမျက်နှာများ စီမံရန်'
    );

    protected $author = 'Li Jia Li';

    protected $authorUrl = 'http://dragonvirus.com';

    protected $authorEmail = 'limonster.li@gmail.com';

    protected $frontendSupport = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'pages';

    protected $allowToChangeUriPrefix = false;

    protected $useAsDefaultModule = true;

    protected $sharedData = false;

    /**
    * Variable for Allow Custom Field.
    * If you allow custom field in your module, set true
    *
    * @var boolean
    **/
    protected $allowCustomfield = false;

    protected $roles = array(
            'pages.create'      => 'Create',
            'pages.edit'        => 'Edit',
            'pages.delete'      => 'Delete',
    );

}
