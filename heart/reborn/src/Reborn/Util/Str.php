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

} // END class Str
