<?php

namespace Module;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('module::module');
	}

	public function settings()
	{
		return array();
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('module', 'Module Manager', $modUri, 'utilities', $order = 35);
	}

	public function moduleToolbar()
	{
		$mod_toolbar = array(
			'index'	=> array(
                'url'	=> 'module',
                'name'	=> 'Manage',
                'info'	=> 'Management Area for Reborn Module',
                'class'	=> 'add'
            ),
            'add'	=> array(
                'url'	=> 'module/upload',
                'name'	=> 'Upload New Module',
                'info'	=> 'Upload new module for Reborn CMS',
                'class'	=> 'add'
            ),
        );

        return $mod_toolbar;
	}

	public function eventRegister() {}

}
