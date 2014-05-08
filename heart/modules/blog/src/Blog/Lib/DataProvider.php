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

		if ($limit > 0) {

			$blog->take($limit);

		}

		if ($offset > 0) {

			$blog->skip($offset);

		}

		return $blog->get();

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