<?php

namespace Field;

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
		$menu->add('field', 'Field', $modUri, 'content', 46);
	}

	/**
	 * Module Toolbar Data for Admin Panel
	 *
	 * @return array
	 */
	public function moduleToolbar()
	{
		$mod_toolbar = array(
			'all' 	=> array(
				'url'	=> 'field',
				'name'	=> 'Field',
				'info'	=> 'All Field',
				'class'	=> ''
			),
			'create' => array(
				'url' => 'field/create',
				'name' => 'Create',
				'info' => 'Create new field',
				'class' => ''
			),
			'all_group' => array(
				'url'	=> 'field/group',
				'name'	=> 'Group',
				'info'	=> 'All Group',
				'class'	=> ''
			),
			'create_group' => array(
				'url' => 'field/group-create',
				'name' => 'Create Group',
				'info' => 'Create new group',
				'class' => ''
			),
		);

		return $mod_toolbar;
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
	public function register()
	{
		require __DIR__.DS.'helpers.php';

		// Make Class Alias
		\Alias::aliasRegister(array('Field' => 'Field\Facade\Field'));

		$this->app->bind('\Field\Builder', function($c) {
			return new \Field\Builder($c);
		});
	}

}
