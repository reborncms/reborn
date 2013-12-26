<?php

namespace Reborn\Asset;

use Munee\Request;
use Munee\Dispatcher;

class MuneeResolver
{
	/**
	 * Munee Request instance
	 *
	 * @var \Munee\Request
	 **/
	protected $munee;

	/**
	 * Asset files with comma separate string
	 *
	 * @var string
	 **/
	protected $files;

	/**
	 * Asset sub folder name
	 *
	 * @var string
	 **/
	protected $sub_folder;

	/**
	 * Request from admin panel
	 *
	 * @var boolean
	 **/
	protected $admin = false;

	/**
	 * Request for global asset path
	 *
	 * @var boolean
	 **/
	protected $global = false;

	/**
	 * Create default instance
	 *
	 * @param boolean $minify
	 * @return void
	 **/
	public function __construct($files, $sub_folder = 'css', $minify = true)
	{
		$this->files = $files;
		$this->sub_folder = $sub_folder;
		$this->munee = new Request(array('lessifyAllCss' => true));
		$this->minify($minify);
	}

	/**
	 * Set asset files
	 *
	 * @param string $files
	 * @return \Reborn\Asset\MuneeResolver
	 **/
	public function files($files)
	{
		$this->files = $files;
	}

	/**
	 * Set minify for Munee Request
	 *
	 * @param boolean $value
	 * @return \Reborn\Asset\MuneeResolver
	 **/
	public function minify($value = true)
	{
		$value = ((bool) $value === true) ? 'true' : 'false';
		$this->munee->setRawParam('minify', $value);

		return $this;
	}

	/**
	 * Set request is admin panel
	 *
	 * @return void
	 **/
	public function isAdmin()
	{
		$this->admin = true;
		$this->global = false;
	}

	/**
	 * Set request is global asset path
	 *
	 * @return void
	 **/
	public function isGlobal()
	{
		$this->global = true;
		$this->admin = false;
	}

	/**
	 * Run the Munee Dispatcher
	 *
	 * @return string
	 **/
	public function run()
	{
		$this->munee->setFiles($this->solveFiles());

		return Dispatcher::run($this->munee);
	}

	/**
	 * Dynamically echo string with PHP's magic method.
	 *
	 * @return string
	 **/
	public function __toString()
	{
		return $this->run();
	}

	/**
	 * Solve file string for Munee
	 *
	 * @return string
	 **/
	protected function solveFiles()
	{
		$files = array();

		foreach (explode(',', rtrim($this->files, '/')) as $file) {
			$explode = explode('__', $file);

			if (count($explode) > 1) {
				$path = $this->getFromModule($explode[0], $explode[1]);
			} else {
				$path = $this->getFromTheme($explode[0]);
			}

			if (! is_null($path) ) {
				$files[] = $path;
			}
		}

		return join(',', $files);
	}

	/**
	 * Get file path from module
	 *
	 * @param string $module
	 * @param string $file
	 * @return string|null
	 **/
	protected function getFromModule($module, $file)
	{
		$path = \Reborn\Module\ModuleManager::get($module, 'path');

		if (is_file($file = $path.DS.'assets'.DS.$this->sub_folder.DS.$file)) {
			return str_replace(BASE, '', $file);
		}

		return null;
	}

	/**
	 * Get file path from active theme
	 *
	 * @param string $file
	 * @return string|null
	 **/
	protected function getFromTheme($file)
	{
		$path = $this->getThemePath();

		if (is_file($file = $path.DS.'assets'.DS.$this->sub_folder.DS.$file)) {
			return str_replace(BASE, '', $file);
		}

		return null;
	}

	/**
	 * Get theme path
	 *
	 * @return string
	 **/
	protected function getThemePath()
	{
		if ($this->global) {
			return (BASE.'global');
		} elseif ($this->admin) {
			$manager = \Reborn\Cores\Facade::getApplication()->view_manager;
			return rtrim($manager->adminTheme()->getThemePath(), '/');
		}

		return rtrim(\Reborn\Cores\Facade::getApplication()->theme->getThemePath(), '/');
	}
}
