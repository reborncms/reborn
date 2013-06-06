<?php

namespace Reborn\MVC\View;

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
     * variable for the theme name
     *
     * @var string
     **/
    protected $theme;

    /**
     * variable for the theme fodler path
     *
     * @var string
     **/
    protected $path;

    /**
     * Default constructor method
     *
     * @param string $name Theme name
     * @param string $path Theme path
     * @return Object($this)
     **/
    public function __construct($name, $path)
    {
        $this->theme = $name;

        $this->path = $path;

        return $this;
    }

    /**
     * Set the theme path
     *
     * @param string $path Theme path
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
        if(! \Dir::is($this->path.$this->theme))
        {
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
        $all = glob($this->path.$this->theme.DS.'views'.DS.'layout'.DS.'*.html');
        $layouts = array();

        foreach ($all as $s) {
            $layouts[] = str_replace($this->path.$this->theme.DS.'views'.DS.'layout'.DS, '', $s);
        }

        return $layouts;
    }

    /**
     * Check the given layout name is exists in the active theme's layout folder.
     *
     * @param string $name Layout name, no need file extension
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
     * @param string $name File name
     * @param string $folder Default is partial
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
     * @param string $name Theme name, if you set this value is null
     *                      return info from active theme
     * @param boolean $frontend_only Only theme from frontend theme path
     * @return array
     **/
    public function info($name = null, $frontend_only = false)
    {
        $theme = is_null($name) ? $this->theme : $name;

        if ($frontend_only) {
            if(File::is(THEME.$theme.DS.'info.php')) {
                return require THEME.$theme.DS.'info.php';
            }
        } else {
            if(File::is($this->path.$theme.DS.'info.php')) {
                return require $this->path.$theme.DS.'info.php';
            }
        }

        throw new FileNotFoundException("info.php", 'theme '.$theme);
    }


    /**
     * Find the Widgets from theme
     *
     * @param string $name Theme name, if you set this value is null
     *                      return info from active theme
     * @return array
     **/
    public function findWidgets($name = null)
    {
        $theme = is_null($name) ? $this->theme : $name;
        $dir = $this->path.$theme.DS.'widgets';

        if(!Dir::is($dir)) {
            return array();
        }

        $all = Dir::get($dir.DS.'*', GLOB_ONLYDIR);

        return $all;
    }

} // END class Theme
