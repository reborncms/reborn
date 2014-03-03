<?php

namespace Navigation\Builder;

/**
 * Foundation CSS Navigation Builder Class
 *
 * @package Navigation
 * @author MyanmarLinks Professional Web Development Team
 **/
class Foundation extends Base
{

    /**
     * Get class name for "li" tag.
     *
     * @param  string  $url
     * @param  boolean $has_child
     * @return string
     **/
    protected function getClass($url, $has_child = false)
    {
        $class = parent::getClass($url, $has_child);

        if ($has_child) {
            $class .= ' has-dropdown not-click';
        }

        return ltrim($class, ' ');
    }

    /**
     * Get submenu ul tag
     *
     * @param  integer $level
     * @return string
     **/
    protected function getSubMenuUl($level)
    {
        return '<ul class="dropdown level-'.$level.'">';
    }

} // END class Foundation
