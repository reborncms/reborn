<?php

namespace Blog;

class Bootstrap extends \Reborn\Module\AbstractBootstrap
{

	public function boot() {}

	public function adminMenu(\Reborn\Util\Menu $menu, $modUri)
	{
		$menu->add('blog', 'Blog', $modUri, 'content', '', 27);
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
				'name'	=> 'All Posts',
				'info'	=> 'View All Posts',
				'class'	=> ''
			),
			'add'	=> array(
				'url'	=> 'blog/create',
				'name'	=> 'Add Post',
				'info'	=> 'Create new blog post',
				'class'	=> 'add'
			),
			'category' => array(
				'url' => 'blog/category',
				'name' => 'Categories',
				'info' => 'Manage Categories',
				'class' => ''
			),
		);

		return $mod_toolbar;
	}

	public function eventRegister() {}

}
