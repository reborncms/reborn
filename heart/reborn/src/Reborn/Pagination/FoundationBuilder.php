<?php

namespace Reborn\Pagination;

use Reborn\Cores\Application;

/**
 * Foundation 5.x Style Pagination Builder Class
 *
 * @package Reborn\Pagination
 * @author MyanmarLinks Professional Web Development Team
 **/
class FoundationBuilder extends Builder
{
	/**
	 * Foundation pagination align center.
	 * http://foundation.zurb.com/docs/components/pagination.html
	 *
	 * @var boolean
	 **/
	protected $center = false;

	/**
	 * Make Foundation Pagination Builder instance.
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
	 * @param boolean $bool
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
	 * Get Foundation Pagination Wrapper
	 *
	 * @return string
	 **/
	protected function getWrapper()
	{
		$class = $this->center ? ' pagination-centered' : '';

		return '<div class="pagi-wrapper '.$class.'"><ul class="pagination">';
	}

	/**
	 * Get Foundation Pagination Previous Link
	 *
	 * @return string
	 **/
	protected function getPreviousLink()
	{
		$prev_link = $this->current - 1;

		$url = $this->url;
		$class = ($prev_link == 0) ? ' unavailable' : '';

		if ($prev_link > 1) {
			$url = $url.'page-'.$prev_link;
		}

		$link = '<li class="arrow'.$class.'">';
		$link .= '<a href="'.$url.'" class="'.$this->template['prev_link_class'].'">';
		$link .= $this->template['prev_link_text'].'</a>';
		$link .= '</li>';

		return $link;
	}

	/**
	 * Get Foundation Pagination Link with "li" tag.
	 *
	 * @param int $page
	 * @param string $url
	 * @return string
	 **/
	protected function getLink($page)
	{
		$class = '';

		if ($this->current == $page) {
			$class = ' class="current"';
		}

		$url = $this->url;

		if ($page > 1) {
			$url = $url.'page-'.$page;
		}

		$link = '<li'.$class.'>';
		$link .= '<a href="'.$url.'"'.$class.'>'.$page.'</a>';
		$link .= '</li>';

		return $link;
	}

	/**
	 * Get Foundation Separator Dot Block
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
	 * Get Foundation Pagination Next Link
	 *
	 * @return string
	 **/
	protected function getNextLink()
	{
		$next_link = $this->current + 1;

		$url = $this->url.'page-'.$next_link;

		$class = ($this->total_pages == $this->current) ? ' unavailable' : '';

		$link = '<li class="arrow'.$class.'">';
		$link .= '<a href="'.$url.'" class="'.$this->template['next_link_class'].'">';
		$link .= $this->template['next_link_text'].'</a>';
		$link .= '</li>';

		return $link;
	}

} // END class FoundationBuilder extends Builder
