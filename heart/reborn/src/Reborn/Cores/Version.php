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
    /**
     * Application Name
     *
     * @var string
     */
    const NAME = 'Reborn CMS';

    /**
     * Full version number and extra
     *
     * @var string
     */
    const FULL = '2.0.0-beta2';

    /**
     * Major version number
     *
     * @var string
     */
    const MAJOR = '2';

    /**
     * Minor version number
     *
     * @var string
     */
    const MINOR = '0';

    /**
     * Bug fixed version number
     *
     * @var string
     */
    const FIX = '0';

    /**
     * Version extra type
     *
     * @var string
     */
    const EXTRA = 'beta2';

    /**
     * Code name for Major version
     *
     * @var string
     */
    const CODE_NAME = 'rorb';

    /**
     * Release date of Reborn CMS Package
     *
     * @var string
     */
    const RELEASE = '16/09/2013';

    /**
     * URL of Reborn CMS Official Site
     *
     * @var string
     */
    const URL = 'http://www.reborncms.com';

    /**
     * Blog Feed Url of Reborn CMS
     *
     * @var string
     */
    //const REBORN_FEED = 'http://www.reborncms.com/blog/rss';

    /**
     * Get the Reborn CMS Full Version
     *
     * @return string
     **/
    public static function getVersion()
    {
        return static::FULL;
    }

    /**
     * Get the Application Name
     *
     * @return string
     **/
    public static function getAppName()
    {
        return static::NAME;
    }

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
