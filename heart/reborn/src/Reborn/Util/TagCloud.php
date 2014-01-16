<?php

namespace Reborn\Util;

/**
 * Manage Tags as Cloud Format (Tag Cloud Widget Helper Class)
 *
 * @package Reborn\Util
 * @author Myanmar Links Web Development Team
 **/
class TagCloud
{

	/**
	 * tags data array
	 *
	 * @var array
	 **/
	protected $tags;

	/**
	 * Maximun font size for tag
	 *
	 * @var int
	 **/
	protected $maxFontSize = 26;

	/**
	 * Minimun font size for tag
	 *
	 * @var int
	 **/
	protected $minFontSize = 10;

	/**
	 * Font step between maxFontSize and min FontSize
	 *
	 * @var flaot|int
	 **/
	protected $font_step;

	/**
	 * Maximun tag count
	 *
	 * @var int
	 **/
	protected $maximum = 0;

	/**
	 * Minimun tag count
	 *
	 * @var int
	 **/
	protected $minimum = 0;

	/**
	 * Order for tag display.
	 * Default is random. Tags will display radonmize.
	 * (random, name)
	 *
	 * @var string
	 **/
	protected $order = 'random';

	/**
	 * Tag order direction.
	 * This is use at tag order by name only.
	 *
	 * @var string
	 **/
	protected $orderDirection = 'desc';

	/**
	 * Tag format. Decide tag format is use font-size or class name.
	 * (default is font). Accepted format are font and class.
	 *
	 * @var string
	 **/
	protected $formatter = 'font';

	protected $accepted_formatter = array('font', 'class');

	/**
	 * Font size unit for tag. Supprot (px, pt)
	 *
	 * @var string
	 **/
	protected $fontUnit = 'pt';

	/**
	 * Html tag class prefix string. Default is "tag".
	 * TagCloud will added automatically (-) at end of prefix.
	 *
	 * @var string
	 **/
	protected $classPrefix = 'tag-';

	/**
	 * Html tag to wrap the tag link.
	 * eg: <span><a href="#">$tag</a></span>
	 *
	 * @var string
	 **/
	protected $wrap = '';

	/**
	 * Tag <a> tag attribute title text string.
	 *
	 * @var string
	 **/
	protected $title = 'Total posts %s';

	/**
	 * URL Prefix for tag url.
	 *
	 * @var string
	 **/
	protected $urlPrefix = '';

	/**
	 * Default Constructor method.
	 *
	 * @param array $options Tag Cloud options list array
	 * @return void
	 **/
	public function __construct($options = array())
	{
		if (isset($options['minFont']) ) {
			$this->setMinFontSize($options['minFont']);
		}
		if (isset($options['maxFont']) ) {
			$this->setMaxFontSize($options['maxFont']);
		}
		if (isset($options['fontUnit']) ) {
			$this->setFontUnit($options['fontUnit']);
		}
		if (isset($options['wrap']) ) {
			$this->setWrap($options['wrap']);
		}
		if (isset($options['format']) ) {
			$this->setFormat($options['format']);
		}
		if (isset($options['classPrefix']) ) {
			$this->setClassPrefix($options['classPrefix']);
		}
		if (isset($options['order']) ) {
			if (isset($options['orderDir'])) {
				$dir = $options['orderDir'];
			} else {
				$dir = false;
			}
			$this->setOrder($options['order'], $dir);
		}
		if (isset($options['title'])) {
			$this->setTitle($options['title']);
		}
		if (isset($options['url'])) {
			$this->setUrl($options['url']);
		}
	}

	public function setMaxFontSize($size)
	{
		$this->maxFontSize = (int) $size;
		return $this;
	}

	public function setMinFontSize($size)
	{
		$this->minFontSize = (int) $size;
		return $this;
	}

	public function setFontUnit($unit)
	{
		$this->fontUnit = $unit;
		return $this;
	}

	public function setFormat($format)
	{
		if (in_array($format, $this->accepted_formatter)) {
			$this->formatter = $format;
		}
		return $this;
	}

	public function setWrap($tag)
	{
		$this->wrap = $tag;
		return $this;
	}

	public function setClassPrefix($prefix)
	{
		$this->classPrefix = trim($prefix, '-').'-';
		return $this;
	}

	public function setOrder($order, $asc = false)
	{
		$this->order = $order;
		$this->orderDirection = ($asc) ? 'asc' : 'desc';
		return $this;
	}

	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	public function setUrl($url)
	{
		$this->urlPrefix = url($url);
	}

	public function add($tag, $url = null)
	{
		if (is_null($url)) {
			$url = $tag;
		}

		$this->tags[$tag] = $this->prepareTag($tag, $url);
		return $this;
	}

	public function remove($tag, $remove_all = false)
	{
		if (isset($this->tags[$tag])) {
			if ($remove_all || ($this->tags[$tag]['count'] == 1)) {
				unset($this->tags[$tag]);
			} else {
				$this->tags[$tag]['count'] = $this->tags[$tag]['count'] - 1;
			}
		}
		return $this;
	}

	public function generate()
	{
		$result = array();

		if (is_null($this->tags)) {
			return null;
		}

		// Sorting the tags
		if ($this->order == 'random') {
			$sorted = $this->sortByRandomize();
		} elseif($this->order == 'name') {
			$sorted = $this->sortByName();
		} else {
			$sorted = $this->tags;
		}
		// Set Maximun and Minimun tags count
		$this->setMaxMin();
		// Set Font Step
		$this->setStep();

		foreach ($sorted as $tag => $data) {
			$size = $this->calculateSize($data['count']);
			if ($this->formatter == 'class') {
				$attr = 'class="'.$this->classPrefix.$size.'"';
			} else {
				$attr = 'style="font-size:'.$size.$this->fontUnit.';"';
			}
			$title = 'title="'.sprintf($this->title, $data['count']).'"';
			$result[] = '<a href="'.$this->urlPrefix.'/'.$data['url'].'" '.$attr.' '.$title.' >'.$tag.'</a>';
		}

		if ($this->wrap == 'span') {
			$output = '<span>';
			$output .= join('</span><span>', $result);
			$output .= '</span>';
		} elseif ($this->wrap == 'ul' || $this->wrap == 'ol') {
			$wrap = $this->wrap;
			$output = "<$wrap><li>";
			$output .= join('</li><li>', $result);
			$output .= "</li></$wrap>";
		} else {
			$output = join("\n", $result);
		}

		return $output;
	}

	protected function prepareTag($tag, $url)
	{
		if (isset($this->tags[$tag])) {
			$count = $this->tags[$tag]['count'] + 1;
		} else {
			$count = 1;
		}

		return array('url' => $url, 'count' => $count);
	}


	protected function setMaxMin()
	{
		$i = 0;
		foreach($this->tags as $tag => $data) {
			if ($i == 0) {
				$this->minimum = $data['count'];
				$this->minimum = $data['count'];
			} else {
				if ($data['count'] > $this->maximum) {
					$this->maximum = $data['count'];
				} elseif ($data['count'] < $this->minimum) {
					$this->minimum = $data['count'];
				}
			}
			$i++;
		}
	}

	protected function sortByRandomize()
	{
		$sorted = array();

		if (! is_null($this->tags) ) {
			$tags = array_keys($this->tags);
			shuffle($tags);
			foreach ($tags as $tag) {
				$sorted[$tag] = $this->tags[$tag];
			}
		}

		return $sorted;
	}

	protected function sortByName()
	{
		$sorted = $this->tags;
		ksort($sorted);
		if ($this->orderDirection == 'desc') {
			$sorted = array_reverse($sorted);
		}

		return $sorted;
	}

	protected function calculateSize($size)
	{
		$ratio = (($size - $this->minimum ) * $this->font_step );

		return round($ratio + $this->minFontSize);
	}

	protected function setStep()
	{
		$step = $this->maximum - $this->minimum;

		if ($step <= 0) {
			$step = 1;
		}

		$font_spread = $this->maxFontSize - $this->minFontSize;

		if ($font_spread < 0) {
			$font_spread = 1;
		}

		$this->font_step = $font_spread / $step;
	}

} // END class TagCloud
