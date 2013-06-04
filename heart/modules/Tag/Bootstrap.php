<?php

namespace Tag;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot() {}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('tag', 'Tag', $modUri, 'content', '', 37);
	}

	public function settings()
	{
		return array();
	}

	public function moduleToolbar()
	{
		$mod_toolbar = array();

		return $mod_toolbar;
	}

	public function eventRegister() {}

}
