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
		$menu->add('user', t('user::user.menu'), $modUri, 'user_management');
		if (user_has_access('user.group')) {
		$menu->add('group', t('user::group.menu'), $modUri.'/group', 'user_management');
		}
		if (user_has_access('user.permission')) {
			$menu->add('permission', t('user::permission.menu'), $modUri.'/permission', 'user_management');
		}
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
			if( $uri == 'group' and user_has_access('user.group.create') ) {
				$mod_toolbar = array(
					'add_group'	=> array(
						'url'	=> 'user/group/create',
						'name'	=> 'Create Group',
						'info'	=> 'Create new group',
						'class'	=> 'add'
					)
				);
			} else {
				if (user_has_access('user.create')) {
					$mod_toolbar = array(
						'add'	=> array(
							'url'	=> 'user/create',
							'name'	=> 'Create User',
							'info'	=> 'Create a new user',
							'class'	=> 'add'
						),
					);
				} else {
					$mod_toolbar = array();
				}
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
