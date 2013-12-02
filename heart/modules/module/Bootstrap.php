<?php

namespace Module;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('module::module');
	}

	public function settings()
	{
		return array();
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('module', t('module::module.title'), $modUri, 'utilities', $order = 35);
	}

	public function moduleToolbar()
	{
		$mod_toolbar = array(
			'index'	=> array(
                'url'	=> 'module',
                'name'	=> t('module::module.manage'),
                'info'	=> t('module::module.manage_info'),
                'class'	=> 'add'
            ),
            'add'	=> array(
                'url'	=> 'module/upload',
                'name'	=> t('module::module.upload_area'),
                'info'	=> t('module::module.upload_area_info'),
                'class'	=> 'add'
            ),
        );

        if (\Module::get('module', 'db_version') === '1.0') {
        	$mod_toolbar['fix'] = array(
        		'url'	=> 'module/update',
                'name'	=> 'Update',
                'info'	=> 'Please Update Module Manager',
                'class'	=> 'update-btn'
        	);
        }

        return $mod_toolbar;
	}

	public function register() {}

}
