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
		$menu->add('navigation', t('nav.menu'), $modUri, 'appearance', 35);
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
					'name'	=> t('nav.toolbar.link'),
					'info'	=> t('nav.toolbar.link_info')
				),
				'group'	=> array(
					'url'	=> 'navigation/group',
					'name'	=> t('nav.toolbar.group'),
					'info'	=> t('nav.toolbar.group_info')
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
