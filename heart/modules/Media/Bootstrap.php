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
        \Translate::load('media::media', 'm');
    }

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('media', \Translate::get('m.title.title'), $modUri, null,25);
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
                    'name'  => \Translate::get('m.btn.upload'),
                    'info'  => \Translate::get('m.info.upload'),
                    'id'    => 'media_upload',
                ),
                'folder'    => array(
                    'url'   => 'media/createFolder/',
                    'name'  => \Translate::get('m.btn.create'),
                    'info'  => \Translate::get('m.info.create'),
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