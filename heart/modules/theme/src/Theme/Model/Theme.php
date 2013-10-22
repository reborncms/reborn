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
        $themes = array();
        $results = scandir(THEMES);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;

            if (is_dir(THEMES . '/' . $result)) {
                $themes[] = $result;
            }
        }

        foreach ( $themes as $key => $value ) {
            $themeinfo[$value] = self::loadInfo($value);
        }

        return $themeinfo;
    }

    /**
     * Load in the theme.info file for the given (or active) theme.
     *
     * @param   string  $theme  Name of the theme (null for active)
     * @return  array   Theme info array
     */
    protected static function loadInfo($theme = null)
    {
        $class = \Facade::getApplication()->theme;

        if ($theme === null) $theme = $this->active;

        $screenshot = THEMES.$theme.DS.'screenshot.png';

        $info = $class->info($theme, true);

        if (is_file($screenshot)) {
            $info['screenshot'] = str_replace(array(BASE, DS), array('', '/'), $screenshot);            
        } else {
            $info['screenshot'] = 'heart/modules/theme/assets/img/screenshot.png';
        }

        return $info;
    }

}
