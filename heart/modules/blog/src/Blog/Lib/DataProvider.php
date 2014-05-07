<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

class DataProvider 
{

	public static function allPublicPosts($limit = 10, $offset = 0)
	{

		$blog = Blog::active()
		                ->notOtherLang()
		                ->with(array('category','author'))
		                ->orderBy('created_at', 'desc');

		if ($limit != 0) {

			$blog->take($limit);

		}

		if ($offset != 0) {

			$blog->skip($offset);

		}

		return $blog->get();

	}

	public static function getPostsBy($wheres = array(), $limit = 10, $offset = 0)
	{

		$blog = Blog::active()
					->notOtherLang()
					->with(array('category','author'));

		foreach ($wheres as $key => $value) {
			if ($key == 'tag') {

				$blog_ids = \Tag\Lib\Helper::getObjectIds($value, 'blog');
				$blog->whereIn('id', $blog_ids);

			} else {

				$blog->where($key, $value);

			}
		}

		if ($limit != 0) {

			$blog->take($limit);

		}

		if ($offset != 0) {

			$blog->skip($offset);

		}

		return $blog->get();

	}

	public static function post($id)
	{

		return Blog::active()
                        ->with(array('category', 'author'))
                        ->where('id', $id)
                        ->first();

	}
	
}