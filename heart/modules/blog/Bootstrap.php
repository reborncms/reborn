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
			'blog_content_default_lang' => array(
				'type'	=> 'select',
				'options' => \Config::get('langcodes')
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
			'category' => array(
				'url' => 'blog/category',
				'name' => t('blog::blog.categories'),
				'info' => t('blog::blog.categories_info'),
				'class' => ''
			),
		);

		if (user_has_access('blog.create')) {
            $mod_toolbar['add'] = array(
				'url'	=> 'blog/create',
				'name'	=> t('blog::blog.add_post'),
				'info'	=> t('blog::blog.add_post_info'),
				'class'	=> 'add'
			);
        }

		return $mod_toolbar;
	}

	public function register()
	{
		// Make Class Alias
		\Alias::aliasRegister(array('Blog' => 'Blog\Facade\Blog'));

		\Event::on('user_deleted', function($param){
			return \Blog\Lib\Helper::changeAuthor($param->id);
		});

		\Event::on('reborn.dashboard.widgets.leftcolumn', function(){
			return \Blog\Lib\Helper::dashboardWidget();
		});
	}

}
