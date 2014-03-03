<?php

namespace Reborn\Asset;

class AssetFinder
{
    protected $theme = null;

    protected $asset = 'assets';

    /**
     * Get active theme's path
     *
     * @return string
     **/
    public function getThemePath()
    {
        if (is_null($this->theme)) {
            $this->theme = \Reborn\Cores\Facade::getApplication()->theme;
        }

        return $this->theme->getThemePath();
    }

    /**
     * Get css asset file path from module or theme.
     *
     * @param  string      $file
     * @param  string|null $module
     * @return string
     **/
    public function css($file, $module = null)
    {
        return $this->getFilePath($file, $module);
    }

    /**
     * Get less asset file path from module or theme.
     *
     * @param  string      $file
     * @param  string|null $module
     * @return string
     **/
    public function less($file, $module = null)
    {
        return $this->getFilePath($file, $module, 'less');
    }

    /**
     * Get javascript asset file path from module or theme.
     *
     * @param  string      $file
     * @param  string|null $module
     * @return string
     **/
    public function js($file, $module = null)
    {
        return $this->getFilePath($file, $module, 'js');
    }

    /**
     * Get image asset file path from module or theme.
     *
     * @param  string      $file
     * @param  string|null $module
     * @return string
     **/
    public function img($file, $module = null)
    {
        return $this->getFilePath($file, $module, 'img');
    }

    /**
     * Get asset folder path
     *
     * @param  string      $type
     * @param  string|null $module
     * @return string
     * @author Nyan Lynn Htut
     **/
    public function path($type = null, $module = null)
    {
        if (is_null($module)) {
            $path = $this->getThemePath();
        } else {
            $path = \Module::get($module, 'path').DS;
        }

        $path = is_null($type) ? $path.$this->asset.DS : $path.$this->asset.DS.$type.DS;

        return $path;
    }

    /**
     * Get asset file path
     *
     * @param  string      $file
     * @param  string|null $module
     * @param  string      $type
     * @return string|null
     **/
    protected function getFilePath($file, $module, $type = 'css')
    {
        $file = str_replace(array('/', '\\'), DS, $file);

        if (is_file($f = $this->path($type, $module).$file)) {
            return str_replace(BASE, '', $f);
        }

        return null;
    }
}
