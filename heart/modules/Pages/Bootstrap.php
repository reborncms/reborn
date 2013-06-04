<?php

namespace Pages;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

    public function boot() {}

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('pages', 'Pages', $modUri, 'content', '', 30);
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
                'name'	=> 'Add Page',
                'info'	=> 'Create new page',
                'class'	=> 'add'
            ),
        );

        return $mod_toolbar;
    }

    public function eventRegister() {}

}
