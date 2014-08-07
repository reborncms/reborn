<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

use Reborn\Auth\Sentry\Eloquent\User;

class DataProvider 
{

	public static function allPublicPosts($limit = 10, $offset = 0)
	{

		$blog = Blog::active()
		                ->notOtherLang()
		                ->with(array('category','author'))
		                ->orderBy('created_at', 'desc');

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $blog->get();

	}

	public static function countBy($conditions = array())
	{
		$blog = Blog::active()
					->notOtherLang();

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

	public static function getPostsBy($conditions = array(), $limit = 10, $offset = 0)
	{

		$blog = Blog::active()
					->notOtherLang()
					->with(array('category','author'));

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
						$blog->where(\DB::raw('UNIX_TIMESTAMP(created_at)'), '>=', $value);
						break;

					case 'until':
						$blog->where(\DB::raw('UNIX_TIMESTAMP(created_at)'), '<=', $value);
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

		return $blog->get();

	}

	/**
	 * Get single blog post
	 * 
	 **/
	public static function post($id)
	{

		return Blog::active()
                        ->with(array('category', 'author'))
                        ->where('id', $id)
                        ->first();

	}

	/**
	 * Get Categories
	 *
	 * @return void
	 * @author 
	 **/
	public static function getCategories()
	{
		return BlogCategory::all();	
	}

	/**
	 * List of Authors
	 *
	 * @return void
	 * @author 
	 **/
	public static function getAuthors()
	{
		$author_ids = array_values(array_unique(Blog::lists('author_id')));

		return User::whereIn('id', $author_ids)->get();

	}

	/**
	 * List of Tags
	 *
	 * @return void
	 **/
	public static function getTags()
	{
		return \Tag\Lib\Helper::getObjectTags('blog');
	}


	
}