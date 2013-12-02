<?php

namespace Theme\Model;

class Theme
{
    /**
     * Get all themes from associated directories
     *
     * @return  array
    */
    public static function all()
    {
        $handler = \Facade::getApplication()->theme;
        $results = $handler->all(true);            

        $themeinfo = array();
        
        foreach ($results as $theme) {
            $themeinfo[] = self::loadInfo($theme, $handler);            
        }

        return $themeinfo;
    }

    /**
     * Load in the theme.info file for the given (or active) theme.
     *
     * @param   string  $theme  Name of the theme (null for active)
     * @return  array   Theme info array
     */
    protected static function loadInfo($theme, $handler)
    {   
        
        $screenshot = $handler->findTheme($theme).'screenshot.png';

        $info = $handler->info($theme, true);

        if (is_file($screenshot)) {
            $info['screenshot'] = str_replace(array(BASE, DS), array('', '/'), $screenshot);            
        } else {
            $info['screenshot'] = 'heart/modules/theme/assets/img/screenshot.png';
        }

        return $info;
    }

}
