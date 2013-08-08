<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

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
}