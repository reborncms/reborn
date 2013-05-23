<?php

namespace {module};

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot() {}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		// eg: $menu->add('name', 'Title', 'link', $parent_menu = null, $order = 35)
	}

	public function moduleToolbar()
	{
		return array();
	}

	public function settings()
	{
		return array();
	}

	public function eventRegister() {}

}
