<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

use Reborn\Auth\Sentry\Eloquent\User;

use Field;

class DataProvider 
{

	/**
	 * Blog Model
	 *
	 **/
	protected $blog;

	/**
	 * Blog Categories
	 *
	 **/
	protected $blog_categories;


	public function __construct()
	{

		$this->blog = new Blog;
		$this->blog_categories = new BlogCategory;

	}

	/**
	 * Get Blog Public Posts Instance
	 *
	 * @return void
	 * @author 
	 **/
	protected function getPostsInstance($conditions = array('active', 'notOtherLang', 'embed_data', 'order'))
	{

		$blog = $this->blog;

		if (in_array('active', $conditions)) {

			$blog = $blog->active();

		}

		if (in_array('notOtherLang', $conditions)) {
			
			$blog = $blog->notOtherLang();

		}

		if (in_array('embed_data', $conditions)) {
			
			$blog = $blog->with(array('category','author'));

		}

		if (in_array('order', $conditions)) {
			
			$blog = $blog->orderBy('created_at', 'desc');

		}

		return $blog;

	}


	/**
	 * Get All active posts with default languages
	 *
	 * @return void
	 **/
	public function allPublicPosts($limit = 10, $offset = 0)
	{

		$blog = $this->getPostsInstance();

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());

	}

	public function getPostsBy($conditions = array(), $limit = 10, $offset = 0)
	{

		$blog = $this->getPostsInstance();

		if (isset($conditions['wheres'])) {

			foreach ($conditions['wheres'] as $key => $value) {

				if ($key == 'tag') {

					$blog_ids = \Tag\Lib\Helper::getObjectIds($value, 'blog');

					if(empty($blog_ids)) {
						return array();
					}

					$blog->whereIn('id', $blog_ids);

				} else {

					$blog->where($key, $value);

				}
			}
		}

		if (isset($conditions['before_after'])) {
			
			foreach ($conditions['before_after'] as $key => $value) {

				switch ($key) {
					case 'since':
						$blog->where(\DB::raw('UNIX_TIMESTAMP(created_at)'), '>', $value);
						break;

					case 'until':
						$blog->where(\DB::raw('UNIX_TIMESTAMP(created_at)'), '<', $value);
						break;
					
					case 'after_id':
						$blog->where('id', '>', $value);
						break;

					case 'before_id':
						$blog->where('id', '<', $value);
						break;
				}

			}

		}

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());

	}

	/**
	 * Get Posts by Muti-values
	 *
	 * @return void
	 * @author 
	 **/
	public function getPostsWhereIn($key, $values, $limit = 10, $offset = 0)
	{

		$blog = $this->getPostsInstance();

		$blog->whereIn($key, $values);

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());
	}

	/**
	 * Count posts by whereIn
	 *
	 * @return void
	 * @author 
	 **/
	public function countWhereIn($key, $values)
	{

		$blog = $this->getPostsInstance(array('active', 'notOtherLang'));

		$blog->whereIn($key, $values);

		return $blog->count();

	}

	/**
	 * Get single blog post
	 * 
	 **/
	public function post($id)
	{

		$blog = $this->getPostsInstance(array('active', 'embed_data'));

		$blog = $blog->where('id', $id)
             			->first();

        return $this->getCustomFields($blog);

	}

	/**
	 * Get single blog by slug
	 *
	 * @return void
	 * @author 
	 **/
	public function getPostBySlug($slug, $active = true)
	{

		if ($active) {

			$condition = array('active', 'embed_data');

		} else {

			$condition = array('embed_data');

		}

		$blog = $this->getPostsInstance($condition);

        $blog = $blog->where('slug', $slug)
                	->first();

        return $this->getCustomFields($blog);

	}

	/**
	 * Get Archives post by year and month
	 *
	 * @return void
	 * @author 
	 **/
	public function getArchives($year, $month = null, $limit = 0, $offset = 0, $count = false)
	{
		$blog = $this->getPostsInstance(array('active', 'embed_data'));

		$blog = $blog->where(\DB::raw('YEAR(created_at)'), $year);

		if ($month) {

			$blog->where(\DB::raw('MONTH(created_at)'), $month);

		}


		if ($count) {

			return $blog->count();

		}

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $this->getCustomFields($blog->get());
	}

	public function countBy($conditions = array())
	{
		$blog = $this->getPostsInstance(array('active', 'notOtherLang'));

		foreach ($conditions as $key => $value) {

			if ($key == 'tag') {

				$blog_ids = \Tag\Lib\Helper::getObjectIds($value, 'blog');

				if(empty($blog_ids)) {

					return array();
				}

				$blog->whereIn('id', $blog_ids);

			} else {

				$blog->where($key, $value);

			}
		}

		return $blog->count();
	}

	/**
	 * Get custom Fields 
	 *
	 * @return void
	 * @author 
	 **/
	protected function getCustomFields($blog)
	{
		return Field::get('blog', $blog, 'custom_field');
	}

	/**
	 * Get Categories
	 *
	 * @return void
	 * @author 
	 **/
	public function getCategories()
	{
		return BlogCategory::all();	
	}

	/**
	 * List of Authors
	 *
	 * @return void
	 * @author 
	 **/
	public function getAuthors()
	{
		$author_ids = array_values(array_unique(Blog::lists('author_id')));

		return User::whereIn('id', $author_ids)->get();

	}

	/**
	 * List of Tags
	 *
	 * @return void
	 **/
	public function getTags()
	{
		return \Tag\Lib\Helper::getObjectTags('blog');
	}

	
}