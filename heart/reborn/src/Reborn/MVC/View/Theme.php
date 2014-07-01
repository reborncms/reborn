<?php

namespace Reborn\MVC\View;

use Reborn\Util\Str;
use Reborn\Cores\Application;
use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;
use Reborn\Exception\FileNotFoundException;

/**
 * Theme class for the Reborn
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
class Theme
{
    /**
     * Variable for the theme name
     *
     * @var string
     **/
    protected $theme;

    /**
     * Variable for the theme folder path
     *
     * @var string
     **/
    protected $path;

    /**
     * Application (IOC) Container instance
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Default constructor method
     *
     * @param  \Reborn\Cores\Application $app
     * @param  string                    $name Theme name
     * @param  string                    $path Theme path
     * @return void
     **/
    public function __construct(Application $app, $name, $path)
    {
        $this->app = $app;

        $this->theme = $name;

        $this->path = $path;
    }

    /**
     * Get all theme from private path and shared path.
     *
     * @param  boolean $name_only
     * @return array
     **/
    public function all($name_only = false)
    {
        $paths = $this->getThemeFolderPaths();

        $all = array();

        $type = 'private';
        foreach ($paths as $path) {
            $all[$type] = Dir::get($path.'*', GLOB_ONLYDIR);
            $type = 'shared';
        }

        if ($name_only) {
            $names = array();
            foreach ($all as $type => $paths) {
                foreach ($paths as $path) {
                    $names[] = basename($path);
                }
            }

            return $names;
        }

        return $all;
    }

    /**
     * Find theme from theme paths
     *
     * @param  string      $theme
     * @return string|null
     **/
    public function findTheme($theme)
    {
        $paths = $this->getThemeFolderPaths($theme);

        foreach ($paths as $path) {
            if (Dir::is($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Set the theme path
     *
     * @param  string $path Theme path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the active them path
     *
     * @return string
     **/
    public function getThemePath()
    {
        if (! Dir::is($this->path.$this->theme)) {
            throw new \Exception("{$this->theme} folder doesn't exists in {$this->path}");
        }

        return $this->path.$this->theme.DS;
    }

    /**
     * Get the layouts list array from active theme.
     * Layout list will return only layout file name, not include path
     *
     * @return array
     **/
    public function getLayouts()
    {
        $path = $this->path.$this->theme.DS.'views'.DS.'layout'.DS;
        $layouts = array();
        $all = glob($path.'*.html');

        foreach ($all as $file) {
            $layouts[] = str_replace($path, '', $file);
        }

        return $layouts;
    }

    /**
     * Get the layouts list array from given theme.
     * Layout list will return only layout file name, not include path
     *
     * @return array
     **/
    public function layoutsFrom($name)
    {
        $path = $this->findTheme($name);

        $layouts = array();

        if (is_null($path)) return $layouts;

        $all = glob($path.'views'.DS.'layout'.DS.'*.html');

        foreach ($all as $s) {
            $layouts[] = str_replace($path.'views'.DS.'layout'.DS, '', $s);
        }

        return $layouts;
    }

    /**
     * Check the given layout name is exists in the active theme's layout folder.
     *
     * @param  string  $name Layout name, no need file extension
     * @return boolean
     **/
    public function hasLayout($name)
    {
        $file = $this->path.$this->theme.DS.'views'.DS.'layout'.DS.$name.'.html';

        return File::is($file);
    }

    /**
     * Check the given file name is has in this theme
     *
     * @param  string  $name   File name
     * @param  string  $folder Default is partial
     * @return boolean
     */
    public function hasFile($name, $folder = 'partial')
    {
        $file = $this->path.$this->theme.DS.'views'.DS.$folder.DS.$name.'.html';

        return File::is($file);
    }

    /**
     * Get the theme information from active theme's info file or given theme name
     *
     * @param  string  $name          Theme name, if you set this value is null
     *                                return info from active theme
     * @param  boolean $frontend_only Only theme from frontend theme path
     * @return array
     **/
    public function info($name = null, $frontend_only = false)
    {
        $theme = is_null($name) ? $this->theme : $name;

        if ($frontend_only) {
            if ($file = $this->findThemeFile($theme, 'theme.info')) {
                return $this->parseThemeInfo($file);
            }
        } else {
            if (File::is($this->path.$theme.DS.'info.php')) {
                return require $this->path.$theme.DS.'info.php';
            } elseif (File::is($this->path.$theme.DS.'theme.info')) {
                return $this->parseThemeInfo($this->path.$theme.DS.'theme.info');
            }
        }

        throw new FileNotFoundException("info.php", 'theme '.$theme);
    }

    /**
     * Get the theme config from active theme's info file or given theme name
     *
     * @param  string  $name          Theme name, if you set this value is null
     *                                return config from active theme
     * @param  boolean $frontend_only Only theme from frontend theme path
     * @return array
     **/
    public function config($name = null, $frontend_only = false)
    {
        $theme = is_null($name) ? $this->theme : $name;

        if ($frontend_only) {
            if ($file = $this->findThemeFile($theme, 'config.php')) {
                return $file;
            }
        } else {
            if (File::is($this->path.$theme.DS.'config.php')) {
                return require $this->path.$theme.DS.'config.php';
            }
        }

        throw new FileNotFoundException("config.php", 'theme '.$theme);
    }

    /**
     * Get the theme options from active theme's options file or given theme name
     *
     * @param  string  $name          Theme name, if you set this value is null
     *                                return options from active theme
     * @param  boolean $frontend_only Only theme from frontend theme path
     * @return array|null
     **/
    public function option($name = null, $frontend_only = false)
    {
        $theme = is_null($name) ? $this->theme : $name;

        if ($frontend_only) {
            if ($file = $this->findThemeFile($theme, 'options.php')) {
                return $file;
            }
        } else {
            if (File::is($this->path.$theme.DS.'options.php')) {
                return require $this->path.$theme.DS.'options.php';
            }
        }

        return null;
    }


    /**
     * Find the Widgets from theme
     *
     * @param  string $name Theme name, if you set this value is null
     *                      return info from active theme
     * @return array
     **/
    public function findWidgets($name = null)
    {
        $theme = is_null($name) ? $this->theme : $name;

        $path = $this->findTheme($theme);

        if (!Dir::is($path.DS.'widgets')) {
            return array();
        }

        $all = Dir::get($path.DS.'widgets'.DS.'*', GLOB_ONLYDIR);

        return $all;
    }

    /**
     * Parse Theme Inof file
     *
     * @param  string $file file with full path
     * @return array
     **/
    protected function parseThemeInfo($file)
    {
        $info_parser = $this->app->info_parser;

        return $info_parser->parse($file);
    }

    /**
     * Get theme file from theme's root folder
     *
     * @param  string      $theme
     * @param  string      $filename
     * @return string|null
     **/
    protected function findThemeFile($theme, $filename)
    {
        $paths = $this->getThemeFolderPaths($theme);

        foreach ($paths as $path) {
            if (File::is($path) and File::is($path.$filename)) {
                return $path.$filename;
            }
        }

        return null;
    }

    /**
     * Get theme folder paths.
     * If you pass theme name, return path with theme name.
     *
     * @param  string|null $theme
     * @return array
     **/
    protected function getThemeFolderPaths($theme = null)
    {
        $private = Str::endIs(THEMES.$theme.DS, DS);
        $shared = Str::endIs(SHARED.'themes'.DS.$theme.DS, DS);

        return array($private, $shared);
    }

} // END class Theme
