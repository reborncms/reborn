<?php

namespace Reborn\Translate\Loader;

use Reborn\Cores\Application;

/**
 * Translate File Loader for Reborn
 *
 * @package Reborn\Translate
 * @author Myanmar Links Professional Web Development Team
 **/
class PHPFileLoader implements LoaderInterface
{

    /**
     * Reborn Application Instance
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Default instance method for PHP File Loader
     *
     * @param  \Reborn\Cores\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Load the given lang file
     * eg: pages::label [label.php lang file from pages module]
     * eg: label [label.php file from core lang folder]
     *
     * @param  string      $resource Lang file name
     * @param  string      $locale   locale folder name
     * @return array|false
     */
    public function load($resource, $locale)
    {
        if (false !== strpos($resource, "::")) {
            return $this->moduleFileLoader($resource, $locale);
        } elseif ('theme@' === substr($resource, 0, 6)) {
            return $this->themeFileLoader($resource, $locale);
        } else {
            return $this->coreFileLoader($resource, $locale);
        }
    }

    /**
     * Load language file form active theme
     *
     * @param  string      $resource File resource string(eg: theme@caption)
     * @param  string      $locale   locale folder name
     * @return array|fasle
     **/
    protected function themeFileLoader($resource, $locale)
    {
        $theme = $this->app->theme->getThemePath();
        $file = substr($resource, 6);

        $filepath = $theme.'lang'.DS.$locale.DS.$file.'.php';

        if (file_exists($filepath)) {
            return require $filepath;
        } else {
            return false;
        }
    }

    /**
     * Lang file loader from the module
     *
     * @param  string      $resource File resource string(eg: pages::label)
     * @param  string      $locale   locale folder name
     * @return array|fasle
     */
    protected function moduleFileLoader($resource, $locale)
    {
        list($module, $file) = explode('::', $resource);

        $mod = \Module::get($module);

        if (is_null($mod)) return false;

        $path = $mod->path.DS.'lang'.DS;

        if (file_exists($f = $path.$locale.DS.$file.EXT)) {
            return require $f;
        } else {
            return false;
        }
    }

    /**
     * Lang file loader from the core lang folder
     *
     * @param  string      $resource File resource string(eg: label)
     * @param  string      $locale   locale folder name
     * @return array|fasle
     */
    protected function coreFileLoader($resource, $locale)
    {
        if (is_dir($dir = APP.'lang'.DS.$locale.DS)) {
            $dirPath = $dir;
        } else {
            return false;
        }

        if (file_exists($f = $dirPath.$resource.EXT)) {
            return require $f;
        } else {
            return false;
        }
    }

} // END class File Loader
