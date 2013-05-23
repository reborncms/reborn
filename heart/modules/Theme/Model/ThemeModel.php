<?php

namespace Theme\Model;

class ThemeModel extends \Model
{

    /**
     * Get all themes from associated directories
     *
     * @return  array
    */
    public static function all()
    {
        $themes = array();
        $results = scandir(THEMES);

        foreach ($results as $result) 
        {
            if ($result === '.' or $result === '..') continue;
            
            if (is_dir(THEMES . '/' . $result)) 
            {
                $themes[] = $result;
            }
        }

        foreach ( $themes as $key => $value )
        {   
            $themeinfo[$value] = self::load_info($value);
        }

        return $themeinfo;
    }

    /**
     * Load in the theme.info file for the given (or active) theme.
     *
     * @param   string  $theme  Name of the theme (null for active)
     * @return  array   Theme info array
     */
    protected static function load_info($theme = null)
    {
        if ($theme === null)
        {
            $theme = $this->active;
        }

        if (is_array($theme))
        {
            $path = $theme['path'];
            $name = $theme['name'];
        }
        else
        {
            $path = THEMES.$theme;
            $name = $theme;
            $theme = array(
                'name' => $name,
                'path' => $path
            );
        }

        if ( ! $path)
        {
            throw new \ThemeException(sprintf('Could not find theme "%s".', $theme));
        }

        if(\File::is($path.DS.'info.php'))
        {
            $file = $path.DS.'info.php';
        }
        
        $info = require $file;
        
        $screenshot = is_file($path.DS.'screenshot.png') ? $path.DS.'screenshot.png' : '';
        $info['screenshot'] = str_replace(array(BASE, DS), array('', '/'), $screenshot);

        return $info;
    }

}
