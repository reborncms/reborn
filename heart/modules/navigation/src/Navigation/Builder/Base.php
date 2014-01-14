<?php

namespace Navigation\Builder;

use Reborn\Cores\Application;
use Reborn\Cores\Setting;
use Navigation\Lib\Helper;
use Navigation\Model\Navigation as Group;
use Navigation\Model\NavigationLinks as Links;

/**
 * Base Navigation Builder Class
 *
 * @package Navigation
 * @author MyanmarLinks Professional Web Development Team
 **/
class Base
{
	/**
	 * Reborn Application (IOC) Container instance
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * Current request url
	 *
	 * @var string
	 **/
	protected $current;

	/**
	 * Home page variable
	 *
	 * @var string
	 **/
	protected $home;

	/**
	 * Default instance method.
	 *
	 * @param \Reborn\Cores\Application $app
	 * @return void
	 **/
	public function __construct(Application $app, $group)
	{
		$this->current = rtrim($app->request->requestUrl(), '/');

		$this->home = Setting::get('home_page');

		$this->group = $group;

		$this->app = $app;
	}

	/**
	 * Render navigation ui.
	 *
	 * @return string
	 **/
	public function render()
	{
		$tree = $this->getResults($this->group);

		$output = $this->getMainUlTag();

		foreach ($tree as $t) {
			$url = $this->prepareUrl($t);

			$class = $this->getClass($url, !empty($t['child']));

			$id = slug($t['title']);

			$output .= "\t".'<li id="'.$id.'" class="'.$class.'">';
			$output .= '<a href="'.$url.'" class="'.$t['class'].'" >';
			$output .= $t['title'];
			$output .= '</a>';
			if ($t['child']) {
				$output .= $this->renderChild($t);
			}
			$output .= "\t</li>\n";
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Render Child "li" Elements.
	 *
	 * @param array $links
	 * @param integer $level
	 * @return string
	 **/
	protected function renderChild($links, $level = 1)
	{
		$output = '';

		if ($links['child']):
			$tab = str_repeat("\t", $level);
			$inner = $tab."\t";
			$output .= "\n" . $tab . $this->getSubMenuUl($level) . "\n";
			foreach($links['child'] as $link) :
				$url = $this->prepareUrl($link);
				$class = $this->getClass($url, !empty($link['child']), $level);

				$output .= $inner.'<li id="'. $link['title'].'" class="'.$class.'">';
				$output .= '<a href="'.$url.'" class="'.$link['class'].'" >';
				$output .= $link['title'];
				$output .= '</a>';

				if (! empty($link['child'])) :
						$output .=	$this->renderChild($link, $level +1);
					$output .= $inner."</li>\n";
				else :
					$output .= "</li>\n";
				endif;

			endforeach;

			$output .= "$tab</ul>\n";

		endif;

		return $output;
	}

	/**
	 * Prepare link url.
	 *
	 * @param array $data
	 * @return string
	 **/
	protected function prepareUrl($data)
	{
		if ('url' === $data['link_type']) {
			$url = $data['url'];
		} else {
			if ($data['url'] === $this->home) {
				$url = url();
			} else {
				$url = url($data['url']);
			}
		}

		return $url;
	}

	/**
	 * Get class name for "li" tag.
	 *
	 * @param string $url
	 * @param boolean $has_child
	 * @return string
	 **/
	protected function getClass($url, $has_child = false)
	{
		$class = '';

		// Active class name
		$active = $this->getActiveClass();

		if ($this->current === rtrim($url,'/')) {
				$class = $active;
		} elseif (rtrim(url($this->home), '/') === rtrim($url,'/')) {
			if (rtrim(url(), '/') == $current) {
				$class = $active;
			}
		}

		if ($has_child) {
			$class .= ' has-submenu';
		}

		return ltrim($class, ' ');
	}

	/**
	 * Get active class name.
	 *
	 * @return string
	 **/
	protected function getActiveClass()
	{
		return 'active';
	}

	/**
	 * Get Main "ur" tag.
	 *
	 * @return string
	 **/
	protected function getMainUlTag()
	{
		return '<ul class="nav" id="'.$this->group.'-menu">';
	}

	/**
	 * Get submenu ul tag
	 *
	 * @param integer $level
	 * @return string
	 **/
	protected function getSubMenuUl($level)
	{
		return '<ul class="level-'.$level.'">';
	}

	/**
	 * Get link result from cache or database
	 *
	 * @param string $group
	 * @return array
	 **/
	protected function getResults($group)
	{
		$nav = Group::where('slug', '=', $group)->first();

		$cache_key = 'Navigation::navigation_'.$group.'_trees';

		$id = $nav->id;

		$tree = $this->app['cache']->solve($cache_key, function() use($id)
				{
					$obj = Links::where('navigation_id', '=', $id)
							->orderBy('link_order', 'asc')
							->get()
							->toArray();

					return Helper::getNavTree($obj);
				});

		return $tree;
	}

} // END class Base
