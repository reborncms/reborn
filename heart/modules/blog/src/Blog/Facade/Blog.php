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
	 * Get Blog Posts.
	 *
	 *
	 * @param array $options Options to fetch blog posts
	 * @return void
	 * @author
	 **/
	public static function posts($options = array())
	{
		$cats = isset($options['category']) ? explode(',', $options['category']) : null;
		$limit = isset($options['limit']) ? (int)$options['limit'] : 5;
		$offset = isset($options['offset']) ? (int)$options['offset'] : null;
		$order = isset($options['order']) ? $options['order'] : 'created_at';
		$dir = isset($options['order_dir']) ? $options['order_dir'] : 'desc';
		$template = isset($options['template']) ? $options['template'] : 'default';

		$posts = Model::active();
		if (!is_null($cats)) {
			$cat_ids = CategoryModel::whereIn('slug', $cats)->lists('id');
			$posts->whereIn('category_id', $cat_ids);
		}

		$posts = $posts->orderBy($order, $dir)->take($limit)->skip($offset)->get();

		return $posts;
	}
}
