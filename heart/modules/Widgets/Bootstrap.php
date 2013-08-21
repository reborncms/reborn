<?php

namespace Widgets;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot() 
	{
		\Translate::load('widgets::widgets');
	}

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

	public function register() 
	{
		$file = realpath(__DIR__).DS.'Events'.DS.'register.php';
		
		\Event::on('reborn.parser.create', function($parser) use ($file) {
			require $file;
		});
	}

}
