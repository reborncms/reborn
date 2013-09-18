<?php

namespace Reborn\Util;

/**
 * String Helper Class for Reborn
 *
 * @package Reborn\Util
 * @author Myanmar Links Professional Web Development Team
 **/
class Str
{
	/**
	 * Replacer value for camel and snake
	 */
	protected static $replacer = array('\\', '/', '=', '.', '-', '_', '?');

	/**
	 * Convert the string to camelCase
	 *
	 * @param string $str
	 * @return string
	 **/
	public static function camel($str)
	{
		$str = str_replace(static::$replacer, ' ', $str);
		$strArr = explode(' ', $str);

		if (count($strArr) == 1) {
			$res = $strArr[0];
		} else {
			$first = array_shift($strArr);
			$upper = str_replace(' ', '', ucwords(implode(' ', $strArr)));
			$res = $first.$upper;
		}

		return lcfirst($res);
	}

	/**
	 * Convert the string to snakeCase
	 *
	 * @param string $str
	 * @param string $delimiter Delimiter for the snake. Defaut is '_'
	 * @return string
	 **/
	public static function snake($str, $delimiter = '_')
	{
		$str = str_replace(static::$replacer, '', $str);

		preg_match_all('/[A-Z]/', $str, $matches);

		foreach ($matches[0] as $m) {
			$str = str_replace($m, $delimiter.strtolower($m), $str);
		}

		return ltrim($str, $delimiter);
	}

	/**
	 * Convert the given string to slug(URL) type string
	 *
	 * @param string $str
	 * @param string $separator Separator for space. Defaut is '-'
	 * @return string
	 **/
	public static function slug($str, $separator = '-')
	{
		if ($str == '') return '';
		$str = sanitize($str, 'A-Za-z0-9-\s');
		return strtolower(str_replace(' ', $separator, $str));
	}

	/**
	 * Sanitize the given string by given pattern
	 * example :
	 * <code>
	 * 		// Output is "Who are you"
	 * 		sanitize('Who are you?', 'A-Za-z-0-9-\s');
	 * </code>
	 *
	 * @param string $str String
	 * @param string $pattern Regular Expression Pattern
	 * @return string
	 **/
	public static function sanitize($str, $pattern)
	{
		if ($str == '') return '';
		return preg_replace('#[^'.$pattern.']#', '', $str);
	}

	/**
	 * Generate the random string.
	 *
	 * @param integer $length Length of random string
	 * @return string
	 **/
	public static function random($length = 10)
	{
		$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		$pool_length = (strlen($pool) - 1);
		$random = '';
		for ($i=0; $i < $length; $i++) {
			$random .= $pool[rand(0, $pool_length)];
		}

		return $random;
	}

	/**
	 * Get word with limit
	 *
	 * @param string $string Given text
	 * @param int $limit Word limit
	 * @param string $ending Word limit ending eg:(...)
	 * @return string
	 **/
	public static function words($string, $limit = 100, $ending = '...')
	{
		$words = array();
		$words = explode(" ", $string);

		if(count($words) < $limit){
		   return $string;
		}

		$words = array_slice($words, 0, $limit);

		return implode(' ', $words).$ending;
	}

	/**
	 * Get word with text limit
	 *
	 * @param string $string Given text
	 * @param int $limit Text limit
	 * @param string $ending Word limit ending eg:(...)
	 * @return string
	 **/
	public static function limit($string, $limit = 100, $ending = '...')
	{
		$shown_string = implode(" ", $words);
		$string = strip_tags($string);
		if (strlen($string) > $limit) {

		    // truncate string
		    $string_cut = substr($string, 0, $limit);

		    $string = substr($string_cut, 0, strrpos($string_cut, ' ')).$ending;
		}

		return $string;
	}

	/**
	 * Convert given string to Title String Style.
	 *
	 * example ::
	 *  // $str = 'hello_world';
	 *  Str::title($str); // output : Hello World
	 *
	 * If you want to stay separator word form your string, use $remove_separator = false
	 *  // $str = 'hello_world';
	 *  Str::title($str); // output : Hello_World
	 *
	 * @param string $value
	 * @param boolean $remove_separator Remove separator (_ or -) form str. Default is true.
	 * @param string $separator This is require in $remove_separator=false only. Default _
	 * @return string
	 **/
	public static function title($value, $remove_separator = true, $separator = '_')
	{
		$val = ucwords(str_replace(array('_', '-'), ' ', $value));

		if (! $remove_separator) {
			return str_replace(' ', $separator, $val);
		}

		return $val;
	}

} // END class Str
