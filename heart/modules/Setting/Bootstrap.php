<?php

namespace Setting;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Alias::aliasRegister(array('SettingHelper' => 'Setting\Lib\Helper'));
		\Translate::load('setting::setting');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('setting_system', 'System', $modUri.'/system', 'settings', '', 10);
		$settings = \Setting::getFromModules();

		if (isset($settings['modules'])) {
			foreach ($settings['modules'] as $mod => $val) {
				if (\Module::isEnabled($mod)) {
					$menu->add('setting_'.$mod, $mod, $modUri.'/module/'.strtolower($mod), 'settings');
				}
			}
		}
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
