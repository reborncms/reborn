<?php

namespace Blog;

use Blog\Model\Blog as Model;
use Blog\Presenter\PostPresenter;

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

		$this->postsBind();

		\Event::on('user_deleted', function($param){
			return \Blog\Lib\Helper::changeAuthor($param->id);
		});

		\Event::on('reborn.dashboard.widgets.leftcolumn', function(){
			return \Blog\Lib\Helper::dashboardWidget();
		});
	}

	/**
	 * Bind Bog Post data for Theme
	 * Avaliable Passing Param
	 *  - category category slug. If you need multiple category, use comma (eg: news,announcement)
	 *  - limit Blog post limit. Defult is 5
	 *  - offset Blog post offset
	 *  - order [order_key] Default is created_at
	 *  - order_dir [asc|desc] Default is desc
	 */
	protected function postsBind()
	{
		\ViewData::bind('blog_posts',
			function($options) {
				return with($ins = new \Blog\Lib\Blog())->post($options);
			}
		);
	}

}
