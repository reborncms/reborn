<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

class Helper {

	protected static $level = 1;

	protected static $cat_list;

	protected static $inc_count = 0;

	public  static function catStructure($category)
	{
		$bcs = '';
		$bcs .= '<div class="draggable_wrap">';
		$bcs .= 	$category['name'];

		$bcs .= '<div class="actions">';
		$bcs .= '<a href="'.\Uri::create(ADMIN_URL.'/blog/category/edit/'.$category['id']).'" title="'. t('global.edit') .'" class="tipsy-tip c-edit-box"><i class="icon-edit icon-black"></i></a>';
		if ($category['id'] != 1) {
			$bcs .= '<a href="'.\Uri::create(ADMIN_URL.'/blog/category/delete/'.$category['id']).'" title="'. t('global.delete') .'" class="confirm_delete tipsy-tip"><i class="icon-remove icon-black"></i></a>';
		}
		$bcs .= '</div>';
		$bcs .= '</div>';
		return $bcs;
	}

	public static function changeAuthor($author_id){
		$blogs = Blog::where('author_id', $author_id)->update(array('author_id' => 1));
		if ($blogs) {
			return true;
		} else {
			return false;
		}
	}

	public static function generateChildren($children)
	{
		$gc = '';
		$gc .= '<ol>';
		foreach ($children as $category) {
			$gc .= '<li id="cat_'.$category['id'].'">';
			$gc .= self::catStructure($category);
			if (isset($category['children'])) {
				$gc .= self::generateChildren($category['children']);
			}
			$gc .= '</li>';
		}
		$gc .= '</ol>';

		return $gc;
	}

	public static function catList($from = null, $currentCat = null)
	{
		if ($from != null) {
			static::$cat_list[0] = '-- none --';
		}
		$categories = BlogCategory::cat_stucture();
		foreach ($categories as $category) {
			static::$cat_list[$category['id']] = $category['name'];
			if(isset($category['children'])) {
				static::$level = 1;
				self::childCats($category['children'], 1);
			}
		}
		if ($currentCat) {
			unset(static::$cat_list[$currentCat]);
		}
		return static::$cat_list;
	}

	protected static function childCats($children, $lvl = 0)
	{
		foreach ($children as $category) {
			static::$cat_list[$category['id']] = str_repeat('>',$lvl).' '.$category['name'];
			if(isset($category['children'])) {
				static::$level++;
				static::$inc_count++;
				self::childCats($category['children'], static::$level);
			} else {
				static::$level = static::$level - static::$inc_count;
				static::$inc_count = 0;
			}
		}
	}

	public static function dashboardWidget()
	{
		$widget = array();
		$widget['title'] = 'Latest Blog Posts';
		$widget['icon'] = 'icon-archive';
		$widget['id'] = 'blog';
		$widget['body'] = '';
		$posts = Blog::with('author')->take(5)->orderBy('created_at', 'desc')->get();
		$widget['body'] .= '<ul>';
		if (count($posts) > 0) {
			foreach ($posts as $post) {
				$widget['body'] .= '<li>
										<span class="date">'.$post->post_date('d M Y').'</span>
										<span class="blog-author"><i class="icon-user icon-white"></i>
											<a href="'.rbUrl('user/profile/'.$post->author->id).'">'.$post->author_name.'</a>
										</span>
										<a href="'.rbUrl('blog/'.$post->slug).'" target="_black">'.$post->title.'</a>
									</li>';
			}
		} else {
			$widget['body'] .= '<li><span class="empty-list">'. t('label.last_post_empty') .'</span></li>';
		}
		$widget['body'] .= '</ul>';

		return $widget;
	}
}
