<?php

namespace {module};

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
		$menu->add('{table}', '{module}', $modUri, 'content', 35);
	}

	/**
	 * Module Toolbar Data for Admin Panel
	 *
	 * @return array
	 */
	public function moduleToolbar()
	{
		return array(
			'{table}' => array(
				'url'	=> '{uri}',
				'name'	=> '{module}',
				'info'	=> 'View All {module}',
				'class'	=> 'add'
			),
			'{table}_add' => array(
				'url'	=> '{uri}/create',
				'name'	=> 'Create {module}',
				'info'	=> 'Create New {module}',
				'class'	=> 'add'
			)
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
