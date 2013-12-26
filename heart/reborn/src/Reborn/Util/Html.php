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
	 * Html style link tag
	 *
	 * @param string $url
	 * @param string $media
	 * @return string
	 **/
	public static function style($url, $media = 'all')
	{
		$attrs = array(
					'href'	=> $url,
					'media'	=> $media,
					'rel'	=> 'stylesheet',
					'type'	=> 'text/css'
				);

		return static::tag('link', null, $attrs);
	}

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
		} elseif (false != strpos($url, 'http')) {
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
	 * Html img tag
	 *
	 * @param string $src Image source file
	 * @param string|null $alt Alternative text
	 * @param array $attrs Img tag attribtues
	 * @return string
	 **/
	public static function img($src, $alt = null, $attrs = array(), $reborn = false)
	{
		if (is_array($alt)) {
			if (is_bool($attrs)) {
				$reborn = $attrs;
			}
			$attrs = $alt;
			$alt = null;
		}

		if(is_bool($alt)) {
			$reborn = $alt;
			$alt = null;
		}

		$attrs['src'] = $reborn ? rbUrl('media/image/'.$src) : $src;

		if(!is_null($alt)) {
			$attrs['alt'] = $alt;
		}

		return static::tag('img', null, $attrs);
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
		if (empty($lists_array)) return null;

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
		if (empty($lists_array)) return null;

		$lists = static::li($lists_array, array(), 'ol');

		return static::tag('ol', "\n".$lists."\n", $options);
	}

	/**
	 * HTML List Tag. (<li>)
	 *
	 * @param array $lists_array
	 * @param array $options
	 * @param string $type List type (ul or ol)
	 * @return string
	 **/
	public static function li($list_array, $options = array(), $type = 'ul')
	{
		$list = '';

		foreach ($list_array as $li) {
			if (is_array($li)) {
				// Fixed for PHP5.3.*
				if ('ol' == $type) {
					$content = static::ol($li);
				} else {
					$content = static::ul($li);
				}
				$list .= '<li>'.$content.'</li>';
			} else {
				$list .= static::tag('li', "\n".$li."\n", $options);
			}
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
		return static::tag('base', null, array('href' => $href) );
	}

	/**
	 * HTML meta Tag. (<meta name="keywords" content="Reborncms, Myanmar, CMS" >)
	 *
	 * @param string $name Meta tag name
	 * @param string $content Meta tag content
	 * @return string
	 **/
	public static function meta($name, $content)
	{
		return static::tag('meta', null, array('name' => $name, 'content' => $content));
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
		return str_repeat(static::tag('br', null, $options), $repeat);
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
	 * @param string|null $content
	 * @param array $options
	 * @return string
	 **/
	public static function tag($tag_name, $content = null, $options = array())
	{
		$tag = '<'.$tag_name.' '.self::buildAttributes($options);

		if (in_array(strtolower($tag_name), static::$singleTags)
			|| is_null($content)
			) {
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
	public static function buildAttributes(array $options = array())
	{
		$attrs = '';

		foreach ($options as $key => $value) {
			if (is_string($key)) {
				$attrs .= $key.'="'.$value . '" ';
			} else {
				$attrs .= $value . ' ';
			}
		}

		return $attrs;
	}

} // END class Html
