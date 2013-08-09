<?php

namespace Blog;

class Widget extends \Reborn\Widget\AbstractWidget
{

	protected $properties = array(
			'name' 			=> 'Blog Module Widget',
			'sub' 			=> array(
				'posts' 	=> array(
					'title' => 'Blog Post',
					'description' => 'Latest Blog Posts which is posted last days',
				),
				/*'archive' 	=> array(
					'title' =>'Blog Archive',
					'description' => 'Blog by Year and month',
				),*/
				'category' 	=> array(
					'title' => 'Blog Category',
					'description' => 'Blog Category List',
				),
				'tagCloud'	=> array(
					'title' => 'Blog Tag Cloud',
					'description' => 'Blog Tag Cloud',
				),
			),
			'author' => 'Reborn CMS Development Team'
		);

	public function save() {}

	public function update() {}

	public function delete() {}

	public function options() 
	{
		return array(
			'posts' => array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
				'limit' 	=> array(
					'label' 	=> 'Number of Posts',
					'type'		=> 'text',
				),
			),
			'archive' => array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
				'show_type'		=> array(
					'label'		=> 'Yearly or Monthly',
					'type'		=> 'select',
					'options'	=> array(
						'yearly'	=> 'Yearly',
						'monthly'	=> 'Monthly',
					),
				),
			),

			'category' 	=> array(
				'title' => array(
					'label' 	=> 'Title',
					'type'		=> 'text',
					'info'		=> 'Leave it blank if you don\'t want to show your widget title',
				),
				/*'show_type' 	=> array(
					'label'		=> 'Show Type',
					'type'		=> 'select',
					'options'	=> array(
						'cat'	=> 'Category',
						'post'	=> 'Blog Posts',
					)
				),*/
			),
		);
	}

	/**
	 * Query the Blog Posts
	 *
	 * @return string
	 **/
	public function posts()
	{
		if(\Module::isDisabled('Blog')) {
			return null;
		}

		$title = $this->get('title', 'Latest Blog Posts');

		$limit = $this->get('limit', 5);
		$offset = $this->get('offset', 0);
		$order = $this->get('order', 'created_at');
		$order_dir = $this->get('order_dir', 'desc');

		\Module::load('Blog');
		$posts = \Blog\Model\Blog::with('category', 'author')
							->where('status', 'live')
							->where('created_at', '<=', date('Y-m-d H:i:s'))
							->orderBy($order, $order_dir)
							->take($limit)
							->skip($offset)
							->get();

		return $this->show(array('posts' => $posts, 'title' => $title), 'post');
	}

	/**
	 * Not Ready Yet! (#TODO)
	 *
	 * @return void
	 **/
	public function popular() {}

	/**
	 * Get the Blog Categories
	 *
	 * @return string
	 **/
	public function category()
	{
		if(\Module::isDisabled('Blog')) {
			return null;
		}

		\Module::load('Blog');

		$data = array();

		$data['categories'] = \Blog\Model\BlogCategory::all();

		$data['title'] = $this->get('title', 'Blog Catagories');

		return $this->show($data, 'category');
	}

	/**
	 * Blog Post Archive Widget
	 *
	 * @return string
	 **/
	public function archive()
	{
		if(\Module::isDisabled('Blog')) {
			return null;
		}

		\Module::load('Blog');
		$title = $this->get('title', 'Archives');
		$limit = $this->get('limit', 5);
		$data = array();

		$data['title'] = $this->get('title', 'Blog Archives');
		$data['list'] = array();

		return $this->show($data, 'archive');
	}

	/**
	 * Get the Blog Tag Cloud
	 *
	 * @return string
	 **/
	public function tagCloud()
	{
		if(\Module::isDisabled('Tag') || \Module::isDisabled('Blog')) {
			return null;
		}

		\Module::load('Tag');
		\Module::load('Blog');

		$arr = array(
				'maxFont' => $this->get('maxsize', 26),
				'minFont' => $this->get('minsize', 10),
				'fontUnit' => $this->get('unit', 'pt'),
				'wrap' => $this->get('wrap', ''),
				'format' => $this->get('format', 'font'),
				'classPrefix' => $this->get('class_prefix', 'tag'),
				'order' => $this->get('order', 'random'),
				'orderDir' => $this->get('order_dir', false),
				'title' => $this->get('tag_title', 'Total posts %s'),
				'url' => $this->get('url', 'blog/tag/'),
			);
		$tc = new \Reborn\Util\TagCloud($arr);

		$posts = \Blog\Model\Blog::where('status', 'live')
							->where('created_at', '<=', date('Y-m-d H:i:s'))
							->get(array('id'));
		if ($posts->isEmpty()) {
			return null;
		}

		foreach ($posts as $p) {
			$ids[] = $p->id;
		}

		$tags = \Tag\Model\TagsRelationship::where('object_name', 'blog')
							->whereIn('object_id', array_values($ids))->get();

		foreach ($tags as $t) {
			$tc->add($t->tag->name);
		}
		$data = array();

		$data['tag_body'] = $tc->generate();

		$data['title'] = $this->get('title', 'Tag Cloud');

		return $this->show($data, 'tagCloud');
	}

	public function render()
	{
		return $this->posts();
	}
}
