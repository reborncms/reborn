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

	/**
	 * Single(< />) tag lists
	 *
	 * @var array
	 **/
	protected static $singleTags = array('input', 'hr', 'br', 'img', 'base', 'link', 'meta');

	/**
	 * HTML Anchor Tag. (<a>Title</a>)
	 *
	 * @param string $url
	 * @param string $text a tag's text (<a>$text</a>)
	 * @param array $options
	 * @return string
	 **/
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

	/**
	 * HTML Anchor Tag for mailto. (<a href="mailto:">Title</a>)
	 *
	 * @param string $address
	 * @param string $text a tag's text (<a>$text</a>)
	 * @param array $options
	 * @return string
	 **/
	public static function mailto($address, $text = null, $options = array())
	{
		$options['href'] = 'mailto:'.$address;

		if (is_null($text)) {
			$text = $address;
		}

		return static::tag('a', $text, $options);
	}

	/**
	 * HTML Unorder List Tag. (<ul>)
	 *
	 * @param array $lists_array
	 * @param array $options
	 * @return string
	 **/
	public static function ul($lists_array, $options = array())
	{
		$lists = static::li($lists_array);

		return static::tag('ul', "\n".$lists."\n", $options);
	}

	/**
	 * HTML Order List Tag. (<ol>)
	 *
	 * @param array $lists_array
	 * @param array $options
	 * @return string
	 **/
	public static function ol($lists_array, $options = array())
	{
		$lists = static::li($lists_array);

		return static::tag('ol', "\n".$lists."\n", $options);
	}

	/**
	 * HTML List Tag. (<li>)
	 *
	 * @param array $lists_array
	 * @param array $options
	 * @return string
	 **/
	public static function li($list_array, $options = array())
	{
		$list = '';

		foreach ($list_array as $li) {
			$list .= static::tag('li', "\n".$li."\n", $options);
		}
		return $list;
	}

	/**
	 * HTML base Tag. (<base href="#">)
	 *
	 * @param string $href
	 * @return string
	 **/
	public static function base($href)
	{
		return static::tag('base', '', array('href' => $href) );
	}

	/**
	 * HTML Header Tag. (<ul>)
	 *
	 * @param string $head (h1, h2, h3, h4, h5 or h6)
	 * @param string $content
	 * @param array $options
	 * @return string
	 **/
	public static function header($head = 'h1', $content, $options = array())
	{
		return static::tag($head, $content, $options);
	}

	/**
	 * HTML br Tag. (<br />)
	 *
	 * @param int $repeat
	 * @param array $options
	 * @return string
	 **/
	public static function br($repeat = 1, $options = array())
	{
		return str_repeat(static::tag('br', '', $options), $repeat);
	}

	/**
	 * HTML nbsp Tag. ($nbsp;)
	 *
	 * @param int $repeat
	 * @return string
	 **/
	public static function nbsp($repeat = 1)
	{
		return str_repeat('&nbsp;', $repeat);
	}

	/**
	 * HTML Tag generate.
	 *
	 * @param string $tag_name
	 * @param string $content
	 * @param array $options
	 * @return string
	 **/
	public static function tag($tag_name, $content = '', $options = array())
	{
		$tag = '<'.$tag_name.' '.self::buildAttrs($options);

		if (in_array(strtolower($tag_name), static::$singleTags)) {
			return $tag .= '/>';
		} else {
			return $tag .= '>'.$content.'</'.$tag_name.'>';
		}
	}

	/**
	 * Build tag attribute string from given array
	 *
	 * @param array $options
	 * @return string
	 **/
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
