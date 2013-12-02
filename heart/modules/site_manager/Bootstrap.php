<?php

namespace SiteManager;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	/**
	 * This method will run when module boot.
	 *
	 * @return void;
	 */
	public function boot() {}

	/**
	 * Menu item register method for admin panel
	 *
	 * @return void
	 */
	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		if ($this->app->site_manager->isMulti()) {
			$menu->add('module', 'Multisite Manager', $modUri, 'utilities', $order = 55);
		}
	}

	/**
	 * Module Toolbar Data for Admin Panel
	 *
	 * @return array
	 */
	public function moduleToolbar()
	{
		return array(
			'index'	=> array(
                'url'	=> 'site',
                'name'	=> 'All',
                'info'	=> 'View All',
                'class'	=> 'add'
            ),
            'add'	=> array(
                'url'	=> 'site/create',
                'name'	=> 'Create',
                'info'	=> 'Create Site',
                'class'	=> 'add'
            ),
        );
	}

	/**
	 * Setting attributes for Module
	 *
	 * @return array
	 */
	public function settings()
	{
		return array();
	}

	/**
	 * Register method for Module.
	 * This method will call application start.
	 * You can register at requirement for Reborn CMS.
	 *
	 */
	public function register() {}

}
