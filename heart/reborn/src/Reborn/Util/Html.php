<?php

namespace Reborn\Util;

/**
 * Html Tag Helper class
 *
 * @package Reborn\Util
 * @author Myanmar Links Web Development Team
 **/
class Html
{

	protected static $singleTags = array('input', 'hr', 'img', 'base', 'link', 'meta');

	public static function a($url, $text = null, $options = array())
	{
		if ('@admin' == substr($url, 0, 6)) {
			$url = adminUrl(ltrim(substr($url, 6), '/'));
		} elseif (false == strpos($url, 'http')) {
			$url = rbUrl($url);
		}

		$options['href'] = $url;

		if (is_null($text)) {
			$text = $url;
		}

		return static::tag('a', $text, $options);
	}

	public static function ul($lists_array, $options = array())
	{
		$lists = static::li($lists_array);

		return static::tag('ul', "\n".$lists."\n", $options);
	}

	public static function ol($lists_array, $options = array())
	{
		$lists = static::li($lists_array);

		return static::tag('ol', "\n".$lists."\n", $options);
	}

	public static function li($list_array, $options = array())
	{
		$list = '';

		foreach ($list_array as $li) {
			$list .= static::tag('li', "\n".$li."\n", $options);
		}
		return $list;
	}

	public static function base($href)
	{
		return static::tag('base', '', array('href' => $href) );
	}

	public static function header($head = 'h1', $content, $options = array())
	{
		return static::tag($head, $content, $options);
	}

	public static function tag($tag_name, $content = '', $options = array())
	{
		$tag = '<'.$tag_name.' '.self::buildAttrs($options);

		if (in_array(strtolower($tag_name), static::$singleTags)) {
			return $tag .= '/>';
		} else {
			return $tag .= '>'.$content.'</'.$tag_name.'>';
		}
	}

	protected static function buildAttrs($options = array())
	{
		$attrs = '';

		if (empty($options)) {
			return $attrs;
		}

		foreach ($options as $key => $value) {
			$attrs .= $key.'="'.$value.'" ';
		}

		return $attrs;
	}

} // END class Html
