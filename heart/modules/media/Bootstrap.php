<?php

namespace Media;

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
        /*require __DIR__ . DS . 'Lib' . DS .'Helpers.php';*/
        \Translate::load('media::media', 'm');
    }

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('media', \Translate::get('media::media.title.title'), $modUri,
        null,25);
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
                    'name'  => \Translate::get('media::media.btn.upload'),
                    'info'  => \Translate::get('media::media.info.upload'),
                    'id'    => 'media_upload',
                ),
                'folder'    => array(
                    'url'   => 'media/create-folder/',
                    'name'  => \Translate::get('media::media.btn.create'),
                    'info'  => \Translate::get('media::media.info.create'),
                    'id'    => 'media_create_folder',
                ),
            );

        return $mod_toolbar;
    }

    public function register ()
    {
        // Nothing to do now
    }

} // END class Bootstrap
