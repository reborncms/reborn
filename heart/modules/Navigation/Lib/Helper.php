<?php

namespace Navigation\Lib;

use Navigation\Model\Navigation;
use Navigation\Model\NavigationLinks as Links;

class Helper
{
	public static function render($nav = 'header', $tag = 'ul', $active = 'active')
	{
		$nav = Navigation::where('slug', '=', $nav)->first();
		$obj = Links::where('navigation_id', '=', $nav->id)
						->orderBy('link_order', 'asc')
						->get()->toArray();
		$tree = static::getNavTree($obj);

		$current = rtrim(\Registry::get('app')->request->requestUrl(), '/');

		$homepage = \Setting::get('home_page');

		$o = "<$tag>\n";
			foreach ($tree as $t) {

				if ('url' == $t['link_type']) {
					$url = $t['url'];
				} else {
					$url = rbUrl($t['url']);
				}

				if ($current == rtrim($url,'/')) {
					$activeClass = $active;
				} elseif (rtrim(rbUrl($homepage), '/') == rtrim($url,'/')) {
					if (rtrim(rbUrl(), '/') == $current) {
						$activeClass = $active;
					} else {
						$activeClass = '';
					}
				} else {
					$activeClass = '';
				}

				$id = slug($t['title']);
				$o .= "\t".'<li id="'.$id.'" class="'.$activeClass.'">';
				$o .= '<a href="'.$url.'" class="'.$t['class'].'" >';
				$o .= $t['title'];
				$o .= '</a>';
				if ($t['child']) {
					$o .= static::renderChild($tag, $t);
				}
				$o .= "\t</li>\n";
			}
		$o .= "</$tag>";

		return $o;
	}

	protected static function renderChild($tag, $link, $level = 1)
	{
		$output = '';
		if ($link['child']):
			$tab = str_repeat("\t", $level);
			$inner = $tab."\t";
			$output .= "\n$tab<$tag class=\"level-$level\">\n";
			foreach($link['child'] as $link) :

					$output .= $inner.'<li id="'. $link['title'].'">';
					$output .= '<a href="'.$link['url'].'" class="'.$link['class'].'" >';
					$output .= $link['title'];
					$output .= '</a>';

				if (! empty($link['child'])) :
						$output .=	static::renderChild($tag, $link, $level +1);
					$output .= $inner."</li>\n";
				else :
					$output .= "</li>\n";
				endif;

			endforeach;

			$output .= "$tab</$tag>\n";

		endif;

		return $output;
	}

	public static function pageSelect()
	{
		if( \Module::isDisabled('Pages')) {
			return array();
		}

		\Module::load('Pages');
		return \Pages\Lib\Helper::pageList();
	}

	public static function moduleSelect()
	{
		$modules = \Module::getAll();
		$select = array();
		foreach($modules as $name => $m) {
			if(\Module::isEnabled($name) and ($m['frontendSupport'])) {
				$select[strtolower($name)] = $name;
			}
		}

		return $select;
	}

	/**
	 * Get nav tree array
	 */
	public static function getNavTree(&$categories)
	{
		 $map = array(
			0 => array('child' => array())
		);

		foreach ($categories as &$category) {
			$category['child'] = array();
			$map[$category['id']] = &$category;
		}

		foreach ($categories as &$category) {
			$map[$category['parent_id']]['child'][] = &$category;
		}

		return $map[0]['child'];
	}

	/**
	 * Build the html for the admin link tree view
	 *
	 * @param array $link Current navigation link
	 */
	public static function tree_builder($link)
	{
		$output = '';
		if ($link['child']):

			foreach($link['child'] as $link) :

					$output .= '<li id="link_'. $link['id'].'">';
					$output .=	'<div class="draggable_wrap">';

					$output .=	'<div class="nav_title">'. $link['title'].'</div>';

					$output .=	'<div class="nav_actions">';

					if(user_has_access('nav.edit')) {
						$output .= '<a href="'.adminUrl('navigation/edit/'.$link['id']);
						$output .= '" title="'.t('global.edit').'" class="link-edit tipsy-tip">';
						$output .= '<i class="icon-edit icon-black"></i>';
						$output .= '</a>';
					}

					if(user_has_access('nav.delete')) {
						$output .= '<a href="'.adminUrl('navigation/delete/'.$link['id']);
						$output .= '" title="'.t('global.delete').'" class="confirm_delete tipsy-tip">';
						$output .= '<i class="icon-trash2 icon-black"></i>';
						$output .= '</a>';
					}

					$output .= '</div></div> <!-- end of draggable_wrap -->';

				if (! empty($link['child'])) :
						$output .= '<ol>';
						$output .=	static::tree_builder($link);
						$output .= '</ol>';
					$output .= '</li>';
				else :
					$output .= '</li>';
				endif;

			endforeach;

		endif;

		return $output;
	}
}
