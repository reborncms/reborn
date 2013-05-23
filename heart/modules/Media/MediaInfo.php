<?php

namespace Media;
//
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

    protected $description = 'Media module for RebornCMS';

    protected $author = 'Yan Naing';

    protected $authorUrl = 'http://www.myanmarlinks.net';

    protected $authorEmail = 'bulletson.geek@gmail.com';

    protected $frontendSupport = true;

    protected $backendSupport = true;

    protected $uriPrefix = 'media';

    protected $allowToChangeUriPrefix = false;

    protected $useAsDefaultModule = false;

} // END class MediaInfo