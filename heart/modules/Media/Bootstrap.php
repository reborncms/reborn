<?php

namespace Media;
//
/**
 * Bootstrapt class for media module
 *
 * @package Media
 * @author RebornCMS Development Team
 **/
class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

    public function boot()
    {
        \Translate::load('media::media');
        \Translate::load('media::file');
        \Translate::load('media::folder');
    }

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('media', 'Media Manager', $modUri, null, 27);
    }

    public function settings()
    {
        return array();
    }

    public function moduleToolbar()
    {
        $mod_toolbar = array(
                'upload'    => array(
                    'url'   => 'media/upload/',
                    'name'  => 'Upload',
                    'info'  => 'Upload your files',
                    'id'    => 'media_upload',
                ),
                'folder'    => array(
                    'url'   => 'media/createFolder/',
                    'name'  => 'New Folder',
                    'info'  => 'Create a new folder',
                    'id'    => 'media_create_folder',
                ),
            );

        return $mod_toolbar;
    }

    public function eventRegister ()
    {
        // Nothing to do now
    }

} // END class Bootstrap