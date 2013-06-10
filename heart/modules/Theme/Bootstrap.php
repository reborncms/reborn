<?php

namespace Theme;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('theme::theme');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('theme', 'Themes', $modUri, 'appearance', $order = 35);
	}

	public function settings()
	{
		return array();
	}

	public function moduleToolbar()
	{
		$mod_toolbar = array(
			'add'	=> array(
				'url'	=> 'theme/upload',
				'name'	=> 'Upload a New Theme',
				'info'	=> 'Upload a new Theme',
				'class'	=> 'add'
			)
		);

		return $mod_toolbar;
	}

	public function eventRegister() {}
}
