<?php

namespace Reborn\Widget;

use Reborn\Util\Str;
use Reborn\Filesystem\File;
use Reborn\Filesystem\Directory as Dir;

/**
 * Widget class
 *
 * @package Reborn\Widget
 * @author Myanmar Links Web Development Team
 **/
class Widget
{
	/**
	 * All Avaliable Widgets
	 *
	 * @var array
	 **/
	protected $widgets = array();

	/**
	 * All Avaliable Modules
	 *
	 * @var array
	 **/
	protected $modules = array();

	/**
	 * Form File variable
	 *
	 * @var string
	 **/
	protected static $form = 'form.html';

	/**
	 *Widget Table variable
	 *
	 * @var string
	 **/
	protected $table = 'widgets';

	/**
	 * Default Construct Method
	 *
	 * @return void
	 **/
	public function __construct()
	{
		$this->modules = \Module::getAll();
	}

	/**
	 * Initialize the Widget Class
	 * This method call from Application Initialize
	 * We prepare all widgets at this method.
	 *
	 * @return void
	 **/
	public function initialize()
	{
		$this->find();
	}

	/**
	 * This is static method call (helper of Widget::run())
	 * You can call widget use by this method.
	 * <code>
	 * 	// In your template view file
	 *  {{ Widget::call('BlogCategory') }}
	 *
	 * // With arguments passing
	 * {{ Widget::call('BlogCategory', array('limit' => 5, 'order' => 'asc')) }}
	 * </code>
	 *
	 * @param string $name Widget Name
	 * @param array $attrs
	 * @return string
	 **/
	public static function call($name, $attrs = array())
	{
		$ins = \Registry::get('app')->widget;

		return $ins->run($name, $attrs);
	}

	/**
	 * Static method for WidgetView.
	 *
	 * @param string $name Widget name
	 * @param array $data Data array for view
	 * @return string|null
	 */
	public static function view($name, $data = array(), $filename)
	{
		$ins = \Registry::get('app')->widget;

		$theme = \Registry::get('app')->view->getTheme();

		$theme_path = $theme->getThemePath().'views'.DS.'widgets'.DS.$name.DS;

		if ($wg = $ins->get($name)) {

			if (File::is($theme_path.$filename.'.html')) {
				$file = $theme_path.$filename.'.html';
			} else {
				$file = $wg['path'].'views'.DS.$filename.'.html';
			}

			if (File::is($file)) {
				static::getView()->set($data);
				return static::getView()->render($file);
			}
		}

		return null;
	}

	/**
	 * Get the view object.
	 */
	protected static function getView()
	{
		return \Registry::get('app')->view->getView();
	}

	/**
	 * Static method for Widget Property get method.
	 *
	 * @param string $name
	 * @return array
	 */
	public static function propertiesFrom($name)
	{
		$ins = \Registry::get('app')->widget;

		return $ins->getProperty($name);
	}

	/**
	 * Get the Widget Properties.
	 * widget peroperties are 'name', 'description', 'author',...
	 *
	 * @param string $name
	 * @return array
	 */
	public function getProperty($name)
	{
		$class = $this->getClass($name);

		return $class->getProperties();
	}

	/**
	 * Get the Widget Options
	 *
	 * @param string $name
	 * @return array
	 */
	public function getOptions($name)
	{
		if (strpos($name, '::')) {

			$s = explode('::', $name);

			$name = $s[0];

			$sub_name = $s[1];

		}

		$class = $this->getClass($name);

		$options = $class->options();

		if (isset($sub_name)) {
			
			if (isset($options[$sub_name])) {

				return $options[$sub_name];

			}
			return;
		
		} else {

			return $options;

		}
		
	}

	/**
	 * Static method to Get the Widget Options
	 *
	 * @param string $name
	 * @return array
	 */
	public static function options($name)
	{
		$ins = \Registry::get('app')->widget;
		
		return $ins->getOptions($name);	
	}

	/**
	 * Run the widget.
	 *
	 * @param string $name
	 * @param array $args
	 * @return string
	 */
	public function run($name)
	{
		$args = array_slice(func_get_args(), 1);

		list($class, $method) = $this->widgetNameParse($name);

		$class = $this->getClass($class, $args);

		if (is_null($class)) {
			return null;
		}

		if (is_null($method)) {
			$method = 'render';
		}

		return call_user_func_array(array($class, $method), array());
	}

	/**
	 * Saving the widget Attributes.
	 *
	 * @param string $name
	 * @param array $args
	 * @return string
	 */
	public function saving($name)
	{
		$args = array_slice(func_get_args(), 1);

		$class = $this->getClass($name, $args);

		if (is_null($class)) {
			return null;
		}

		return call_user_func_array(array($class, 'save'), array());
	}

	/**
	 * Updating the widget Attributes.
	 *
	 * @param string $name
	 * @param array $args
	 * @return string
	 */
	public function updating($name)
	{
		$args = array_slice(func_get_args(), 1);

		$class = $this->getClass($name, $args);

		if (is_null($class)) {
			return null;
		}

		return call_user_func_array(array($class, 'update'), array());
	}

	/**
	 * Display the widget Form (for Admin Panel...).
	 *
	 * @param string $name
	 * @param array $args
	 * @return string
	 */
	public function displayForm($name)
	{
		$args = array_slice(func_get_args(), 1);

		$class = $this->getClass($name, $args);

		if (is_null($class)) {
			return null;
		}

		return call_user_func_array(array($class, 'form'), array());
	}

	/**
	 * Delete the widget Form DB.
	 *
	 * @param string $name
	 * @param array $args
	 * @return string
	 */
	public function deleting($name)
	{
		$args = array_slice(func_get_args(), 1);

		$class = $this->getClass($name, $args);

		if (is_null($class)) {
			return null;
		}

		return call_user_func_array(array($class, 'delete'), array());
	}

	/**
	 * Get all avaliable widgets
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->widgets;
	}

	/**
	 * Check widget is has or not
	 *
	 * @return string $name
	 * @return boolean
	 */
	public function has($name)
	{
		return isset($this->widgets[$name]);
	}

	/**
	 * Get the avaliable widget by name
	 *
	 * @param string $name Widget Name
	 * @return array|null
	 */
	public function get($name)
	{
		if (isset($this->widgets[$name])) {
			return $this->widgets[$name];
		}

		return null;
	}

	/**
	 * Find all avaliable widgets for Reborn CMS
	 *
	 * @return void
	 */
	public function find()
	{
		// Find and Prepare widgets form Modules
		foreach ($this->modules as $name => $attrs) {
			if ($attrs['enabled']) {

				$dirs = Dir::get($attrs['path'].'Widgets'.DS.'*', GLOB_ONLYDIR);

				$this->prepare($dirs);
			}
		}

		// Find and Prepare from Widgets Path
		$this->externalWidgets();

		// Find and Prepare from Active Theme Path
		$this->themeWidgets();
	}

	/**
	 * Find Widgets from content\widgets\ Path
	 *
	 * @return void
	 */
	protected function externalWidgets()
	{
		$dirs = Dir::get(WIDGETS.'*', GLOB_ONLYDIR);

		$this->prepare($dirs);
	}

	/**
	 * Find Widgets from active theme
	 *
	 * @return void
	 */
	protected function themeWidgets()
	{
		$view = \Registry::get('app')->view;
		$theme = $view->getTheme();

		$all = $theme->findWidgets();

		$this->prepare($all);
	}

	/**
	 * Prepare the widgets data array
	 *
	 * @param array $dirs
	 * @return void
	 */
	protected function prepare($dirs)
	{
		if (empty($dirs)) {
			return true;
		}

		foreach($dirs as $dir) {
			$name = basename($dir);
			$this->widgets[$name]['class'] = $name;
			$this->widgets[$name]['file'] = $dir.DS.'Widget.php';
			$this->widgets[$name]['path'] = $dir.DS;
		}
	}

	/**
	 * Parse Class and Method from Widget Calling Name
	 *
	 * @param string $name
	 * @return array
	 **/
	protected function widgetNameParse($name)
	{
		if (false != strpos($name, '::')) {
			return explode('::', $name, 2);
		} else {
			return array($name, null);
		}
	}

	/**
	 * Get Widget Class by name
	 *
	 * @param string $name Widget name
	 * @param array $args Widget arguments array
	 * @return WidgetClass|null
	 */
	protected function getClass($name, $args = array())
	{
		if(!empty($args)) {
			$args = $args[0];
		}

		if ($this->has($name)) {
			$klass = $this->widgets[$name]['class'].'\Widget';

			if (! class_exists($klass)) {
				require $this->widgets[$name]['file'];
			}

			$class = new $klass;

			if ($class instanceof AbstractWidget) {
				$class->set($args);
				return $class;
			}
		}

		return null;
	}

} // END class Widget
