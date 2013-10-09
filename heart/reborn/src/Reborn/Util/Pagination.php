<?php

namespace Reborn\Util;

use Reborn\Http\Uri;
use Reborn\Form\Form;

/**
 * Pagination class for Reborn CMS
 *
 * @package Reborn\Pagination
 * @author Reborn CMS Development Team
 **/
class Pagination
{

	/**
	 * @var int Total number of items
	 **/
	protected static $total_items;

	/**
	 * @var url Paginated url
	 **/
	protected static $url;

	/**
	 * @var int Number of items to show on a page
	 **/
	public static $items_per_page = 5;

	/**
	 * @var int Current Page
	 **/
	protected static $current = 1;

	/**
	 * @var int Param name of Pagination. Default is 'page'
	 **/
	protected static $param_name = 'page';

	/**
	 * @var int Number of Links to show on pagination
	 **/
	protected static $show_links = 10;

	/**
	 * Show number pagination
	 *
	 * @var boolean
	 **/
	protected static $numbers_pagi = true;

	/**
	 * @var int Number of Pagination Links
	 **/
	protected static $num_links;

	/**
	 * Page Jump
	 *
	 * @var boolean
	 **/
	protected static $page_jump = false;

	/**
	 * @var array Template for pagination
	 **/
	protected static $template = array(
		'start_container' => '<div class="pagination">',
		'end_container' => '</div>',
		'start_page_link' => '<li>',
		'end_page_link' => '</li>',
		'active_class' => 'active',
		'pre_link_class' => 'pagi_pre',
		'next_link_class' => 'pagi_next',
		'pre_link_text' => '&laquo; Prev',
		'next_link_text' => 'Next &raquo;',
		'separator' => '...',
		'separator_class' => 'separator'
	);

	/**
	 * Create Pagination
	 *
	 * @return void
	 **/
	public static function create($options = array())
	{
		foreach($options as $key => $val)
		{
			switch ($key) {
				case 'template':
					static::$template = array_merge(static::$template, $val);
					break;

				default:
					static::${$key} = $val;
					break;
			}
		}

		if (is_null(static::$url)) {
			static::$url = preg_replace('@(\/page-(\d+))@', '', \Uri::current()).'/';
		} else {
			static::$url = \Uri::create(static::$url);
		}

		$params = \Facade::getApplication()->request->params;

		$page = isset($params[static::$param_name]) ? $params[static::$param_name] : null;

		if($page != null){
			static::$current = (int) str_replace('page-', '', $page);
		}

		$total_pagi = ceil(static::$total_items / static::$items_per_page);

		if ($total_pagi > 1) {
			static::$num_links = $total_pagi;
			return static::links($total_pagi);
		}
	}

	/**
	 * Check pagination number (page-*) is invalid.
	 *
	 * @return boolean
	 **/
	public static function isInvalid()
	{
		if (self::offset() == 0 and static::$total_items == 0) {
			return false;
		}

		if (self::offset() >= static::$total_items) {
			return true;
		}

		return false;
	}

	/**
	 * Limit item
	 *
	 * @return int
	 **/
	public static function limit()
	{
		return static::$items_per_page;
	}

	/**
	 * offset item
	 *
	 * @return int
	 **/
	public static function offset()
	{
		$fs = static::$current * static::$items_per_page;
		$ss = $fs - static::$items_per_page;
		return $ss;
	}

	/**
	 * Regenerate separator open tag
	 *
	 * @return string
	 **/
	protected static function sepOpen()
	{
		$sepTag = str_replace('>', ' class="'.static::$template['separator_class'].'">',static::$template['start_page_link']);
		return $sepTag;
	}

	/**
	 * Page Jump Field
	 *
	 * @return string
	 **/
	protected static function pageJump()
	{
		$JumpField = Form::start('','JumpForm');
		$JumpField .= Form::input('page_num', null, 'text',array('class' => 'pagi_jump', 'style' => 'width:30px;'));
		$JumpField .= Form::button('jumpTo','Go','button',array('onclick' => 'JumpToPage()'));
		$JumpField .= Form::end();
		$JumpField .= "<script type='text/javascript'>
			function JumpToPage()
			{
				var pagi_num = document.forms['JumpForm'].page_num.value;
				var goLink = '".static::$url."page-' + pagi_num;
				if(pagi_num > ".static::$num_links.") {
					alert('Invalid page number');
				} else {
					window.open(goLink ,'_self');
				}
			}
		</script>";
		return $JumpField;
	}


	/**
	 * Pagination Links
	 *
	 * //== Generate page links with separator(...) logic from
	 * 		PHP Pagination Class by David Carr - dave@daveismyname.com ==/
	 *
	 * @return string
	 **/
	protected static function links($num_links)
	{
		$pagi_links = static::$template['start_container'];
		$pagi_links .= '<ul>';
		$prev_link = static::$current - 1;
		$next_link = static::$current + 1;
		//Previous link
		if ($prev_link > 1) {
			$prev_url = static::$url.'page-'.$prev_link;
		} else {
			$prev_url = static::$url;
		}

		if (static::$current > 1) {
			$pagi_links .= static::$template['start_page_link'];
			$pagi_links .= '<a href="'.$prev_url.'" class="'.static::$template['pre_link_class'].'">'.static::$template['pre_link_text'].'</a>';
			$pagi_links .= static::$template['end_page_link'];
		}

		if (static::$numbers_pagi == true) {

			if ($num_links > static::$show_links + 3) {
				if (static::$show_links < 10) {
					$adj = 1;
				} else if (static::$show_links  > 15) {
					$adj = 3;
				} else {
					$adj = 2;
				}

				$total_adj = $adj * 2;
				$b_last = $num_links - 1;

				if (static::$current < static::$show_links - ($total_adj + 1) ) {

					$first_part = static::$show_links - 3;
					$last_part_start = $num_links - 2;

					for($p = 1; $p <= $first_part; $p++) {

						if ($p > 1) {
							$url = static::$url.'page-'.$p;
						} else {
							$url = static::$url;
						}

						$active = ($p == static::$current) ? " class='".static::$template['active_class']."'" : "";
						$pagi_links .= static::$template['start_page_link'];
						$pagi_links .= '<a href="'.$url.'"'.$active.'>'.$p.'</a>';
						$pagi_links .= static::$template['end_page_link'];
					}

					$pagi_links .= static::sepOpen();
					$pagi_links .= static::$template['separator'];
					$pagi_links .= static::$template['end_page_link'];

					for($p = $last_part_start; $p <= $num_links; $p++) {
						$active = ($p == static::$current) ? " class='".static::$template['active_class']."'" : "";
						$pagi_links .= static::$template['start_page_link'];
						$pagi_links .= '<a href="'.static::$url.'page-'.$p.'"'.$active.'>'.$p.'</a>';
						$pagi_links .= static::$template['end_page_link'];
					}

				}
				elseif ($num_links - $total_adj > static::$current && static::$current > $total_adj) {

				 	$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.static::$url.'1">1</a>';
					$pagi_links .= static::$template['end_page_link'];

					$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.static::$url.'page-'.'2">2</a>';
					$pagi_links .= static::$template['end_page_link'];

					if (static::$current - $adj != 3) {
						$pagi_links .= static::sepOpen();
						$pagi_links .= static::$template['separator'];
						$pagi_links .= static::$template['end_page_link'];
					}

					for($p = static::$current - $adj; $p <= static::$current + $adj; $p++) {
						$active = ($p == static::$current) ? " class='".static::$template['active_class']."'" : "";
						$pagi_links .= static::$template['start_page_link'];
						$pagi_links .= '<a href="'.static::$url.'page-'.$p.'"'.$active.'>'.$p.'</a>';
						$pagi_links .= static::$template['end_page_link'];
					}

					$pagi_links .= static::sepOpen();
					$pagi_links .= static::$template['separator'];
					$pagi_links .= static::$template['end_page_link'];

					$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.static::$url.'page-'.$b_last.'">'.$b_last.'</a>';
					$pagi_links .= static::$template['end_page_link'];

					$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.static::$url.'page-'.$num_links.'">'.$num_links.'</a>';
					$pagi_links .= static::$template['end_page_link'];

				} else {

					$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.static::$url.'1">1</a>';
					$pagi_links .= static::$template['end_page_link'];

					$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.static::$url.'page-'.'2">2</a>';
					$pagi_links .= static::$template['end_page_link'];

					$pagi_links .= static::sepOpen();
					$pagi_links .= static::$template['separator'];
					$pagi_links .= static::$template['end_page_link'];

					for($p = $num_links - (2 + $total_adj); $p <= $num_links; $p++) {

						$active = ($p == static::$current) ? " class='".static::$template['active_class']."'" : "";
						$pagi_links .= static::$template['start_page_link'];
						$pagi_links .= '<a href="'.static::$url.'page-'.$p.'"'.$active.'>'.$p.'</a>';
						$pagi_links .= static::$template['end_page_link'];
					}
				}
			}
			else {
				for($p = 1; $p <= $num_links; $p++) {

					if ($p > 1) {
						$url = static::$url.'page-'.$p;
					} else {
						$url = static::$url;
					}

					$active = ($p == static::$current) ? " class='".static::$template['active_class']."'" : "";
					$pagi_links .= static::$template['start_page_link'];
					$pagi_links .= '<a href="'.$url.'"'.$active.'>'.$p.'</a>';
					$pagi_links .= static::$template['end_page_link'];
				}
			}
		}

		//next link
		if (static::$current != $num_links) {
			$pagi_links .= static::$template['start_page_link'];
			$pagi_links .= '<a href="'.static::$url.'page-'.$next_link.'" class="'.static::$template['next_link_class'].'">'.static::$template['next_link_text'].'</a>';
			$pagi_links .= static::$template['end_page_link'];
		}
		$pagi_links .= '</ul>';
		$pagi_links .= static::$template['end_container'];

		//Go To
		if (static::$page_jump == true) {
			$pagi_links .= static::pageJump();
		}

		return $pagi_links;

	}

}
