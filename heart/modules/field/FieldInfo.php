<?php

namespace Field;

class FieldInfo extends \Reborn\Module\AbstractInfo
{
    protected $name = 'Field';

    protected $version = '1.0';

    protected $displayName = array(
                                'en' => 'Field'
                                );

    protected $description = array(
                            'en' => 'Custom Fields Module'
                            );

    protected $author = 'Nyan Lynn Htut';

    protected $authorUrl = 'http://reborncms.com';

    protected $authorEmail = 'lynnhtut87@gmail.com';

    protected $frontendSupport = false;

    protected $backendSupport = true;

    protected $useAsDefaultModule = false;

    protected $uriPrefix = 'field';

    protected $allowToChangeUriPrefix = false;

    protected $sharedData = false;

}
