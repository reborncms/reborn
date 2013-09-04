<?php

namespace Comment;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot()
	{
		\Translate::load('comment::comment');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('Comment', t('comment::comment.comment.plu'), $modUri, 'content', '', 35);

	}

	public function moduleToolbar()
	{
		$uri = \Uri::segment(3);

		$mod_toolbar = array();

		return $mod_toolbar;
	}

	public function settings()
	{
		return array(
			'comment_gravatar_size' => array(
				'type' => 'text',
			),
			'akismet_api_key' => array(
				'type'	=> 'text',
			),
			'use_default_style' => array(
				'type' => 'checkbox'
			),
			'comment_enable' => array(
				'type'	=> 'select',
				'options'	=> array(
					'enable' => 'Enable',
					'disable' => 'Disable'
				)
			),
			'comment_need_approve' => array(
				'type'	=> 'checkbox'
			),
		);
	}

	public function register()
	{
		// Make Class Alias
		\Alias::aliasRegister(array('Comment' => 'Comment\Facade\Comment'));

		\Event::on('user_deleted', function($user){
			return \Comment\Lib\Helper::userDeleted($user);
		});

		\Event::on('reborn.dashboard.widgets.leftcolumn', function(){
			return \Comment\Lib\Helper::dashboardWidget();
		});
	}
}
