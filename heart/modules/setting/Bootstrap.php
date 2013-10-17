<?php

namespace Setting;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		// Call Setting UI Extend Event
		\Event::call('setting.ui.extends');

		\Translate::load('setting::setting');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('settings', 'System', $modUri, null, null, 90);
	}

	public function settings()
	{
		return array();
	}

	public function moduleToolbar()
	{
		return array();
	}

	public function register()
	{

	}

}
