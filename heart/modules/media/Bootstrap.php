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
        require __DIR__ . DS . 'helpers.php';
        \Translate::load('media::media', 'm');
    }

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('media', t('media::media.title.title'), $modUri,
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
                    'name'  => t('media::media.btn.upload'),
                    'info'  => t('media::media.info.upload'),
                    'id'    => 'media_upload',
                ),
                'folder'    => array(
                    'url'   => 'media/create-folder/',
                    'name'  => t('media::media.btn.create'),
                    'info'  => t('media::media.info.create'),
                    'id'    => 'media_create_folder',
                ),
            );

        return $mod_toolbar;
    }

    public function register ()
    {
        // Load Zebra_Image
        require __DIR__.DS.'vendor'.DS.'Zebra'.DS.'Zebra_Image.php';

        // Make Class Alias
        \Alias::aliasRegister(array('Media' => 'Media\Facade\Media'));

        // Extend Form for featured thumbnail
        \Form::extend('thumbnail', function($name, $value, $width = null, $labels = array()){
            return \Media::thumbnailForm($name, $value, $width, $labels);
        });

        \Form::extend('upload', function($folderId){
            return \Media::uploadForm($folderId);
        });
    }

} // END class Bootstrap
