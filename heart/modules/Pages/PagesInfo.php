<?php

namespace Pages;

class PagesInfo extends \Reborn\Module\AbstractInfo
{
    protected $name = 'Pages';

    protected $version = '1.0';

    protected $description = 'Manage Pages of your website';

    protected $author = 'Li Jia Li';

    protected $authorUrl = 'http://dragonvirus.com';

    protected $authorEmail = 'limonster.li@gmail.com';

    protected $frontendSupport = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'pages';

	protected $allowToChangeUriPrefix = false;

	protected $useAsDefaultModule = true;

}
