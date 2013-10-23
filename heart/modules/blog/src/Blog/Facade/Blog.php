<?php

namespace Blog\Facade;

use Blog\Model\Blog as Model;
use Blog\Model\BlogCategory as CategoryModel;

/**
 * Blog Facade Class for Theme Devloper
 *
 * @package Blog Module
 */
class Blog
{

	/**
	 * Blog Model Cache
	 *
	 * @var array
	 **/
	protected static $cache;

	/**
	 * Get the posts form cache model
	 *
	 * @param string $name Model cache name
	 * @param array $options Options to fetch blog posts
	 * @return Illuminate\Database\Eloquent\Collection
	 **/
	public static function magicPosts($name = 'default', $options = array(), $skip = null, $limit = null)
	{
		if(!isset(static::$cache[$name])) {
			$posts = static::posts($options);
			static::$cache[$name]['model'] = $posts;
			static::$cache[$name]['options'] = $options;
		} else {
			$diff = array_diff($options, static::$cache[$name]['options']);

			if (! empty($diff)) {
				$posts = static::posts($options);
				static::$cache[$name]['model'] = $posts;
				static::$cache[$name]['options'] = $options;
			}
		}

		$slice = static::$cache[$name]['model'];

		if (isset($skip)) {
			$slice = $slice->slice($skip);
		}

		if(isset($limit)) {
			$slice = $slice->take($limit);
		}

		return $slice;
	}

	/**
	 * Get Blog Posts.
	 *
	 *
	 * @param array $options Options to fetch blog posts
	 * @return Illuminate\Database\Eloquent\Collection
	 **/
	public static function posts($options = array())
	{
		$cats = isset($options['category']) ? explode(',', $options['category']) : null;
		$limit = isset($options['limit']) ? (int)$options['limit'] : 5;
		$offset = isset($options['offset']) ? (int)$options['offset'] : null;
		$order = isset($options['order']) ? $options['order'] : 'created_at';
		$dir = isset($options['order_dir']) ? $options['order_dir'] : 'desc';
		$author = isset($options['author']) ? $options['author'] : false;

		$posts = Model::active();
		if (!is_null($cats)) {
			$cat_ids = CategoryModel::whereIn('slug', $cats)->lists('id');
			$posts->whereIn('category_id', $cat_ids);
		}

		if ($author) {
			$aq = explode(',', $author);
			$posts->whereIn('author_id', $aq);
		}

		$posts = $posts->orderBy($order, $dir)->take($limit)->skip($offset)->get();

		return $posts;
	}
}
