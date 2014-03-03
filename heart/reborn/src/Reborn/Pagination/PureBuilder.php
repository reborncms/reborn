<?php

namespace Reborn\Pagination;

use Reborn\Cores\Application;

/**
 * Pure CSS 0.3.x Style Pagination Builder Class
 *
 * @package Reborn\Pagination
 * @author MyanmarLinks Professional Web Development Team
 **/
class PureBuilder extends Builder
{
    /**
     * Make Foundation Pagination Builder instance.
     * http://purecss.io/menus/#paginators
     *
     * @return void
     **/
    public function __construct(Application $app, $options)
    {
        $this->app = $app;

        $this->options($options);
    }

    /**
     * Set pagination center or not.
     *
     * @param  boolean                              $bool
     * @return \Reborn\Pagination\FoundationBuilder
     **/
    public function center($bool = true)
    {
        $this->center = (bool) $bool;

        return $this;
    }

/**
 * =====================================================
 * Foundation 5.x Pagination Render Methods
 * =====================================================
 */

    /**
     * Get Pure CSS Pagination Wrapper
     *
     * @return string
     **/
    protected function getWrapper()
    {
        return '<div class="pagi-wrapper"><ul class="pure-paginator">';
    }

    /**
     * Get Pure CSS Pagination Previous Link
     *
     * @return string
     **/
    protected function getPreviousLink()
    {
        $prev_link = $this->current - 1;

        $page = null;
        if ($prev_link > 1) {
            $page = 'page-'.$prev_link;
        }

        $url = $this->buildUrl($page);

        $link = '<li>';
        $link .= '<a href="'.$url.'" class="pure-button prev">';
        $link .= $this->template['prev_link_text'].'</a>';
        $link .= '</li>';

        return $link;
    }

    /**
     * Get Foundation Pagination Link with "li" tag.
     *
     * @param  int    $page
     * @param  string $url
     * @return string
     **/
    protected function getLink($page)
    {
        $class = ' class="pure-button"';

        if ($this->current == $page) {
            $class = ' class="pure-button pure-button-active"';
        }

        $page_no = null;
        if ($page > 1) {
            $page_no = 'page-'.$page;
        }

        $url = $this->buildUrl($page_no);

        $link = '<li>';
        $link .= '<a href="'.$url.'"'.$class.'>'.$page.'</a>';
        $link .= '</li>';

        return $link;
    }

    /**
     * Get Separator Dot Block.
     * Pure CSS doesn't have class name for this
     *
     * @return string
     **/
    protected function getSeparator()
    {
        $sep = '<li class="unavailable">';
        $sep .= '<span>'.$this->template['separator'].'</span>';
        $sep .= '</li>';

        return $sep;
    }

    /**
     * Get Pure CSS Pagination Next Link
     *
     * @return string
     **/
    protected function getNextLink()
    {
        $next_link = $this->current + 1;

        $page = null;
        if ($next_link > 1) {
            $page = 'page-'.$next_link;
        }

        $url = $this->buildUrl($page);

        $link = '<li>';
        $link .= '<a href="'.$url.'" class="pure-button next">';
        $link .= $this->template['next_link_text'].'</a>';
        $link .= '</li>';

        return $link;
    }

} // END class PureBuilder extends Builder
