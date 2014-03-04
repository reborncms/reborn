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
		$childs = array();

		$childs[] = array('title' => t('user::user.menu'), 'uri' => '');
		if (user_has_access('user.group')) {
			$childs[] = array('title' => t('user::group.menu'), 'uri' => 'group');
		}
		if (user_has_access('user.permission')) {
			$childs[] = array('title' => t('user::permission.menu'), 'uri' => 'permission');
		}
			
		$menu->group($modUri, t('navigation.user_management'), 'icon-users', 50, $childs);
	}

	public function settings()
	{
		return array(
			'user_registration' => array(
				'type'	=> 'select',
				'options' => array('enable'=>'Enable','disable'=>'Disable')
			),
		);
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
						'name'	=> t('user::group.modToolbar.name'),
						'info'	=> t('user::group.modToolbar.info'),
						'class'	=> 'add'
					)
				);
			} else {
				if (user_has_access('user.create')) {
					$mod_toolbar = array(
						'add'	=> array(
							'url'	=> 'user/create',
							'name'	=> t('user::user.modToolbar.name'),
							'info'	=> t('user::user.modToolbar.info'),
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

	public function register()
	{
		// Laster
		require __DIR__.DS."helpers.php";

		\Event::on('reborn.dashboard.widgets.rightcolumn', function(){
			return \User\Lib\Helper::dashboardWidget();
		});
	}
}
