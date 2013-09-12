<?php

namespace Field;

use Input;
use Reborn\Cores\Application;

/**
 * Form Field Type Abstract class
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
abstract class AbstractType
{

	protected $type;

	/**
	 * IOC Container Instance
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * Default Instance Method
	 *
	 * @return void
	 **/
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	abstract public function filler($default = null, $options = null);

	/**
	 * Get display form view to insert data
	 *
	 * @return string
	 **/
	public function displayForm($field, $value = null)
	{
		return null;
	}

	/**
	 * Make field info.
	 * eg: <p class="info">This field is what</p>
	 *
	 * @param string|null $info Field Info (description)
	 * @return string
	 **/
	protected function makeInfo($info)
	{
		return $info ? '<p class="info">'.$info.'</p>' : '';
	}

	/**
	 * Get Field value.
	 *
	 * @param string $key Field element name
	 * @param string $default Default value
	 * @return string
	 **/
	protected function getValue($key, $default)
	{
		return Input::get($key, $default);
	}

	/**
	 * Make Options string to array for dropdown|checkbox|radio
	 *
	 * @param string $str options string
	 * @return array
	 **/
	protected function makeOptions($str)
	{
		$lines = explode("\n", $str);

		$options = array();

		foreach ($lines as $line) {
			list($key, $value) = explode('=', $line);
			$options[$key] = $value;
		}

		return $options;
	}

	/**
	 * Pre Check before saving.
	 *
	 * @param Field\Model\Field $field Field Model Object
	 * @return boolean
	 **/
	public function preSaveCheck($field)
	{
		$key = $field->field_slug;

		$value = Input::get($key);

		if ('' == $value) return false;

		if (\Event::has('field.'.$key.'save_check')) {
			$res = \Event::call('field.'.$key.'save_check', array($value));

			return isset($res[0]) ? $res[0] : false;
		}

		return true;
	}

	/**
	 * Pre Check before updating.
	 *
	 * @param Field\Model\Field $field Field Model Object
	 * @param mixed $value Field value form db
	 * @return mixed
	 **/
	public function preUpdateCheck($field, $value)
	{
		$key = $field->field_slug;

		$new_value = Input::get($key);

		if ('' == $new_value) return false;

		// Same value, not need to update
		if ($value == $new_value) return false;

		if (\Event::has('field.'.$key.'update_check')) {
			$res = \Event::call('field.'.$key.'update_check', array($new_value));

			return isset($res[0]) ? $res[0] : false;
		}

		return $new_value;
	}

} // END abstract class AbstractType
