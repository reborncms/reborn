<?php

namespace Pages;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

    public function boot() 
    {
        \Translate::load('pages::pages');
    }

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('pages', t('pages::pages.titles.main_title'), $modUri, 'content', '', 30);
    }

    public function settings()
    {
        return array();
    }

    public function moduleToolbar()
    {
        $mod_toolbar = array(
            'add'	=> array(
                'url'	=> 'pages/create',
                'name'	=> t('pages::pages.titles.add_page'),
                'info'	=> t('pages::pages.titles.add_page_info'),
                'class'	=> 'add'
            ),
        );

        return $mod_toolbar;
    }

    public function eventRegister() {
        \Module::load('Pages');

        \Event::on('user_deleted', function($user){
            return \Pages\Lib\Helper::changeAuthor($user->id);
        });
    }

}
