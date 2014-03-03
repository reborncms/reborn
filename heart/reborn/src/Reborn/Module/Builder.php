<?php

namespace Reborn\Module;

use Composer\Autoload\ClassLoader;

/**
 * Module Builder (Data Container) Class
 *
 * @package Reborn\Module
 * @author MyanmarLinks Professional Web Development Team
 **/
class Builder implements \ArrayAccess
{
    /**
     * Module's data container
     *
     * @var array
     **/
    protected $data;

    /**
     * Module is already regsiter with Autoloader
     *
     * @var boolean
     **/
    protected $is_loaded = false;

    /**
     * Default instance method
     *
     * @param  string $path
     * @param  array  $data
     * @return void
     **/
    public function __construct($path, $data)
    {
        $data['path'] = $path;

        $this->data = $data;
    }

    /**
     * Module register with Composer\Autoload\ClassLoader.
     *
     * @return boolean
     **/
    public function load()
    {
        if (!class_exists('Composer\Autoload\ClassLoader')) {
            throw new RbException("ModuleLoader need \"Composer\Autoload\ClassLoader\"");
        }

        if ($this->is_loaded) {
            return true;
        }

        if ($this->data['enabled']) {
            $path = $this->data['path'];
            $ns = $this->data['ns'];
            $loader = new ClassLoader();
            $loader->add($ns, $path.DS.'src');
            $loader->register();
            $this->is_loaded = true;

            return true;
        }

        return false;
    }

    /**
     * Check module is core module.
     *
     * @return boolean
     **/
    public function isCore()
    {
        return $this->data['isCore'];
    }

    /**
     * Check module is installed or not.
     *
     * @return boolean
     **/
    public function isInstalled()
    {
        return $this->installed === true;
    }

    /**
     * Check module is enabled or not.
     *
     * @return boolean
     **/
    public function isEnabled()
    {
        return $this->data['enabled'];
    }

    /**
     * Check module need to update
     *
     * @return boolean
     **/
    public function needToUpdate()
    {
        if (version_compare($this->data['version'], $this->data['db_version']) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Get module display name by langauge
     *
     * @param  string $lang
     * @return string
     **/
    public function displayName($lang = 'en')
    {
        if (isset($this->data['display_name'][$lang])) {
            return $this->data['display_name'][$lang];
        }

        return $this->data['display_name']['en'];
    }

    /**
     * Get module description by langauge
     *
     * @param  string $lang
     * @return string
     **/
    public function desc($lang = 'en')
    {
        if (isset($this->data['description'][$lang])) {
            return $this->data['description'][$lang];
        }

        return $this->data['description']['en'];
    }

    /**
     * Get module creator inforrmation data
     *
     * @return array
     **/
    public function authorInfo()
    {
        $info = array();
        $info['name'] = $this->data['author'];
        $info['email'] = $this->data['author_email'];
        $info['url'] = $this->data['author_url'];

        return $info;
    }

    /**
     * Dynamically access for property with PHP's Magic method.
     *
     * @param  string $name
     * @return mixed
     **/
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * Determine if an data exists at an offset.
     *
     * @param  mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Get an data at a given offset.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Set the data at a given offset.
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->data[] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Unset the data at a given offset.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

} // END class Builder
