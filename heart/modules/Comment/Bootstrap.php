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
		$menu->add('Comment', 'Comment', $modUri, 'content', '', 35);
		
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
		);
	}
	
	public function eventRegister()
	{
		// Sampler
		/*\Event::add('blog.post_create', function($title, $author){

			$msg = "Blog post ".$title.' is created by '.$author.' at '.date('d-m-Y H:i');
			// Blog post created record is save at log file.
			\Log::info($msg);
		});*/
	}
}
