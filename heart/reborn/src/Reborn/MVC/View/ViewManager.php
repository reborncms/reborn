<?php

namespace Reborn\MVC\View;

use Reborn\Config\Config;

/**
 * View Manager class for the Reborn
 *
 * @package Reborn\MVC\View
 * @author Myanmar Links Professional Web Development Team
 **/
class ViewManager
{
	/**
	 * Template Object Instance
	 *
	 * @var Reborn\MVC\View\Template
	 **/
	protected $template;

	/**
	 * Theme Object Instance
	 *
	 * @var Reborn\MVC\View\Theme
	 **/
	protected $theme;

	/**
	 * View Object Instance
	 *
	 * @var Reborn\MVC\View\View
	 **/
	protected $view;

	/**
	 * Parser Object Instance
	 *
	 * @var Reborn\MVC\View\Parser
	 **/
	protected $parser;

	// Extension for view file.
	protected $ext = '.html';

	public function __construct()
	{
		$this->theme = $this->themeSetter();

		$this->parser = new Parser();

		\Event::call('reborn.parser.create', array($this->parser));

		$this->addParserHandler();

		$this->view = new View(Config::get('template.cache_path'));

		$this->ext = Config::get('template.template_extension');

		$this->template = new Template($this->theme, $this->view, $this->ext);

		$this->setObject();
	}

	/**
	 * Get the Template Object
	 *
	 * @return Reborn\MVC\View\Template
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Get the Theme Object
	 *
	 * @return Reborn\MVC\View\Theme
	 */
	public function getTheme()
	{
		return $this->theme;
	}

	/**
	 * Get the View Object
	 *
	 * @return Reborn\MVC\View\View
	 */
	public function getView()
	{
		return $this->view;
	}

	/**
	 * Get the Parser Object
	 *
	 * @return Reborn\MVC\View\Parser
	 */
	public function getParser()
	{
		return $this->parser;
	}


	/**
	 * Create the Theme Object
	 *
	 * @return Reborn\MVC\View\Theme
	 */
	protected function themeSetter()
	{
		if (defined('ADMIN')) {
			$theme = \Setting::get('admin_theme');
			$themePath = ADMIN_THEME;
		} else {
			$theme = \Setting::get('public_theme');
			$themePath = THEMES;
		}

		return new Theme($theme, $themePath);
	}

	/**
	 * Set the Parser and Template at View
	 *
	 * @return void
	 **/
	protected function setObject()
	{
		$this->view->setObject($this->parser, $this->template);
	}

	/**
	 * Check and add the parser handler from the active theme
	 *
	 * @return void
	 **/
	protected function addParserHandler()
	{
		$active = $this->theme->getThemePath();
		$handler = $active.'handler'.DS.'register.php';

		if (\Dir::is($active.'handler') and file_exists($handler)) {
			require $handler;
		}

		// Add Habdler Object From Reborn
		$files = \Dir::get(__DIR__.DS.'Handler'.DS.'*.php');
		$namespace = '\Reborn\MVC\View\Handler\\';
		foreach ($files as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			$obj = $namespace.$name;
			$ins = new $obj($this->parser);
			$key = $ins->getKey();
			$this->parser->addHandler($key, $namespace.$name);
		}
	}

} // END class ViewManager
