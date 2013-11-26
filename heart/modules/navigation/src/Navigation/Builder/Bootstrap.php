<?php

namespace Navigation\Builder;

/**
 * Bootstrap CSS Navigation Builder Class
 *
 * @package Navigation
 * @author MyanmarLinks Professional Web Development Team
 **/
class Bootstrap extends Base
{

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

			if ($t['child']) {
				$output .= $this->renderChild($t, 1, $url);
			} else {
				$output .= '<a href="'.$url.'" class="'.$t['class'].'" >';
				$output .= $t['title'];
				$output .= '</a>';
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
	protected function renderChild($links, $level = 1, $url = null)
	{
		$tab = str_repeat("\t", $level);
		$inner = $tab."\t";

		if ($level === 1) {
			$base = $this->prepareUrl($links);
			$output = '<a href="#"  data-toggle="dropdown" class="dropdown-toggle '.$links['class'].'">';
			$output .= $links['title'];
			$output .= '<b class="caret"></b></a>';

			$output .= "\n" . $tab . $this->getSubMenuUl($level) . "\n";
			// Replace Main Nav Link with divider
			$output .= '<li>';
			$output .= '<a href="'.$base.'" class="'.$links['class'].'" >';
			$output .= $links['title'];
			$output .= '</a>';
			$output .='</li><li class="divider"></li>';
		} else {
			$output = "\n" . $tab . $this->getSubMenuUl($level) . "\n";
		}

		foreach($links['child'] as $link) :
				$url = $this->prepareUrl($link);
				$class = $this->getClass($url, !empty($link['child']), $level);

				$output .= $inner.'<li id="'. slug($link['title']).'" class="'.$class.'">';
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

		return $output;
	}

	/**
	 * Get class name for "li" tag.
	 *
	 * @param string $url
	 * @param boolean $has_child
	 * @return string
	 **/
	protected function getClass($url, $has_child = false, $level = 0)
	{
		$class = parent::getClass($url, $has_child);

		if ($has_child) {
			if ($level > 0) {
				$class .= ' dropdown-submenu';
			} else {
				$class .= ' drop-down';
			}
		}

		return ltrim($class, ' ');
	}

	/**
	 * Get Main "ur" tag.
	 *
	 * @return string
	 **/
	protected function getMainUlTag()
	{
		return '<ul class="nav navbar-nav" id="'.$this->group.'">';
	}

	/**
	 * Get submenu ul tag
	 *
	 * @param integer $level
	 * @return string
	 **/
	protected function getSubMenuUl($level)
	{
		return '<ul class="dropdown-menu level-'.$level.'">';
	}

} // END class Bootstrap
