<?php

namespace Reborn\Translate\Loader;

/**
 * Translate File Loader for Reborn
 *
 * @package Reborn\Translate
 * @author Myanmar Links Professional Web Development Team
 **/
class PHPFileLoader implements LoaderInterface
{
    /**
     * Variable for default locale
     *
     * @var string
     **/
    protected $locale;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $fallbackLocale = "en";

    /**
     * Variable for loaded language files
     *
     * @var array
     **/
    protected $loaded = array();

    protected $paths = array();

    /**
     * Construct Method
     *
     * @param array $paths File Paths
     * @param array $locale Locales to loaded [default, fallback]
     * @param array $options
     */
    public function __construct($paths = null, $locale = null)
    {
        $this->paths = $paths;
        $this->locale = $locale;
    }

    /**
     * Load the given lang file
     * eg: pages::label [label.php lang file from pages module]
     * eg: label [label.php file from core lang folder]
     *
     * @param string $resource Lang file name
     * @return array|false
     */
    public function load($resource)
    {
        if (false !== strpos($resource, "::")) {
            return $this->moduleFileLoader($resource);
        } else {
            return $this->coreFileLoader($resource);
        }
    }

    /**
     * Get the array key from given data array.
     * If key is doesn't exits in data array, return the default value
     *
     * @param string $key Key string to get
     * @param array $data Data array
     * @param mixed $default Default value
     * @return mixed
     */
    public function get($key, $data, $default)
    {
        if (false !== strpos($key, '::')) {
            $k =explode('::', $key);
            $key = explode('.', $k[1], 2);
            $key = $key[1];
        } else {
            $key = explode('.', $key, 2);
            $key = $key[1];
        }

        $value = array_get($data, $key, $default);

        return $value;
    }

    /**
     * Lang file loader from the module
     *
     * @param string $resource File resource string(eg: pages::label)
     * @return array|fasle
     */
    protected function moduleFileLoader($resource)
    {
        list($module, $file) = explode('::', $resource);

        $module = ucfirst($module);

        $mod = \Module::getData($module);

        if (is_null($mod)) return false;

        $path = $mod['path'].'lang'.DS;

        if (file_exists($f = $path.$this->locale.DS.$file.EXT)) {
            return require $f;
        } elseif(file_exists($f = $path.$this->fallbackLocale.DS.$file.EXT)) {
            return require $f;
        } else {
            return false;
        }
    }

    /**
     * Lang file loader from the core lang folder
     *
     * @param string $resource File resource string(eg: pages::label)
     * @return array|fasle
     */
    protected function coreFileLoader($resource)
    {
        if (is_dir($dir = APP.'lang'.DS.$this->locale.DS)) {
            $dirPath = $dir;
        } elseif (is_dir($dir = APP.'lang'.DS.$this->fallbackLocale.DS)) {
            $dirPath = $dir;
        }

        if (file_exists($f = $dirPath.$resource.EXT)) {
            return require $f;
        } else {
            return false;
        }
    }

} // END class File Loader
