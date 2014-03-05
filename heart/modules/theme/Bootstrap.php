<?php

namespace Theme;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

    public function boot()
    {
        \Translate::load('theme::theme');
        \Translate::load('theme::editor');
    }

    public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
    {
        $menu->add('theme', t('theme::theme.menu'), $modUri, 'appearance', $order = 35);
        if (user_has_access('theme.editor')) {
            $menu->add('theme-editor', t('theme::editor.menu'), $modUri.'/editor', 'appearance', $order = 36);
        }

    }

    public function settings()
    {
        return array();
    }

    public function moduleToolbar()
    {
        $uri = \Uri::segment(3);

        if ($uri == 'editor') {
            $mod_toolbar = array();
        } else {
            $mod_toolbar = array(
                'add'	=> array(
                    'url'	=> 'theme/upload',
                    'name'	=> t('theme::theme.modToolbar'),
                    'info'	=> t('theme::theme.modToolbar'),
                    'class'	=> 'add'
                )
            );
        }

        return $mod_toolbar;
    }

    public function register() {}
}
