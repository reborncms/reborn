<?php

namespace Reborn\Config;

/**
 * Config Class for Reborn
 *
 * @package Reborn\Config
 * @author Myanmar Links Professional Web Development Team
 **/
class Config
{
    /**
     * Varialbe for configuration items
     *
     * @var array
     **/
    public static $items = array();

    /**
     * Varialbe for configuration items cache
     *
     * @var array
     **/
    protected static $caches = array();

    /**
     * Constructor Method for Config Object
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Set configuration values
     *
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        // Check this key's value is have in caches array
        // If key's value already have in caches, Set from caches['values']
        if (isset(static::$caches[$key]['value'])) {
            static::$caches[$key]['value'] = $value;
        } else {
            static::get($key);
            static::$caches[$key]['value'] = $value;
        }
    }

    /**
     * Set the Config value and Get the this value with directly.
     * Same with set() method but this method will return setter value.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function setToGet($key, $value)
    {
        // Check this key's value is have in caches array
        // If key's value already have in caches, Set and return from caches['values']
        if (isset(static::$caches[$key]['value'])) {
            return static::$caches[$key]['value'] = $value;
        } else {
            static::get($key);
            return static::$caches[$key]['value'] = $value;
        }
    }

    /**
     * Get the configuration value
     *
     * @param string $key
     * @param mixed $default Default value for required $key is not set
     * @return array
     */
    public static function get($key, $default = null)
    {
        // Check this key's value is have in caches array
        // If key's value already have in caches, return from caches['values']
        if (isset(static::$caches[$key]['value'])) {
            return static::$caches[$key]['value'];
        }

        list($config, $config_key) = static::load($key);

        if (is_null($config_key)) {
            return $config;
        } else {
            $value = array_get($config, $config_key, $default);

            // Check config value is colsure or not.
            // Value is colsure, return closure result.
            $value = value($value);

            if ($default != $value) {
                return static::$caches[$key]['value'] = $value;
            } else {
                // If config items is doesn't exit, remove this key from caches array
                static::delete($key);

                return $value;
            }
        }
    }

    /**
     * Delete (unset) the config key from the config caches
     *
     * @param string $key
     */
    public static function delete($key)
    {
        if (isset(static::$caches[$key])) {
            unset(static::$caches[$key]);
        }
    }

    /**
     * Reset the Config caches data
     *
     */
    public static function reset()
    {
        static::$caches = array();
    }

    /**
     * Config file load method
     *
     * @param string $file
     * @return array
     */
    public static function load($file)
    {
        $configs = array();

        list($module, $file, $key) = static::keyParser($file);

        if (is_null($module)) {
            if (file_exists($path = APP.'config'.DS.$file.EXT)) {
                $configs = array_merge($configs, require $path);
            }
        } else {
            $module = ucfirst($module);
            if (file_exists($path = CORE_MODULES.$module.DS.'config'.DS.$file.EXT)) {
                $configs = array_merge($configs, require $path);
            } elseif(file_exists($path = MODULES.$module.DS.'config'.DS.$file.EXT)) {
                $configs = array_merge($configs, require $path);
            }
        }

        return array($configs, $key);
    }

    /**
     * Parse the key string to array(moduleName, fileName, configItems) value.
     *
     * @param string $key
     * @return array
     */
    protected static function keyParser($key)
    {
        if (isset(static::$caches[$key])) {
            return static::$caches[$key];
        }

        $module = null;

        $org_key = $key;

        if (false !== strpos($key, '::')) {
            $explode_key = explode('::', $key);
            $module = $explode_key[0];
            $key = $explode_key[1];
        }

        $file_item = explode('.', $key);
        $file = $file_item[0];

        if (count($file_item) >= 2) {
            return static::$caches[$org_key] = array($module, $file, implode('.', array_slice($file_item, 1)));
        } else {
            return static::$caches[$org_key] = array($module, $file, null);
        }
    }

} // END class Config
