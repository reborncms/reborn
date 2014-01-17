<?php

namespace Reborn\Pagination;

use Reborn\Http\Uri;
use Reborn\Form\Form;
use Reborn\Cores\Application;

/**
 * Default Pagination Builder Class
 *
 * @package Reborn\Pagination
 * @author MyanmarLinks Professional Web Development Team
 **/
class Builder implements BuilderInterface
{
	/**
	 * Application (IOC) Container instance
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * @var int Total number of items
	 **/
	protected $total_items;

	/**
	 * @var url Paginated url
	 **/
	protected $url;

	/**
	 * Query data array lists for http query string.
	 *
	 * @var array
	 **/
	protected $query = array();

	/**
	 * @var int Number of items to show on a page
	 **/
	public $items_per_page = 5;

	/**
	 * @var int Current page number
	 **/
	protected $current = 1;

	/**
	 * @var int Param name of Pagination. Default is 'page'
	 **/
	protected $param_name = 'page';

	/**
	 * @var int Number of Links Pages to show on pagination
	 **/
	protected $show_pages = 13;

	/**
	 * Adjacent value for pagination
	 *
	 * @var int
	 **/
	protected $adjacent = 2;

	/**
	 * @var int Number of Total Pagination Pages
	 **/
	protected $total_pages;

	/**
	 * Page Jump
	 *
	 * @var boolean
	 **/
	protected $page_jump = false;

	/**
	 * @var array Template for pagination
	 **/
	protected $template = array(
		'start_container' => '<div class="pagination">',
		'end_container' => '</div>',
		'active_class' => 'active',
		'prev_link_class' => 'pagi_prev',
		'next_link_class' => 'pagi_next',
		'prev_link_text' => '&laquo; Prev',
		'next_link_text' => 'Next &raquo;',
		'separator' => '&hellip;',
		'separator_class' => 'separator'
	);

	/**
	 * Default instance method for Pagination Builder
	 *
	 * @param \Reborn\Cores\Application $app
	 * @param array $options
	 * @return void
	 **/
	public function __construct(Application $app, array $options = array())
	{
		$this->app = $app;

		if (! empty($options) ) {
			$this->options($options);
		}

		if (is_null($this->url)) {
			$this->url = preg_replace('@(\/page-(\d+))@', '', Uri::current()).'/';
		} else {
			$this->url = Uri::create($this->url);
		}

		$params = $app->request->params;

		$page = isset($params[$this->param_name]) ? $params[$this->param_name] : null;

		if($page != null){
			$this->current = (int) str_replace('page-', '', $page);
		}
	}

	/**
	 * Set query data for url.
	 *
	 * @param array $query
	 * @param boolean $replace
	 * @return \Reborn\Pagination\Builder
	 **/
	public function query(array $query, $replace = false)
	{
		if ($replace) {
			$this->query = $query;
		} else {
			$this->query = array_merge($this->query, $query);
		}

		return $this;
	}

	/**
	 * Set options for pagination.
	 *
	 * @param array $options
	 * @return \Reborn\Pagination\Builder
	 **/
	public function options(array $options)
	{
		foreach($options as $key => $val)
		{
			switch ($key) {
				case 'template':
					$this->template = array_merge($this->template, $val);
					break;

				default:
					$this->{$key} = $val;
					break;
			}
		}

		$this->calculateTotalPages();

		return $this;
	}

	/**
	 * Check pagination number (page-*) is invalid.
	 *
	 * @return boolean
	 **/
	public function isInvalid()
	{
		if ($this->offset() == 0 and $this->total_items == 0) {
			return false;
		}

		if ($this->offset() >= $this->total_items) {
			return true;
		}

		return false;
	}

	/**
	 * Get item per page value.(limit)
	 *
	 * @return int
	 **/
	public function limit()
	{
		return (int) $this->items_per_page;
	}

	/**
	 * Get offset value
	 *
	 * @return int
	 **/
	public function offset()
	{
		return ($this->current * $this->items_per_page) - $this->items_per_page;
	}

	/**
	 * Get total number of pagination pages.
	 * This number is equal with last page number.
	 *
	 * @return int
	 **/
	public function getTotalPages()
	{
		return $this->total_pages;
	}

	/**
	 * Set Adjacent for pagination.
	 *
	 * @param int $adjacent
	 * @return \Reborn\Pagination\BuilderInterface
	 **/
	public function adjacent($adjacent)
	{
		$this->adjacent = (int) $adjacent;

		return $this;
	}

	/**
	 * Pagination Links
	 *
	 * //== Generate page links with separator(...) logic from
	 * 		PHP Pagination Class by David Carr - dave@daveismyname.com ==/
	 *
	 * @return string
	 **/
	public function render()
	{
		$total = $this->total_pages;

		$template = $this->template;

		// Maximum limit of pages to shwo in pagination.
		// If total pages is more than show_pages, need to make
		// (...) sliding style pagination. Minimum value of show_pages is 13
		$show_pages = ($this->show_pages > 12) ? $this->show_pages : 13;

		$adjacent = (int) floor($show_pages / $this->adjacent);

		// Return null on no pagi page
		if ($total < 2) {
			return '';
		}

		if ($total <= $show_pages) {

			$pagis = $this->getPaginationLinks(1, $total);

		} elseif ($this->current <= $adjacent) {
			// Style : PREV|1|2|3|4|current|6|7|8|9|...|12|13|NEXT

			$pagis = $this->getPaginationLinks(1, $adjacent + 2);

			$pagis .= $this->getSeparator();

			$pagis .= $this->getPaginationLinks($total - 1, $total);

		} elseif ($this->current >= ($total - $adjacent)) {
			// Style : PREV|1|2|...|6|7|current|9|10|11|12|13|NEXT

			$pagis = $this->getPaginationLinks(1, 2);

			$pagis .= $this->getSeparator();

			$pagis .= $this->getPaginationLinks($total - 8, $total);

		} else {
			// Style : PREV|1|2|...|4|5|6|current|8|9|...|11|12|13|NEXT

			$current = $this->current;

			$pagis = $this->getPaginationLinks(1, 2);

			$pagis .= $this->getSeparator();

			$pagis .= $this->getPaginationLinks($current - 3, $current + 3);

			$pagis .= $this->getSeparator();

			$pagis .= $this->getPaginationLinks($total - 1, $total);
		}

		// Add previous link
		$pagis = $this->getWrapper().$this->getPreviousLink().$pagis;

		// Add next link
		$pagis = $pagis.$this->getNextLink();

		$pagis .= '</ul>';
		$pagis .= $template['end_container'];

		//Go To
		if ($this->page_jump === true) {
			$pagis .= $this->pageJump();
		}

		return $pagis;
	}

	/**
	 * Collect options data for Extra Builder.
	 * [Bootstrap, Foundation]
	 *
	 * @return array
	 **/
	public function collectOptions()
	{
		return array(
			'url' => $this->url,
			'query' => $this->query,
			'current' => $this->current,
			'show_pages' => $this->show_pages,
			'param_name' => $this->param_name,
			'total_items' => $this->total_items,
			'items_per_page' => $this->items_per_page
		);
	}

	/**
	 * Use Pagination Style for Bootstrap 3
	 *
	 * @return \Reborn\Pagination\BootstrapBuilder
	 **/
	public function bootstrap()
	{
		$ins = new BootstrapBuilder($this->app, $this->collectOptions());

		return $ins;
	}

	/**
	 * Use Pagination Style for Foundation 5.x
	 *
	 * @return \Reborn\Pagination\FoundationBuilder
	 **/
	public function foundation()
	{
		$ins = new FoundationBuilder($this->app, $this->collectOptions());

		return $ins;
	}

	/**
	 * Use Pagination Style for Pure CSS 0.3.x
	 *
	 * @return \Reborn\Pagination\PureBuilder
	 **/
	public function purecss()
	{
		$ins = new PureBuilder($this->app, $this->collectOptions());

		return $ins;
	}

	/**
	 * Get Pager. (Previous ans Next only)
	 *
	 * @return string
	 **/
	public function pager()
	{
		$pager = '<ul class="pager">';
		$pager .= $this->getPreviousLink();
		$pager .= $this->getNextLink($this->total_pages);
		$pager .= '</ul>';

		return $pager;
	}

	/**
	 * Get pagination links "$from" -> "$to".
	 * example getPaginationLinks(1, 3)
	 *  - <a href="..">1</a>
	 *  - <a href="..">2</a>
	 *  - <a href="..">3</a>
	 *
	 * @param int $from
	 * @param int $to
	 * @return string
	 **/
	protected function getPaginationLinks($from, $to)
	{
		$lists = array();

		for ($page = $from; $page <= $to; $page++) {
			$lists[] = $this->getLink($page);
		}

		return implode('', $lists);
	}

	/**
	 * Page Jump Field
	 *
	 * @return string
	 **/
	protected function pageJump()
	{
		$JumpField = Form::start('','JumpForm');
		$JumpField .= Form::input('page_num', null, 'text',array('class' => 'pagi_jump', 'style' => 'width:30px;'));
		$JumpField .= Form::button('jumpTo','Go','button',array('onclick' => 'JumpToPage()'));
		$JumpField .= Form::end();
		$JumpField .= "<script type='text/javascript'>
			function JumpToPage()
			{
				var pagi_num = document.forms['JumpForm'].page_num.value;
				var goLink = '".$this->url."page-' + pagi_num;
				if(pagi_num > ".$this->total_pages.") {
					alert('Invalid page number');
				} else {
					window.open(goLink ,'_self');
				}
			}
		</script>";

		return $JumpField;
	}

	/**
	 * Calculate Total Links.
	 *
	 * @return void
	 **/
	protected function calculateTotalPages()
	{
		$total_pagi = ceil($this->total_items / $this->items_per_page);

		if ($total_pagi >= 1) {
			$this->total_pages = (int) $total_pagi;
		}
	}

/**
 * =====================================================
 * Default Pagination Render Methods
 * =====================================================
 */

	/**
	 * Get Default Pagination Wrapper
	 *
	 * @return string
	 **/
	protected function getWrapper()
	{
		return $this->template['start_container'].'<ul>';
	}

	/**
	 * Get Default Pagination Previous Link
	 *
	 * @param int $prev_link
	 * @return string
	 **/
	protected function getPreviousLink()
	{
		if ($this->current < 2) {
			return '';
		}

		$prev_link = $this->current - 1;

		$page = null;
		if ($prev_link > 1) {
			$page = 'page-'.$prev_link;
		}

		$url = $this->buildUrl($page);

		$link = '<li><a href="'.$url.'" class="'.$this->template['prev_link_class'].'">';
		$link .= $this->template['prev_link_text'].'</a></li>';

		return $link;
	}

	/**
	 * Get Default Pagination Link with "li" tag.
	 *
	 * @param int $page
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

		$link = '<li><a href="'.$url.'"'.$class.'>'.$page.'</a></li>';

		return $link;
	}

	/**
	 * Get Default Separator Block
	 *
	 * @return string
	 **/
	protected function getSeparator()
	{
		$sep = '<li class="'.$this->template['separator_class'].'">';
		$sep .= $this->template['separator'];
		$sep .= '</li>';

		return $sep;
	}

	/**
	 * Get Default Pagination Next Link
	 *
	 * @return string
	 **/
	protected function getNextLink()
	{
		if ($this->total_pages == $this->current) {
			return '';
		}

		$next_link = $this->current + 1;

		$url = $this->buildUrl('page-'.$next_link);

		$link = '<li><a href="'.$url.'" class="'.$this->template['next_link_class'].'">';
		$link .= $this->template['next_link_text'].'</a></li>';

		return $link;
	}

	/**
	 * Get url string.
	 *
	 * @return string
	 **/
	protected function buildUrl($with = null)
	{
		$url = $this->url;

		if (! is_null($with) ) {
			$url = $this->url.$with;
		}

		if ( empty($this->query) ) {
			return $url;
		}

		$query = http_build_query($this->query);

		return $url.'?'.$query;
	}

	/**
	 * Dynamically render the Pagination with PHP Magic Method.
	 *
	 * @return string
	 **/
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Get pagination data with array fromat
	 * <code>
	 * 	// Format
	 * 	[
	 * 		'current_page'	=> 1,
	 * 		'total_pages'	=> 14,
	 * 		'total_items'	=> 68,
	 * 		'item_per_page'	=> 5,
	 * 		'url'			=> 'http://example.com/blog'
	 * 	]
	 * </code>
	 *
	 * @return array
	 **/
	public function toArray()
	{
		return array(
			'current_page' => $this->current,
			'total_pages' => $this->total_pages,
			'total_items' => $this->total_items,
			'item_per_page' => $this->items_per_page,
			'url' => $this->buildUrl(),
		);
	}

} // END class Builder
