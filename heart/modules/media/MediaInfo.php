<?php

namespace Media;

/**
 * Info class for media module
 *
 * @package Media
 * @author RebornCMS Development Team
 **/
class MediaInfo extends \Reborn\Module\AbstractInfo
{

    protected $name = 'Media';

    protected $version = '1.0';

    protected $author = 'Yan Naing';

    protected $authorUrl = 'http://www.myanmarlinks.net';

    protected $authorEmail = 'bulletson.geek@gmail.com';

    protected $frontendSupport = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'media';

    protected $allowToChangeUriPrefix = false;

    protected $useAsDefaultModule = false;

    protected $displayName = array(
        'en'    => 'Media Manager',
        'my'    => 'မီဒီယာ စီမံဌာန',
        'tr'    => 'Medya Yöneticisi'
        );

    protected $description = array(
        'en'    => 'Official media manager of RebornCMS',
        'my'    => 'ဖိုင်များ၊ မီဒီယာ ဖိုင်လ်များကို ကိုင်တွယ်စီမံနိုင်မည့် မော်ဂျူး',
        'tr'    => 'RebornCMS resmi dosya yöneticisi'
        );

    protected $sharedData = false;

    /**
     * {@inheritdoc}
     **/
    protected $allowSharedByUser = false;

} // END class MediaInfo
