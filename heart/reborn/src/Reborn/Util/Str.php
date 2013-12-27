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
	 * Convert the given string to Studly Case string.
	 * <code>
	 * 		$str = 'hello_world';
	 * 		echo Str::studly($str); // HelloWorld
	 * </code>
	 *
	 * @param string $string
	 * @return string
	 **/
	public static function studly($string)
	{
		$string = ucwords(str_replace(array('_', '-', '.'), ' ', $string));

		return str_replace(' ', '', $string);
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
	 * <code>
	 *  // $str = 'hello_world';
	 *  Str::title($str); // output : Hello World
	 *
	 *  //If you want to stay separator word form your string, use $remove_separator = false
	 *  // $str = 'hello_world';
	 *  Str::title($str, false); // output : Hello_World
	 *  Str::title($str, false, '-'); // output : Hello-World
	 * </code>
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

	/**
	 * Increment +1 number to given string
	 *
	 * @param string $str
	 * @return string
	 **/
	public static function increment($str)
	{
		preg_match('#(.*)_(\d+)#', $str, $m);

		if ($m) {
			$next = 1 + (int) $m[2];
			return $m[1].'_'.$next;
		}

		return $str.'_1';
	}

	/**
	 * Join the string.
	 *
	 * @param string $separator String join separator
	 * @return string
	 **/
	public static function join($separator)
	{
		$args = func_get_args();

		$separator = array_shift($args);

		$str = '';

		if (empty($args)) return $str;

		foreach ($args as $s) {
			$str .= $s.$separator;
		}

		return rtrim($str, $separator);
	}

	/**
	 * Highlight span class wrapping for $term from $text
	 *
	 * @param string $text
	 * @param string $term Highlight term
	 * @param string $class Class name for span tag. Default is 'highlight'
	 * @param string $flag Flag for regular expression
	 * @return string
	 **/
	public static function highlight($text, $term, $class = 'highlight', $flag = 'i')
	{
		// $term is empty or $text is empty
		if (empty($term) || '' == $text) {
			return '';
		}

		$term = '#(' . preg_quote($term, '|') . ')#'.$flag;

		$format = '<span class="'.$class.'">$0</span>';

		return preg_replace($term, $format, $text);
	}

	/**
	 * Check needle string is start from haystack
	 *
	 * @param string $needle
	 * @param string $haystack
	 * @param boolean $ignorecase Ignore Case Sensitive. Default is true
	 * @return boolean
	 **/
	public static function startWith($needle, $haystack, $ignorecase = true)
	{
		$needle_len = strlen($needle);

		if ($ignorecase) {
			list($needle, $haystack) = static::lowercase($needle, $haystack);
		}

		$sub = substr($haystack, 0, $needle_len);

		return ($needle_len <= strlen($haystack) && $sub === $needle);
	}

	/**
	 * Check needle string is ending from haystack
	 *
	 * @param string $needle
	 * @param string $haystack
	 * @param boolean $ignorecase Ignore Case Sensitive. Default is true
	 * @return boolean
	 **/
	public static function endWith($needle, $haystack, $ignorecase = true)
	{
		$needle_len = strlen($needle);

		if ($ignorecase) {
			list($needle, $haystack) = static::lowercase($needle, $haystack);
		}

		$sub = substr($haystack, - $needle_len);

		return ($needle_len <= strlen($haystack) && $sub === $needle);
	}

	/**
	 * This string is ending with end word.
	 *
	 * @param string $string
	 * @param string $end Ending word
	 * @return string
	 **/
	public static function endIs($string, $end)
	{
		return rtrim($string, $end).$end;
	}

	/**
	 * Check given string is blank or not
	 * <code>
	 * 		Str::isBlank(''); // true
	 * 		Str::isBlank(null); // true
	 * 		Str::isBlank(' '); // true
	 * 		Str::isBlank("\n"); // true
	 * 		Str::isBlank('1'); // false
	 * </code>
	 *
	 * @param string|null $str
	 * @return boolean
	 **/
	public static function isBlank($str = null)
	{
		if (is_null($str)) $str = '';

		return preg_match('/^\s*$/', $str);
	}

	/**
	 * Explode string by new line (\n)
	 * <code>
	 * 		$str = "Hello\nWorld";
	 * 		dump(Str::lines($str));
	 * 		// Output : array('Hello', 'World');
	 * </code>
	 *
	 * @param string $str
	 * @return array
	 **/
	public static function lines($str)
	{
		return explode("\n", $str);
	}

	/**
	 * Check needle string contain in haystack
	 *
	 * @param string $needle
	 * @param string $haystack
	 * @param boolean $ignorecase Ignore Case Sensitive. Default is true
	 * @return boolean
	 **/
	public static function contain($needle, $haystack, $ignorecase = true)
	{
		if ($ignorecase) {
			list($needle, $haystack) = static::lowercase($needle, $haystack);
		}

		return (false !== strpos($haystack, $needle));
	}

	/**
	 * Check atleast one of list from give array lists
	 * contain in the haystack string
	 * example ::
	 * <code>
	 * // return true
	 * Str::containIn(array('list', 'array'), 'list must be array');
	 * // return true. Because "list" is contain in given string
	 * Str::containIn(array('list', 'object'), 'list must be array');
	 * // return false
	 * Str::containIn(array('list', 'object'), 'lists must be array');
	 * </code>
	 *
	 * @param array $lists
	 * @param string $haystack
	 * @param boolean $ignorecase Ignore Case Sensitive. Default is true
	 * @return boolean
	 **/
	public static function containIn($lists, $haystack, $ignorecase = true)
	{
		$has_false = 0;

		foreach ($lists as $list) {
			if (! static::contain($list, $haystack, $ignorecase)) {
				$has_false++;
			}
		}

		return (count($lists) > $has_false);
	}

	/**
	 * Check must be given all array lists
	 * contain in the haystack string
	 *
	 * @param array $lists
	 * @param string $haystack
	 * @param boolean $ignorecase Ignore Case Sensitive. Default is true
	 * @return boolean
	 **/
	public static function containAll($lists, $haystack, $ignorecase = true)
	{
		$contain = true;

		foreach ($lists as $list) {
			if (! static::contain($list, $haystack, $ignorecase)) {
				$contain = false;
			}
		}

		return $contain;
	}

	/**
	 * Check given string's length must be between min and max.
	 *
	 * @param string $string
	 * @param integer $min Min length
	 * @param integer $max Max length
	 * @return boolean
	 **/
	public static function lengthBetween($string, $min, $max)
	{
		$length = strlen($string);

		return (($length > $min) && ($length < $max));
	}

	/**
	 * Convert given string to lowercase
	 *
	 * @param string
	 * @return array
	 **/
	public static function lowercase()
	{
		$strs = func_get_args();
		$result = array();

		foreach ($strs as $s) {
			$result[] = strtolower($s);
		}

		return $result;
	}

} // END class Str
