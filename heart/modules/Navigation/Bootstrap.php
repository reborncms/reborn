<?php

namespace Navigation;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('navigation::navigation', 'nav');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('navigation', 'Navigation', $modUri, 'appearance', 35);
	}

	public function settings()
	{
		return array();
	}

	public function moduleToolbar()
	{
		$mod_toolbar = array(
				'links'	=> array(
					'url'	=> 'navigation',
					'name'	=> 'Links',
					'info'	=> 'View Navigation Links'
				),
				'group'	=> array(
					'url'	=> 'navigation/group',
					'name'	=> 'Group',
					'info'	=> 'View Navigation Group'
				));

		return $mod_toolbar;
	}

	public function eventRegister()
	{
		$file =realpath(__DIR__).DS.'Events'.DS.'register.php';

		\Event::on('reborn.parser.create', function($parser) use ($file) {
			require $file;
		});
	}

}
