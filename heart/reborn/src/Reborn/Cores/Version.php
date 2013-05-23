<?php

namespace Reborn\Cores;

/**
 * Version Control Class for Reborn
 *
 * @package Reborn\Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class Version
{
    const NAME = 'Reborn CMS';

    const FULL = '2.0.0-alpha';

    const MAJOR = '2';

    const MINOR = '0';

    const FIX = '0';

    const EXTRA = 'alpha';

    const CODE_NAME = 'rorb';

    // Release date of Reborn CMS Package
    const RELEASE = '23/05/2013';

    // URL of Reborn CMS Official Site
    const URL = 'http://www.reborncms.com';

    // Feed of Reborn CMS Official Site
    //const REBORN_FEED = 'http://www.reborncms.com/blog/rss';

    /**
     * Compare the Given Version and Current Version of Reborn CMS.
     *
     * @param string $version Version of the given to compare with current version.
     * @return int Return is same with version_compare() function from PHP.
     */
    public static function compare($new_version)
    {
        $currentVersion = str_replace(' ', '', self::FULL);
        $newVersion = str_replace(' ', '', $new_version);

        return version_compare($newVersion, $currentVersion);
    }

    /*public static function check()
    {
        $server_verison = File::getRemote('http://www.reborncms.com/check/version?current=1.0.0-beta');

        if($server_verison['status'] == 'needUpdate')
        {
            static::update($server_verison);
        }
    }

    public static function update($data = array())
    {
        $file_host = $data['fileHost'];


    }*/
} // END class Version
