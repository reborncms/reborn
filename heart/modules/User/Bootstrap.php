<?php

namespace User;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('user::user');
		\Translate::load('user::permission');
		\Translate::load('user::group');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('user', 'Users', $modUri, 'user_management');
		$menu->add('group', 'Groups', $modUri.'/group', 'user_management');
		$menu->add('permission', 'Permission', $modUri.'/permission', 'user_management');
	}

	public function settings()
	{
		return array();
	}

	public function moduleToolbar()
	{
		$uri = \Uri::segment(3);

		if( $uri != 'permission' )
		{
			if( $uri == 'group' ) {
				$mod_toolbar = array(
					'add_group'	=> array(
						'url'	=> 'user/group/create',
						'name'	=> 'Create Group',
						'info'	=> 'Create new group',
						'class'	=> 'add'
					)
				);
			} else {
				$mod_toolbar = array(
					'add'	=> array(
						'url'	=> 'user/create',
						'name'	=> 'Create User',
						'info'	=> 'Create a new user',
						'class'	=> 'add'
					),
				);
			}
		}
		else {
			$mod_toolbar = array();
		}

		return $mod_toolbar;
	}

	public function eventRegister()
	{
		// Laster
		require __DIR__.DS."helpers.php";
	}
}
