<?php

namespace Reborn\Config;

use Reborn\Cores\Application;
use Reborn\Module\ModuleManager;

/**
 * Config Class for Reborn
 *
 * @package Reborn\Config
 * @author Myanmar Links Professional Web Development Team
 **/
class Config
{
    /**
     * Reborn Application (IOC) Container instance
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Varialbe for configuration items
     *
     * @var array
     **/
    public $items = array();

    /**
     * Varialbe for configuration items cache
     *
     * @var array
     **/
    protected $caches = array();

    /**
     * Constructor Method for Config Object
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Set configuration values
     *
     * @param  string                $key
     * @param  mixed                 $value
     * @return \Reborn\Config\Config
     */
    public function set($key, $value)
    {
        // Check this key's value is have in caches array
        // If key's value already have in caches, Set from caches['values']
        if (isset($this->caches[$key]['value'])) {
            $this->caches[$key]['value'] = $value;
        } else {
            $this->get($key);
            $this->caches[$key]['value'] = $value;
        }

        return $this;
    }

    /**
     * Set the Config value and Get the this value with directly.
     * Same with set() method but this method will return setter value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    /*public static function setToGet($key, $value)
    {
        // Check this key's value is have in caches array
        // If key's value already have in caches, Set and return from caches['values']
        if (isset(static::$caches[$key]['value'])) {
            return static::$caches[$key]['value'] = $value;
        } else {
            static::get($key);

            return static::$caches[$key]['value'] = $value;
        }
    }*/

    /**
     * Get the configuration value
     *
     * @param  string $key
     * @param  mixed  $default Default value for required $key is not set
     * @return array
     */
    public function get($key, $default = null)
    {
        // Check this key's value is have in caches array
        // If key's value already have in caches, return from caches['values']
        if (isset($this->caches[$key]['value'])) {
            return $this->caches[$key]['value'];
        }

        list($config, $config_key) = $this->load($key);

        if (is_null($config_key)) {
            return $config;
        } else {
            $value = array_get($config, $config_key, $default);

            // Check config value is colsure or not.
            // Value is colsure, return closure result.
            $value = value($value);

            if ($default != $value) {
                return $this->caches[$key]['value'] = $value;
            } else {
                // If config items is doesn't exit, remove this key from caches array
                $this->delete($key);

                return $value;
            }
        }
    }

    /**
     * Delete (unset) the config key from the config caches
     *
     * @param string $key
     */
    public function delete($key)
    {
        if (isset($this->caches[$key])) {
            unset($this->caches[$key]);
        }
    }

    /**
     * Reset the Config caches data
     *
     */
    public function reset()
    {
        $this->caches = array();
    }

    /**
     * Config file load method
     *
     * @param  string $file
     * @return array
     */
    public function load($file)
    {
        $configs = array();

        list($module, $file, $key) = $this->keyParser($file);

        if (is_null($module)) {
            $configs = $this->getFromHeart($file);
        } else {
            $configs = $this->getFromModule($module, $file);
        }

        return array($configs, $key);
    }

    /**
     * Get config from Reborn Heart
     *
     * @param  string $file
     * @return array
     **/
    protected function getFromHeart($file)
    {
        $basepath = APP.'config'.DS;

        return $this->getMergedValues($basepath, $file);
    }

    /**
     * Get config from Reborn Module
     *
     * @param  string $module
     * @param  string $file
     * @return array
     **/
    protected function getFromModule($module, $file)
    {
        $basepath = ModuleManager::get($module, 'path').DS.'config'.DS;

        return $this->getMergedValues($basepath, $file);
    }

    /**
     * Get Merged config values for environment stage.
     *
     * @param  string $basepath
     * @param  string $file
     * @return array
     **/
    protected function getMergedValues($basepath, $file)
    {
        $configs = array();

        // Get configs from default file
        if (file_exists($path = $basepath.$file.EXT)) {
            $configs = array_merge($configs, require $path);
        }

        // Check from Environment folder name
        $env = $this->app['env'];

        if ( file_exists($path = $basepath.$env.DS.$file.EXT) ) {
            $env_configs = require $path;

            $configs = array_merge($configs, $env_configs);
        }

        return $configs;
    }

    /**
     * Parse the key string to array(moduleName, fileName, configItems) value.
     *
     * @param  string $key
     * @return array
     */
    protected function keyParser($key)
    {
        if (isset($this->caches[$key])) {
            return $this->caches[$key];
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
            return $this->caches[$org_key] = array($module, $file, implode('.', array_slice($file_item, 1)));
        }

        return $this->caches[$org_key] = array($module, $file, null);
    }

} // END class Config
