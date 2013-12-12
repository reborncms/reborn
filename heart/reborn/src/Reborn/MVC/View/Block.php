<?php

namespace Reborn\MVC\View;

/**
 * View Block Class
 *
 * @package Reborn\MVC\View
 * @author 
 **/
class Block
{

	/**
	 * Defined block lists array
	 *
	 * @var array
	 **/
	protected $blocks = array();

	/**
	 * Block captured data lists
	 *
	 * @var array
	 **/
	protected $captured = array();

	/**
	 * Define the block with name.
	 *
	 * @param string $name
	 * @return void
	 **/
	public function define($name)
	{
		ob_start();

		$this->blocks[] = $name;
	}

	/**
	 * Capture the block buffer for last define block.
	 *
	 * @return void
	 **/
	public function capture()
	{
		$last = array_pop($this->blocks);

		if (isset($this->captured[$last])) {
			$this->extend($last);
		} else {
			$this->captured[$last] = ob_get_clean();
		}		
	}

	/**
	 * Extend the block buffer.
	 * You can set parent(super) buffer with {$name.super} variable
	 * Make new capture, if given name doesn't exists in captured lists.
	 *
	 * @param string $name
	 * @return void
	 **/
	public function extend($name)
	{
		if (isset($this->captured[$name])) {
			$super = $this->captured[$name];
			$this->captured[$name] = str_replace('{'.$name.'.super}', $super, ob_get_clean());
		} else {
			$this->captured[$name] = ob_get_clean();
		}
	}

	/**
	 * Append "content" data to given block "name".
	 *
	 * @param string $name
	 * @param string $content
	 * @return void
	 **/
	public function append($name, $content)
	{
		if (isset($this->captured[$name])) {
			$this->captured[$name] = $this->captured[$name].$content;
		} else {
			$this->captured[$name] = $content;
		}
	}

	/**
	 * Prepend "content" data to given block "name".
	 *
	 * @param string $name
	 * @param string $content
	 * @return void
	 **/
	public function prepend($name, $content)
	{
		if (isset($this->captured[$name])) {
			$this->captured[$name] = $content.$this->captured[$name];
		} else {
			$this->captured[$name] = $content;
		}
	}

	/**
	 * Show captured block by "name".
	 * If "name" doesn't exists in captured lists, return empty string.
	 *
	 * @param string $name
	 * @return string
	 **/
	public function show($name)
	{
		if (isset($this->captured[$name])) {
			return $this->captured[$name];
		}

		return '';
	}

} // END class Block