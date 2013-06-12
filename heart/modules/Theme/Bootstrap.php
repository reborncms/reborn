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
		$menu->add('theme', 'Themes', $modUri, 'appearance', $order = 35);
		$menu->add('theme-editor', 'Editor', $modUri.'/editor', 'appearance', $order = 36);
	}

	public function settings()
	{
		return array();
	}

	public function moduleToolbar()
	{
		$uri = \Uri::segment(3);

		if( $uri == 'editor' ) {
			$mod_toolbar = array();
		} else {
			$mod_toolbar = array(
				'add'	=> array(
					'url'	=> 'theme/upload',
					'name'	=> 'Upload a New Theme',
					'info'	=> 'Upload a new Theme',
					'class'	=> 'add'
				)
			);
		}

		return $mod_toolbar;
	}

	public function eventRegister() {}
}
