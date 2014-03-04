<?php

/**
 * Helper Functions for Reborn
 *
 * @package Reborn CMS
 * @author Myanmar Links Professional Web Development Team
 **/

if (! function_exists('dump')) {
    /**
     * Dump the given value. Use for var_dump().
     * If you want to die the script after dump, use as
     * <code>
     * 		dump($value, true);
     * </code>
     *
     * @param  mixed   $value
     * @param  boolean $die
     * @param  boolean $export Use var_export instead of var_dump
     * @param  string  $title  Title for Dump
     * @return string
     */
    function dump($value, $die = false, $export = false, $title = null)
    {
        if (is_string($die)) {
            $title = $die;
            $export = false;
            $die = false;
        }

        if (is_string($export)) {
            $title = $export;
            $export = false;
        }

        $backtrace = debug_backtrace();
        $file = $backtrace[0]['file'];
        $line = $backtrace[0]['line'];

        echo '<pre style="border: 1px dashed #71CF4D; padding: 5px 10px; background: #F2F2F2; margin: 10px 15px;">';
        if ($title) {
            echo '<h2 style="color: #565656; margin: 0px; padding-bottom: 5px; font-size: 13px; font-weight: normal;">Dump Title : '.$title.'</h2>';
        }
        echo '<h3 style="color: #990000; margin-top: 0px; padding-bottom: 5px; border-bottom: 1px dashed #999; font-size: 13px; font-weight: normal;">';
        echo 'Dump at &raquo; <span style="color: #000099;">'.$file.'</span> ';
        echo 'line number <span style="color: #009900;">'.$line.'</span>.</h3>';
        if ($export) {
            var_export($value);
        } else {
            var_dump($value);
        }
        echo '</pre>';
        if ($die) {
            die;
        }
    }
}

if (! function_exists('h')) {
    /**
     * Filter function for htmlspecialchars.
     * See detail at php's htmlspecialchars function.
     *
     * @param  string  $str
     * @param  int     $flags
     * @param  string  $encode
     * @param  boolean $doubleEncode
     * @return string
     */
    function h($str, $flags = ENT_QUOTES, $encode = 'UTF-8', $doubleEncode = true)
    {
        return htmlspecialchars($str, $flags, $encode, $doubleEncode);
    }
}

if (! function_exists('t')) {
    /**
     * Helper function for the translate.
     * See detail at Translate::get()
     *
     * @param  string $key
     * @param  array  $replace Replace value for langauge string
     * @param  string $default
     * @return string
     **/
    function t($key, $replace = null, $default = null)
    {
        return \Translate::get($key, $replace, $default);
    }
}

if (! function_exists('flash')) {
    /**
     * Get the flush session view (with html).
     * This function is same with Flash::flahsBox();
     *
     *
     * @return string
     */
    function flash()
    {
        return Reborn\Util\Flash::flashBox();
    }
}

if (! function_exists('value')) {
    /**
     * Return the value of given param,
     * If the value is closure, return closure result.
     *
     * @param  mixed $val
     * @return mixed
     */
    function value($val)
    {
        return ($val instanceof \Closure) ? $val() : $val;
    }
}

if (! function_exists('slug')) {
    /**
     * Convert the given string to slug(URL) type string
     *
     * @param  string $str
     * @param  string $separator Separator for space. Defaut is '-'
     * @return string
     **/
    function slug($str, $separator = '-')
    {
        return Reborn\Util\Str::slug($str, $separator);
    }
}

if (! function_exists('sanitize')) {
    /**
     * Sanitize the given string by given pattern
     * example :
     * <code>
     * 		// Output is "Who are you"
     * 		sanitize('Who are you?', 'A-Za-z-0-9-\s');
     * </code>
     *
     * @param  string $str     String
     * @param  string $pattern Regular Expression Pattern
     * @return string
     **/
    function sanitize($str, $pattern)
    {
        return Reborn\Util\Str::sanitize($str, $pattern);
    }
}

if (! function_exists('random_str')) {
    /**
     * Generate the random string.
     *
     * @param  integer $length Length of random string
     * @return string
     **/
    function random_str($length = 10)
    {
        return Reborn\Util\Str::random($length);
    }
}

if (! function_exists('array_pluck')) {
    /**
     * Pluck the value from given array
     *
     * @param  array  $arr
     * @param  string $key
     * @return array
     **/
    function array_pluck($arr, $key)
    {
        return array_map(function ($a) use ($key) { return $a[$key]; }, $arr);
    }
}

if (! function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     * This function is original from Illuminate\Supprot\src\helpers.php file.
     *
     * @param  array  $array
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) or ! array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (! function_exists('arr_is_multi')) {
    /**
     * Check the given array is multidimensional array or not.
     *
     * @param  array   $array
     * @return boolean
     */
    function arr_is_multi($array)
    {
        return count(array_filter($array, 'is_array')) > 0;
    }
}

if (! function_exists('arr_to_object')) {
    /**
     * Convert given array to object.
     *
     * @param  array  $array
     * @return object
     */
    function arr_to_object($array)
    {
        return json_decode(json_encode((array) $array));
    }
}

if (! function_exists('is_json')) {
    /**
     * Check given string is Json or not.
     * This function is original from
     * http://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
     *
     * @param  string  $string
     * @return boolean
     **/
    function is_json($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (! function_exists('remove_base_url')) {
    /**
     * Helper function fto remove baseUrl from given url
     *
     * @param  string $url
     * @return string
     **/
    function remove_base_url($url)
    {
        return str_replace(url(), '', $url);
    }
}

if (! function_exists('url')) {
    /**
     * Helper function for the Uri::create().
     *
     * @param  string $path Uri path to create.
     * @return string
     **/
    function url($path = '')
    {
        return Reborn\Http\Uri::create($path);
    }
}

if (! function_exists('rbUrl')) {
    /**
     * Alias of url()
     */
    function rbUrl($path = '')
    {
        return url($path);
    }
}

if (! function_exists('admin_url')) {
    /**
     * Helper function for the Uri::create() with ADMIN Panel Link.
     *
     * @param  string $path Uri path to create.
     * @return string
     **/
    function admin_url($path = '')
    {
        $admin = \Setting::get('adminpanel_url');
        $path = ltrim($path, '/');

        return Reborn\Http\Uri::create($admin.'/'.$path);
    }
}

if (! function_exists('adminUrl')) {
    /**
     * Alias of admin_url()
     */
    function adminUrl($path = '')
    {
        return admin_url($path);
    }
}

if (! function_exists('image_url')) {
    /**
     * Helper function for the Media Mdoule's image url.
     *
     * @param  string $name   File name
     * @param  int    $width  Image width
     * @param  int    $height Image height
     * @return string
     **/
    function image_url($name, $width = null, $height = null)
    {
        if (preg_match('/(.*)\.(\w+)\/(\d+)/', $name)) {
            return Reborn\Http\Uri::create().'image/'.$name;
        }

        $width = is_null($width) ? null : '/'.$width;
        $height = is_null($height) ? null : '/'.$height;

        return Reborn\Http\Uri::create().'image/'.$name.$width.$height;
    }
}

if (! function_exists('asset_url')) {
    /**
     * Helper function for the asset file path.
     *
     * @param  string $path Uri path to create.
     * @return string
     **/
    function asset_url($path = '')
    {
        return Reborn\Http\Uri::create($path);
    }
}

if (! function_exists('css')) {
    /**
     * Helper function for the Asset::css().
     *
     * @param  string $file   CSS filename with extension.
     * @param  string $media  Medaia type for CSS tag. Default is "all"
     * @param  string $module If you want to use CSS from module, set module name
     * @return string
     **/
    function css($file, $media = "all", $module = null)
    {
        $files = assetfile_preapre($file, $module);
        $href = (defined('ADMIN')) ? url('assets/a/styles/') : url('assets/styles/');

        return Reborn\Util\Html::style($href.'/'.$files, $media)."\n";
    }
}

if (! function_exists('less')) {
    /**
     * Helper function for the Asset::less().
     *
     * @param  string $file   LESS filename with extension.
     * @param  string $media  Medaia type for CSS tag. Default is "all"
     * @param  string $module If you want to use LESS from module, set module name
     * @return string
     **/
    function less($file, $media = "all", $module = null)
    {
        $files = assetfile_preapre($file, $module);
        $href = (defined('ADMIN')) ? url('assets/a/less/') : url('assets/less/');

        return Reborn\Util\Html::style($href.'/'.$files, $media)."\n";
    }
}

if (! function_exists('js')) {
    /**
     * Helper function for the Asset::js().
     *
     * @param  string $file   Js Filename with extension
     * @param  string $module If you want to use JS from module, set module name
     * @return string
     **/
    function js($file, $module = null)
    {
        $files = assetfile_preapre($file, $module);
        $src = (defined('ADMIN')) ? url('assets/a/scripts/') : url('assets/scripts/');
        $attrs = array(
                    'src'	=> $src.'/'.$files
                );

        return Reborn\Util\Html::tag('script', '', $attrs)."\n";
    }
}

if (! function_exists('img')) {
    /**
     * Helper function for the Asset::img().
     *
     * @param  string      $file   Image file name with extension
     * @param  string|null $alt    Text for the ALT.
     * @param  array       $attr   Other attribute for img tag (eg: title, id, etc.)
     * @param  string|null $module If you want file from module, set the module name.
     * @return string
     **/
    function img($file, $alt = null, $attr = array(), $module = null)
    {
        $src = (defined('ADMIN')) ? url('assets/a/images/') : url('assets/images/');
        $src = $src.'/'.assetfile_preapre($file, $module);

        return Reborn\Util\Html::img($src, $alt, $attr)."\n";
    }
}

/**
 * Prepare asset file url path for Munee
 *
 * @param string $file
 * @param string|null $module
 * @return string
 **/
function assetfile_preapre($file, $module = null)
{
    $files = array();

    if (is_array($file)) {
        foreach ($file as $f) {
            if (is_array($f) and isset($f['module'])) {
                $name = $f['file'];
                $mod = $f['module'];
                $files[] = $mod.'__'.$name;
            } else {
                if (is_null($module)) {
                    $files[] = $f;
                } else {
                    $files[] = $module.'__'.$f;
                }
            }
        }
    } else {
        if (is_null($module)) {
            $files[] = $file;
        } else {
            $files[] = $module.'__'.$file;
        }
    }

    return join(',', $files);
}

if (! function_exists('assetPath')) {
    /**
     * Helper function for the Asset File Path (css, js, img).
     *
     * @param  string $type   Asset file type
     * @param  string $module Module name if file is exists in module
     * @return string
     **/
    function assetPath($type = null, $module = null)
    {
        $finder = new Reborn\Asset\AssetFinder();
        switch ($type) {
            case 'css' :
                $path = $finder->path('css', $module);
                break;
            case 'js' :
                $path = $finder->path('js', $module);
                break;
            case 'img' :
            case 'image' :
                $path = $finder->path('img', $module);
                break;
            default :
                $path = $finder->path(null, $module);
                break;
        }

        return url(str_replace(BASE, '', $path));
    }
}

if (! function_exists('global_asset')) {
    /**
     * Hlper Function for Global Assets Tags
     *
     * @param  string $type     Asset Type
     * @param  string $filename asset file name
     * @return string
     */
    function global_asset($type, $filename)
    {
        $files = assetfile_preapre($filename);
        $url = url('assets/global/');

        switch ($type) {
            case 'css' :
                return Reborn\Util\Html::style($url.'/styles/'.$files)."\n";
                break;
            case 'js' :
                $attrs = array('src'	=> $url.'/scripts/'.$files);

                return Reborn\Util\Html::tag('script', '', $attrs)."\n";
                break;
            case 'img' :
            case 'image' :
                $path = $url.'/images/'.str_replace(array('\\', '/'), '/', $files);

                return Reborn\Util\Html::img($path)."\n";
                break;
            default :
                return null;
                break;
        }
    }
}

if (! function_exists('arr_for_select')) {
    /**
     * Converts a multi-dimensional associative array into an array of key => values base on * your set field name.
     *
     * @param   array   the array to convert(Submit StdClass)
     * @param   string	the field name of the key field
     * @param   string	the field name of the value field
     * @return array
     */
    function arr_for_select()
    {
        $args = func_get_args();

        $return = array();

        switch (count($args)) {
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

if (! function_exists('e2s')) {
    /**
     * Eloquent Model Object to the Select Array
     *
     * @param  Object  $data  Eloquent Model Object
     * @param  string  $key   Array Key
     * @param  string  $value Array Value
     * @param  boolean $blank If you want to add --Select-- in return array, set true
     * @return array
     **/
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

if (! function_exists('country')) {
    /**
     * Get the country name by country key
     *
     * @param  string $key
     * @return string
     **/
    function country($key)
    {
        $lists = Reborn\Config\Config::get('country');

        if (array_key_exists($key, $lists)) {
            return $lists[$key];
        }

        return null;
    }
}

if ( ! function_exists('gravatar')) {
    /**
     * Gravatar Function
     *
     * @param  string  $email    Email address for gravatar
     * @param  int     $size     Size for gravatar. Default is 50
     * @param  string  $name     Name of gravatar, using user's name
     * @param  string  $class    Class attributes
     * @param  string  $rating   Rating for gravatar. Default is 'g'
     * @param  string  $default  Default key for gravatar
     * @param  boolean $url_only Set true if you want gravater url only. Default is false
     * @return string  URL
     */
    function gravatar($email = '', $size = 50, $name = null, $class= null, $rating = 'g', $default = null, $url_only = false)
    {
         $base_url 	= '//www.gravatar.com/avatar/';
         $email = empty($email) ? '00000000000000000000000000000000' : md5(strtolower(trim($email)));
         $size = '?s=' . $size;
         $rating = '&amp;r=' . $rating;
         $default = is_null($default) ? '' : '&amp;d='.$default;

         $gravatar = $base_url . $email . $size . $rating . $default;

         if ($url_only != true) {
            $gravatar = "<img src='$gravatar' alt='$name' class='gravatar $class' />";
         }

         return $gravatar;
    }
}

if (! function_exists('checkOnline')) {
    /**
     * Check the Internet Connection is avaliable or not
     *
     * @param  string  $url  Optional
     * @param  int     $port Optional
     * @return boolean
     **/
    function checkOnline($url = 'www.google.com', $port = 80)
    {
        $app = Facade::getApplication();

        $env = $app->getAppEnvironment();

        // Make tweak for error reporting for this function
        if ( ! $app->runInProduction() ) {
            $app->setAppEnvironment('production');
        }

        $is_online = true;

        if (! $connected = @fsockopen($url, $port, $num, $error, 20)) {
            $is_online = false;
        } else {
            fclose($connected);
        }

        $app->setAppEnvironment($env);

        return $is_online;
    }
}

if (! function_exists('setting')) {
    /**
     * Helper Function of Setting::get().
     *
     * @param  string $key
     * @return mixed
     **/
    function setting($key)
    {
        return \Setting::get($key);
    }
}

if (! function_exists('cycle')) {
    /**
     * Cycle is like a
     * https://docs.djangoproject.com/en/dev/ref/templates/builtins/#std:templatetag-cycle.
     * Cycle among the given strings or variables each time this tag is encountered.
     * Within a loop, cycles among the given strings each time through the loop.
     *
     * @param string
     * @return string
     **/
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

if (! function_exists('is_home')) {
    /**
     * Helper Function is_home() for Theme Developer.
     *
     * @return boolean
     **/
    function is_home()
    {
        $home_page = \Setting::get('home_page');
        $uri = Reborn\Http\Uri::segments();

        if ( empty($uri) ) {
            return true;
        } elseif ($home_page == $uri[0]) {
            return true;
        }

        return false;
    }
}

if (! function_exists('markdown')) {
    /**
     * Transform Markdown to HTML with dflydev\markdown.
     *
     * @param  string $text Markdown Text String
     * @return string
     **/
    function markdown($text)
    {
        $markdownParser = new dflydev\markdown\MarkdownParser();

        return $markdownParser->transformMarkdown($text);
    }
}

if (! function_exists('markdown_extra')) {
    /**
     * Transform MarkdownExtra to HTML with dflydev\markdown.
     *
     * @param  string $text Markdown Text String
     * @return string
     **/
    function markdown_extra($text)
    {
        $markdownParser = new dflydev\markdown\MarkdownExtraParser();

        return $markdownParser->transformMarkdown($text);
    }
}

if (! function_exists('num')) {
    /**
     * Change Number with localization
     * This is only for Myanmar Number
     *
     * @param  string $str Number string
     * @return string
     **/
    function num($str)
    {
        \Translate::load('numbers');
        $nums = \Translate::get('numbers.formats');
        $search = array_keys($nums);
        $replace = array_values($nums);

        return str_replace($search, $replace, $str);
    }
}

if (! function_exists('theme_config')) {
    /**
     * Helper function for theme_config value.
     *
     * @param  string $key     Config Key
     * @param  mixed  $default Default value to return if config key doesn't found
     * @return mixed
     **/
    function theme_config($key, $default = null)
    {
        $theme = Reborn\Cores\Facade::getApplication()->theme;

        try {
            $theme_config = $theme->config();
        } catch (Exception $e) {
            return $default;
        }

        return array_get($theme_config, $key, $default);
    }
}

if (!function_exists('first')) {
    /**
     * This function will be help in looping.
     * If you need something at looping's first time only.
     * example :
     * <code>
     * $i = 0;
     * foreach ($images as $img) {
     * 		if ($i == 0) {
     * 			echo "<div class="active">
     * 		} else {
     * 			echo "<div>";
     * 		}
     * 		<img src="$img">
     * 		</div>
     * 		$i++;
     * }
     * //Use first()
     * foreach ($images as $img) {
     * 		<div class="first('active')">
     * 			<img src="$img">
     * 		</div>
     * }
     * </code>
     *
     * @return string
     **/
    function first($var)
    {
        static $j = 0;

        if (0 === $j) {
            $j++;

            return $var;
        }
    }
}

if (! function_exists('template_parse')) {
    /**
     * Helper function for Reborn Parser render for template string
     *
     * @param  string $template Template string
     * @param  array  $data     Data array
     * @return string
     **/
    function template_parse($template, $data = array())
    {
        return Reborn\Cores\Facade::getApplication()->view->renderAsStr($template, $data);
    }
}

if (! function_exists('navigation')) {
    /**
     * Helper function of Navigation Render
     *
     * @param  string $nav  Navigation group name
     * @param  string $type Navigation Style Type
     * @return string
     **/
    function navigation($nav = 'header', $type = 'reborn')
    {
        return \Navigation\Builder\Manager::choose($nav, $type);
    }
}

if (!function_exists('formatSizeUnits')) {
    /**
     * Displays the file size given the number of bytes
     * # Snippet from PHP Share: http://www.phpshare.org
     *
     * @param  integer $bytes Bytes value
     * @return void
     **/
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

if (!function_exists('formatSizeToBytes')) {
    /**
     * Convert file size fromat string to bytes integer
     *
     * @param  string  $size File size string (eg:2KB)
     * @return integer
     **/
    function formatSizeToBytes($size)
    {
        $unit = strtolower(substr($size, -2));
        $value = substr($size, 0, -2);

        switch ($unit) {
            case 'gb':
                return $value * 1073741824;
                break;

            case 'mb':
                return $value * 1048576;
                break;

            case 'kb':
                return $value * 1024;
                break;

            default:
                $bytes = substr($size, -5);
                $bs_value = substr($size, 0, -5);
                $byte = substr($size, -4);
                $b_value = substr($size, 0, -4);

                if ($bytes == 'bytes') {
                    return $bs_value;
                } elseif ($byte == 'byte') {
                    return $b_value;
                } else {
                    0;
                }
                break;
        }
    }
}

if (!function_exists('image_data')) {
    /**
     * Get image data url string.
     *
     * @param  string      $image Image with full path
     * @return string|null
     **/
    function image_data($image)
    {
        if (! is_file($image) ) return null;

        // Read image data and convert base64 encoding data string
        $data = base64_encode(file_get_contents($image));

        return 'data: '.mime_content_type($image).';base64,'.$data;
    }
}

if (!function_exists('tree_lists')) {
    /**
     * Convert tree lists array base on parent field
     *
     * @param  array  $lists
     * @param  string $parent Parent field name. Default is "parent_id"
     * @return array
     **/
    function tree_lists($lists, $parent = 'parent_id')
    {
        $map = array(
            0 => array('child' => array())
        );

        foreach ($lists as &$list) {
            $list['child'] = array();
            $map[$list['id']] = &$list;
        }

        foreach ($lists as &$list) {
            $map[$list[$parent]]['child'][] = &$list;
        }

        return $map[0]['child'];
    }
}

if (!function_exists('ga')) {
    /**
     * Google Analytics JS Code.
     * See detail of google analytics at
     * https://developers.google.com/analytics/devguides/collection/analyticsjs/
     *
     * @param  string $gid    Google analytics ID
     * @param  string $domain Domain name of tracking site
     * @return string
     **/
    function ga($gid, $domain = null)
    {
        if (is_null($domain)) {
            $domain = url();
        }

        $domain = str_replace(array('http://', 'https://', 'www.'), '', $domain);

        return <<<GA
<script>
        (function (i,s,o,g,r,a,m) {i['GoogleAnalyticsObject']=r;i[r]=i[r]||function () {
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '$gid', '$domain');
        ga('send', 'pageview');

    </script>
GA;
    }
}

if (!function_exists('jquery')) {
    /**
     * Get jQuery Google CDN Link.
     *
     * @param  string $version jQuery version number. Defaut is "1.10.2"
     * @return string
     **/
    function jquery($version = '1.10.2')
    {
        return  '<script src="//ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js"></script>';
    }
}
