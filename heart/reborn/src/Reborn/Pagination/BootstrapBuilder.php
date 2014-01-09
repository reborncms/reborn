<?php

namespace Reborn\Pagination;

use Reborn\Cores\Application;

/**
 * Bootstrap 3 Style Pagination Builder Class
 *
 * @package Reborn\Pagination
 * @author MyanmarLinks Professional Web Development Team
 **/
class BootstrapBuilder extends Builder
{
	/**
	 * Bootstrap pagination size variable.
	 * Bootstrap supported "Fancy larger or smaller pagination".
	 *
	 * @var string
	 **/
	protected $size;

	/**
	 * Make Bootstrap Pagination Builder instance.
	 *
	 * @return void
	 **/
	public function __construct(Application $app, $options)
	{
		$this->app = $app;

		$this->options($options);
	}

	/**
	 * Set "large" or "small" for additional pagination sizes.
	 *
	 * @return \Reborn\Pagination\BootstrapBuilder
	 **/
	public function sizing($size)
	{
		if (in_array($size, array('large', 'small'))) {
			$this->{$size}();
		}

		return $this;
	}

	/**
	 * Set large pagination style for bootstrap.
	 * See detail at Bootstrap Pagination Documentation.
	 *
	 * @return \Reborn\Pagination\BootstrapBuilder
	 **/
	public function large()
	{
		$this->size = 'pagination-lg';

		return $this;
	}

	/**
	 * Set small pagination style for bootstrap.
	 * See detail at Bootstrap Pagination Documentation.
	 *
	 * @return \Reborn\Pagination\BootstrapBuilder
	 **/
	public function small()
	{
		$this->size = 'pagination-sm';

		return $this;
	}

/**
 * =====================================================
 * Bootstrap 3 Pagination Render Methods
 * =====================================================
 */

	/**
	 * Get Bootstrap Pagination Wrapper
	 *
	 * @return string
	 **/
	protected function getWrapper()
	{
		$class = is_null($this->size) ? '' : ' '.$this->size;

		return '<div class="pagi-wrapper clearfix"><ul class="pagination'.$class.'">';
	}

	/**
	 * Get Bootstrap Pagination Previous Link
	 *
	 * @return string
	 **/
	protected function getPreviousLink()
	{
		$prev_link = $this->current - 1;

		$class = ($prev_link == 0) ? ' class="disabled"' : '';

		$page = null;
		if ($prev_link > 1) {
			$page = 'page-'.$prev_link;
		}

		$url = $this->buildUrl($page);

		$link = '<li'.$class.'>';
		$link .= '<a href="'.$url.'" class="'.$this->template['prev_link_class'].'">';
		$link .= $this->template['prev_link_text'].'</a>';
		$link .= '</li>';

		return $link;
	}

	/**
	 * Get Bootstrap Pagination Link with "li" tag.
	 *
	 * @param int $page
	 * @param string $url
	 * @return string
	 **/
	protected function getLink($page)
	{
		$class = '';

		if ($this->current == $page) {
			$class = ' class="'.$this->template['active_class'].'"';
		}

		$page_no = null;
		if ($page > 1) {
			$page_no = 'page-'.$page;
		}

		$url = $this->buildUrl($page_no);

		$link = '<li'.$class.'>';
		$link .= '<a href="'.$url.'"'.$class.'>'.$page.'</a>';
		$link .= '</li>';

		return $link;
	}

	/**
	 * Get Default Separator Block
	 *
	 * @return string
	 **/
	protected function getSeparator()
	{
		$sep = '<li class="disabled">';
		$sep .= '<span>'.$this->template['separator'].'</span>';
		$sep .= '</li>';

		return $sep;
	}

	/**
	 * Get Bootstrap Pagination Next Link
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

		$class = ($this->total_pages == $this->current) ? ' class="disabled"' : '';

		$link = '<li'.$class.'>';
		$link .= '<a href="'.$url.'" class="'.$this->template['next_link_class'].'">';
		$link .= $this->template['next_link_text'].'</a>';
		$link .= '</li>';

		return $link;
	}

} // END class BootstrapBuilder extends Builder
