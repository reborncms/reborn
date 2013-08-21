<?php

/**
 * Helper Functions for Reborn
 *
 * @package Reborn
 * @author Myanmar Links Professional Web Development Team
 **/

/**
 * Dump the given value. Use for var_dump().
 * If you want to die the script after dump, use as
 * <code>
 * 		dump($value, true);
 * </code>
 *
 * @param mixed $value
 * @param bool $die
 *
 */
if(! function_exists('dump'))
{
	function dump($value, $die = false)
	{
		$backtrace = debug_backtrace();
		$file = $backtrace[0]['file'];
		$line = $backtrace[0]['line'];

		echo '<pre style="border: 1px dashed #71CF4D; padding: 5px 10px; background: #F2F2F2; margin: 10px 15px;">';
		echo '<h3 style="color: #990000; margin-top: 0px; padding-bottom: 5px; border-bottom: 1px dashed #999; font-size: 13px; font-weight: normal;">';
		echo 'Dump at <span style="color: #000099;">'.$file.'</span> ';
		echo 'line number <span style="color: #009900;">'.$line.'</span>.</h3>';
		var_dump($value);
		echo '</pre>';
		if ($die) {
			die;
		}
	}
}

/**
 * Filter function for htmlspecialchars.
 * See detail at php's htmlspecialchars function.
 *
 * @param string $str
 * @param int $flags
 * @param string $encode
 * @param boolean $doubleEncode
 */
if(! function_exists('h'))
{
	function h($str, $flags = ENT_QUOTES, $encode = 'UTF-8', $doubleEncode = true)
	{
		return htmlspecialchars($str, $flags, $encode, $doubleEncode);
	}
}

/**
 * Helper function for the translate.
 * See detail at Translate::get()
 *
 * @param string $key
 * @param array $replace Replace value for langauge string
 * @param string $default
 * @return void
 **/
if(! function_exists('t'))
{
	function t($key, $replace = null, $default = null)
	{
		return \Translate::get($key, $replace, $default);
	}
}

/**
 * Get the flush session view (with html).
 * This function is same with Flash::flahsBox();
 *
 *
 * @return string
 */
if(! function_exists('flash'))
{
	function flash()
	{
		return \Flash::flashBox();
	}
}

/**
 * Return the value of given param,
 * If the value is closure, return closure result.
 *
 * @param mixed $val
 * @return mixed
 */
if(! function_exists('value'))
{
	function value($val)
	{
		return ($val instanceof \Closure) ? $val() : $val;
	}
}

/**
 * Convert the given string to slug(URL) type string
 *
 * @param string $str
 * @param string $separator Separator for space. Defaut is '-'
 * @return string
 **/
if(! function_exists('slug'))
{
	function slug($str, $separator = '-')
	{
		return \Reborn\Util\Str::slug($str, $separator);
	}
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
if(! function_exists('sanitize'))
{
	function sanitize($str, $pattern)
	{
		return \Reborn\Util\Str::sanitize($str, $pattern);
	}
}

/**
 * Generate the random string.
 *
 * @param integer $length Length of random string
 * @return string
 **/
if(! function_exists('randomStr'))
{
	function randomStr($length = 10)
	{
		return \Reborn\Util\Str::random($length);
	}
}

/**
 * Pluck the value from given array
 *
 * @param array $arr
 * @param string $key
 * @return array
 **/
if(! function_exists('array_pluck'))
{
	function array_pluck($arr, $key)
	{
		return array_map(function($a) use($key) { return $a[$key]; }, $arr);
	}
}


/**
 * Get an item from an array using "dot" notation.
 * This function is original from Illuminate\Supprot\src\helpers.php file.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
if(! function_exists('array_get'))
{
	function array_get($array, $key, $default = null)
	{
		if (is_null($key)) return $array;

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) or ! array_key_exists($segment, $array))
			{
				return value($default);
			}

			$array = $array[$segment];
		}

		return $array;
	}
}

/**
 * Check the given array is multidimensional array or not.
 *
 * @param  array   $array
 * @return boolean
 */
if(! function_exists('arrIsMulti'))
{
	function arrIsMulti($array)
	{
	    return count(array_filter($array, 'is_array')) > 0;
	}
}

/**
 * Convert given array to object.
 *
 * @param array $array
 * @return object
 */
if(! function_exists('arrToObject'))
{
	function arrToObject($array)
	{
	    return json_decode(json_encode((array) $array));
	}
}

/**
 * Helper function for the Uri::create().
 *
 * @param string $path Uri path to create.
 * @return string
 **/
if(! function_exists('rbUrl'))
{
	function rbUrl($path = '')
	{
		return \Uri::create($path);
	}
}

/**
 * Helper function for the Uri::create() with ADMIN Panel Link.
 *
 * @param string $path Uri path to create.
 * @return string
 **/
if(! function_exists('adminUrl'))
{
	function adminUrl($path = '')
	{
		$admin = \Setting::get('adminpanel_url');
		$path = ltrim($path, '/');
		return \Uri::create($admin.'/'.$path);
	}
}

/**
 * Helper function for the Asset::css().
 *
 * @param string $file CSS filename with extension.
 * @param string $media Medaia type for CSS tag. Default is "all"
 * @param string $module If you want to use CSS from module, set module name
 * @return string
 **/
if(! function_exists('css'))
{
	function css($file, $media = "all", $module = null)
	{
		$theme = Registry::get('app')->view->getTheme();
		$asset = new Reborn\Asset\Asset($theme->getThemePath());
		return $asset->css($file, $media, $module);
	}
}

/**
 * Helper function for the Asset::less().
 *
 * @param string $file LESS filename with extension.
 * @param string $media Medaia type for CSS tag. Default is "all"
 * @param string $module If you want to use LESS from module, set module name
 * @return string
 **/
if(! function_exists('less'))
{
	function less($file, $media = "all", $module = null)
	{
		$theme = Registry::get('app')->view->getTheme();
		$asset = new Reborn\Asset\Asset($theme->getThemePath());
		return $asset->less($file, $media, $module);
	}
}

/**
 * Helper function for the Asset::js().
 *
 * @return string
 **/
if(! function_exists('js'))
{
	function js($file, $module = null)
	{
		$theme = Registry::get('app')->view->getTheme();
		$asset = new Reborn\Asset\Asset($theme->getThemePath());
		return $asset->js($file, $module);
	}
}

/**
 * Helper function for the Asset::img().
 *
 * @return string
 **/
if(! function_exists('img'))
{
	function img($file, $alt = null, $attr = array(), $module = null)
	{
		$theme = Registry::get('app')->view->getTheme();
		$asset = new Reborn\Asset\Asset($theme->getThemePath());
		return $asset->img($file, $alt, $attr, $module);
	}
}

/**
 * Helper function for the Asset File Path (css, js, img).
 *
 * @return string
 **/
if(! function_exists('assetPath'))
{
	function assetPath($type = null, $module = null)
	{
		$theme = Registry::get('app')->view->getTheme();
		$asset = new Reborn\Asset\Asset($theme->getThemePath());
		switch($type) {
			case 'css' :
				return $asset->getCssPath($module);
				break;
			case 'js' :
				return $asset->getJsPath($module);
				break;
			case 'img' :
			case 'image' :
				return $asset->getImgPath($module);
				break;
			default :
				return $asset->getAssetPath($module);
				break;
		}
	}
}

/**
 * Converts a multi-dimensional associative array into an array of key => values base on * your set field name.
 *
 * @param   array   the array to convert(Submit StdClass)
 * @param   string	the field name of the key field
 * @param   string	the field name of the value field
 * @return  array
 */
if (! function_exists('arr_for_select')) {
	function arr_for_select()
	{
		$args = func_get_args();

		$return = array();

		switch(count($args))
		{
			case 3:
				foreach ($args[0] as $itteration):
					if(is_object($itteration)) $itteration = (array) $itteration;
					$return[$itteration[$args[1]]] = $itteration[$args[2]];
				endforeach;
			break;

			case 2:
				foreach ($args[0] as $key => $itteration):
					if(is_object($itteration)) $itteration = (array) $itteration;
					$return[$key] = $itteration[$args[1]];
				endforeach;
			break;

			case 1:
				foreach ($args[0] as $itteration):
					$return[$itteration] = $itteration;
				endforeach;
			break;

			default:
				return false;
		}

		return $return;
	}
}

/**
 * Eloquent Model Object to the Select Array
 *
 * @param Object $data Eloquent Model Object
 * @param string $key Array Key
 * @param string $value Array Value
 * @param boolean $blank If you want to add --Select-- in return array, set true
 * @return array
 **/
if (! function_exists('e2s')) {
	function e2s($data, $key, $value, $blank = false)
	{
		$select = array();

			foreach ($data as $k => $v) {
				$select[$v->$key] = $v->$value;
			}

			if ($blank) {
				$select = array('' => '-- Select --') + $select;
			}

			return $select;
	}
}

/**
 * Get the country name by country key
 *
 * @param string $key
 * @return string
 **/
if (! function_exists('country')) {
	function country($key)
	{
		$lists = \Config::get('country');

		if (array_key_exists($key, $lists)) {
			return $lists[$key];
		}

		return null;
	}
}

/**
 * Gravatar Function
 *
 * @param string $email Email address for gravatar
 * @param int $size Size for gravatar. Default is 50
 * @param string $rating Rating for gravatar. Default is 'g'
 * @param string $default Default key for gravatar
 * @param boolean $url_only Set true if you want gravater url only. Default is false
 * @return	string URL
 */
if ( ! function_exists('gravatar'))
{
	function gravatar($email = '', $size = 50, $name = null, $rating = 'g', $default = null, $url_only = false)
	{
		 $base_url 	= '//www.gravatar.com/avatar/';
		 $email = empty($email) ? '00000000000000000000000000000000' : md5(strtolower(trim($email)));
		 $size = '?s=' . $size;
		 $rating = '&amp;r=' . $rating;
		 $default = is_null($default) ? '' : '&amp;d='.$default;

		 $gravatar = $base_url . $email . $size . $rating . $default;

		 if ($url_only != true)
		 {
			$gravatar = "<img src='$gravatar' alt='$name' class='gravatar' />";
		 }

		 return $gravatar;
	}
}

/**
 * Check the Internet Connection is avaliable or not
 *
 * @param string $url Optional
 * @param int $port Optional
 * @return boolean
 **/
if (! function_exists('checkOnline')) {
	function checkOnline($url = 'www.google.com', $port = 80)
	{
	    $connected = @fsockopen($url, $port);
	    if ($connected){
	        $is_conn = true;
	        fclose($connected);
	    }else{
	        $is_conn = false;
	    }

	    return $is_conn;
	}
}

/**
 * Date Format Helper Function
 *
 * @param string|DateTime $date Date object ot date string
 * @param string $format Date output format
 * @param boolean $string Need to use strtotime function. (eg: $date = "now")
 * @return boolean
 **/
if (! function_exists('rbDate')) {
	function rbDate($date, $format = "Y-m-d", $string = false)
	{
		if ($string) {
			$date = strtotime($date);
			return date($format, $date);
		}

		if (! $date instanceof DateTime) {
            $date = new DateTime($date);
        }

        return $date->format($format);
	}
}

/**
 * Helper Function of Setting::get().
 *
 * @param string $key
 * @return mixed
 **/
if (! function_exists('setting')) {
	function setting($key)
	{
        return \Setting::get($key);
	}
}

/**
 * Cycle is like a
 * https://docs.djangoproject.com/en/dev/ref/templates/builtins/#std:templatetag-cycle.
 * Cycle among the given strings or variables each time this tag is encountered.
 * Within a loop, cycles among the given strings each time through the loop.
 *
 * @param string
 * @return string
 **/
if (! function_exists('cycle')) {
	function cycle()
	{
		static $i;

		if (func_num_args() === 0) {
			$i = 0;
			return null;
		}

		$args = func_get_args();
		$key = $i++ % count($args);
		return $args[$key];
	}
}

/**
 * Helper Function is_home() for Theme Developer.
 *
 * @return boolean
 **/
if (! function_exists('is_home')) {
	function is_home()
	{
		$home_page = \Setting::get('home_page');
		$uri = \Uri::segments();

		if ( empty($uri) ) {
			return true;
		} elseif ( $home_page == $uri[0] ) {
			return true;
		}

		return false;
	}
}

/**
 * Transform Markdown to HTML with dflydev\markdown.
 *
 * @param string $text Markdown Text String
 * @return string
 **/
if (! function_exists('markdown')) {
	function markdown($text)
	{
    	$markdownParser = new dflydev\markdown\MarkdownParser();

    	return $markdownParser->transformMarkdown($text);
	}
}

/**
 * Transform MarkdownExtra to HTML with dflydev\markdown.
 *
 * @param string $text Markdown Text String
 * @return string
 **/
if (! function_exists('markdown_extra')) {
	function markdown_extra($text)
	{
    	$markdownParser = new dflydev\markdown\MarkdownExtraParser();

    	return $markdownParser->transformMarkdown($text);
	}
}

/**
 * Change Number with localization
 *
 * @param string $str Number string
 * @return string
 **/
if (! function_exists('num')) {
	function num($str)
	{
		\Translate::load('numbers');
		$nums = \Translate::get('numbers.formats');
		$search = array_keys($nums);
		$replace = array_values($nums);

		return str_replace($search, $replace, $str);
	}
}

if (! function_exists('csrf_meta')) {
	function csrf_meta()
	{
		$name = \Html::meta('csrf-param', \Config::get('app.security.csrf_key'));
		$key = \Html::meta('csrf-token', \Security::CSRFKeyOnly());

		return implode("\n\t", array($name, $key));
	}
}
