<?php

namespace Widgets;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot() {}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('widget', 'Widgets', 'widget', 'appearance', '', 35);
	}

	public function moduleToolbar()
	{
		return array();
	}

	public function settings()
	{
		return array();
	}

	public function eventRegister() 
	{
		$file = realpath(__DIR__).DS.'Events'.DS.'register.php';

		\Module::load('Widgets');
		
		\Event::on('reborn.parser.create', function($parser) use ($file) {
			require $file;
		});
	}

}
