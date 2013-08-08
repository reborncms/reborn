<?php

namespace Blog;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot() 
	{
		\Translate::load('blog::blog');
	}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('blog', t('blog::blog.title_main'), $modUri, 'content', '', 27);
	}

	public function settings()
	{
		return array(
			'blog_per_page' => array(
				'type' => 'text',
			),
			'blog_rss_items' => array(
				'type'	=> 'text',
			),
			'excerpt_length' => array(
				'type' => 'text',
			),
		);
	}

	public function moduleToolbar()
	{
		$mod_toolbar = array(
			'all' 	=> array(
				'url'	=> 'blog',
				'name'	=> t('blog::blog.all_posts'),
				'info'	=> t('blog::blog.all_posts_info'),
				'class'	=> ''
			),
			'add'	=> array(
				'url'	=> 'blog/create',
				'name'	=> t('blog::blog.add_post'),
				'info'	=> t('blog::blog.add_post_info'),
				'class'	=> 'add'
			),
			'category' => array(
				'url' => 'blog/category',
				'name' => t('blog::blog.categories'),
				'info' => t('blog::blog.categories_info'),
				'class' => ''
			),
		);

		return $mod_toolbar;
	}

	public function eventRegister() {}

}
