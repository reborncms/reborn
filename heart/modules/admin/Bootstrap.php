<?php

namespace Admin;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('admin::dashboard', 'das');

		define('DASHBOARD_PATH', __DIR__);
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri) {}

	public function moduleToolbar()
	{
		return array();
	}

	public function settings()
	{
		return array();
	}

	public function register() {}

}
