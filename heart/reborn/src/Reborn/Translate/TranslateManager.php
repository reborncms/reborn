<?php

namespace Reborn\Translate;

use Reborn\Translate\Loader\LoaderInterface;

/**
 * Translate Manager Class for Reborn.
 *
 * @package Reborn\Translate
 * @author Myanmar Links Professional Web Development Team
 **/
class TranslateManager
{
    /**
     * Variable for locale
     *
     * @var string
     **/
    public static $locale = null;

    /**
     * Variable for fallback locale
     *
     * @var string
     **/
    public static $fallback_locale;

    /**
     * File Loader Instance
     *
     * @var \Reborn\Translate\Loader\LoaderInterface
     **/
    protected static $loader;

    /**
     * Language cache variable
     *
     * @var array
     **/
    protected static $caches = array();

    /**
     * Language resource shortcut lists
     *
     * @var array
     **/
    protected static $shortcut = array();

    /**
     * Initialize method for Translate Manager.
     * Set locale and fallback locale for Manager.
     *
     * @param  string $locale          Locale code
     * @param  string $fallback_locale Fallback locale, default is 'en'
     * @return viod
     */
    public static function initialize($locale, $fallback_locale = 'en')
    {
        static::$locale = $locale;
        static::$fallback_locale = $fallback_locale;

        static::setLoader(\Facade::getApplication()->translate_loader);
    }

    /**
     * Set locale for Translate
     *
     * @param  string $locale Locale code
     * @return void
     **/
    public static function setLocale($locale)
    {
        static::$locale = $locale;
    }

    /**
     * Get locale
     *
     * @return string
     **/
    public static function getLocal()
    {
        return static::$locale;
    }

    /**
     * Set File Loader
     *
     * @param  string $loader File Loader Key Name
     * @return void
     **/
    public static function setLoader(LoaderInterface $loader)
    {
        static::$loader = $loader;
    }

    /**
     * Get file loader instance
     *
     * @return \Reborn\Translate\Loader\LoaderInterface
     **/
    public static function getLoader()
    {
        return static::$loader;
    }

    /**
     * Load the language file
     * If you will use language file, first you need to load the these file
     *
     * <code>
     *  // Load lang navigation file from naviagtion module.
     *  Trnaslate::load('navigation::navigation');
     *  // Now you can call lang(language) string
     *  Translate::get('navigation::navigation.title');
     * </code>
     *
     * @param  string  $resource Resource File name
     * @param  string  $subname  SubName for Resource File. This is shortcut name
     * @param  string  $locale   This is optional
     * @return boolean
     */
    public static function load($resource, $shortcut = null, $locale = null)
    {
        $locale = is_null($locale) ? static::$locale : $locale;

        // If resource is already loaded, return true.
        if (isset(static::$caches[$resource][$locale])) {

            if (!is_null($shortcut)) {
                static::addShortcut($resource, $shortcut);
            }

            return true;
        }

        $loader = static::getLoader();

        if (!$loader instanceof LoaderInterface) {
            $driver = \Facade::getApplication()->translate_loader;

            if (is_null($driver)) {
                $driver = new \Reborn\Translate\Loader\PHPFileLoader(
                                            \Facade::getApplication()
                                        );
            }

            static::setLoader($driver);
            $loader = static::getLoader();
        }

        // First load with $locale
        $data = $loader->load($resource, $locale);

        // If didn't find $locale, load with fallback locale
        if (!$data) {
            $data = $loader->load($resource, static::$fallback_locale);
        }

        if (!$data) {
            return false;
        }

        // Add to caches
        static::$caches[$resource][$locale] = $data;
        if (!is_null($shortcut)) {
            static::addShortcut($resource, $shortcut);
        }

        return true;
    }

    /**
     * Add shortcut name for resource name
     *
     * @param  string $resource Resource name
     * @param  string $shortcut Shortcut name for resource
     * @return void
     **/
    public static function addShortcut($resource, $shortcut)
    {
        if (!isset( static::$shortcut[$shortcut] )) {
            static::$shortcut[$shortcut] = $resource;
        }
    }

    /**
     * Get the language resource string.
     *
     * @param  string      $key
     * @param  array       $replace Replace value for langauge string
     * @param  string      $default Default result, will return not found $key
     * @return string|null
     **/
    public static function get($key, $replace = null, $default = null)
    {
        list($resource, $key) = static::parseKey($key);

        $locale = static::$locale;

        // resoruce maked shortcut
        if (isset(static::$shortcut[$resource])) {
            $resource = static::$shortcut[$resource];
        }

        $have = true;

        // If data does't exits in cache, call the load()
        $lang_resources = isset(static::$caches[$resource]) ? static::$caches[$resource] : null;
        if (! is_null($lang_resources) || !isset($lang_resources[$locale]) ) {
            $have = static::load($resource);
        }

        // language resource not found. Return default
        if (!$have) return static::replacer($default, $replace);

        $lang = array_get(static::$caches[$resource][$locale], $key, $default);

        return static::replacer($lang, $replace);
    }

    /**
     * Parse the key name with resource and lang key
     *
     * @param  string $key
     * @return array
     **/
    protected static function parseKey($key)
    {
        return explode('.', $key, 2);
    }

    /**
     * String replacae with key and value
     *
     * @param  string $str     Language string
     * @param  array  $replace Replace data
     * @return string
     **/
    protected static function replacer($str, $replace)
    {
        if (is_null($replace)) return $str;

        foreach ((array) $replace as $k => $v) {
            $str = str_replace('{:'.$k.'}', $v, $str);
        }

        return $str;
    }

} // END class Translate Manager
