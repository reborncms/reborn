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
		$menu->add('navigation', t('navigation::navigation.menu'), $modUri, 'appearance', 35);
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
					'name'	=> t('navigation::navigation.toolbar.link'),
					'info'	=> t('navigation::navigation.toolbar.link_info')
				),
				'group'	=> array(
					'url'	=> 'navigation/group',
					'name'	=> t('navigation::navigation.toolbar.group'),
					'info'	=> t('navigation::navigation.toolbar.group_info')
				));

		return $mod_toolbar;
	}

	public function register()
	{
		// Make Class Alias
        \Alias::aliasRegister(array('Navigation' => 'Navigation\Builder\Manager'));
	}

}
