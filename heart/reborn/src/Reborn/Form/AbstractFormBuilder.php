<?php

namespace Reborn\Form;

use Reborn\Http\Uri;
use Reborn\Http\Input;
use Reborn\Util\Flash;

/**
 * FormBuilder class for Reborn.
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class AbstractFormBuilder
{

	/**
	 * Input key name constant for Flash
	 *
	 * @var string
	 **/
	const INPUT_KEY = '_inputs';

	/**
	 * Use multipart/form-data
	 *
	 * @var boolean
	 **/
	protected $file = false;

	/**
	 * Form legend string
	 *
	 * @var name
	 **/
	protected $legend;

	/**
	 * Field list array for form
	 *
	 * @var array
	 **/
	protected $fields = array();

	/**
	 * Submit button data array
	 *
	 * @var array
	 **/
	protected $submit = array('value' => 'Submit');

	/**
	 * Reset button data array
	 *
	 * @var array
	 **/
	protected $reset = array();

	/**
	 * Cancel <a> tag data array
	 *
	 * @var array
	 **/
	protected $cancel = array();

	/**
	 * Form builder object
	 *
	 * @var Reborn\Form\Blueprint
	 **/
	protected $builder;

	/**
	 * Form validtion object
	 *
	 * @var Reborn\Form\Validation
	 **/
	protected $validator;

	/**
	 * Eloquent Model Object for form field value
	 *
	 * @var Eloquent
	 **/
	protected $model = false;

	/**
	 * Skip fields when saving to the model
	 *
	 * @var string
	 **/
	protected $skipFields = array();

	/**
	 * Creat the FormBuilder Object.
	 *
	 * @param string $action From action URL
	 * @param string $name Form name
	 * @param string $attrs Form Attributes
	 * @return void
	 **/
	public function __construct($action = '', $name = 'default', $attrs = array())
	{
		// Hook for elements setter
		if (method_exists($this, 'setFields')) {
			$this->setFields();
		}

		if ('' == $action) {
			$action = Uri::current();
		}

		$this->builder = new Blueprint($action, $name, $this->file, $attrs);
	}

	/**
	 * Static method to create new form Builder class
	 *
	 * @param string $action From action URL
	 * @param string $name Form name
	 * @param string $attrs Form Attributes
	 * @return \Reborn\Form\AbstractFormBuilder
	 **/
	public static function create($action = '', $name = 'default', $attrs = array())
	{
		$class = get_called_class();
		return new $class($action, $name, $attrs);
	}

	/**
	 * Check the form is valid or not
	 *
	 * @return boolean
	 **/
	public function valid()
	{
		// Check for method is POST
		if (!\Input::isPost()) {
			return false;
		}

		$this->prepareValidation();

		if ($this->validator->valid()) {
			return true;
		}

		// Save Old data in Flash
		$this->setInputsInFlash();

		$this->builder->setErrors($this->validator->getErrors());

		return false;
	}

	/**
	 * Set external hidden fields for Form Build
	 *
	 * @param array $data Hidden data array
	 * @param boolean $merge Merge with original hiddens data. Default is false
	 * @return \Reborn\Form\AbstractFormBuilder
	 **/
	public function setHiddens(array $data, $merge = false)
	{
		$this->builder->setHiddens($data, $merge);

		return $this;
	}

	/**
	 * Build the form. Final step :D
	 *
	 * @param array $hiddenData External Hidden Data
	 * @param boolean $merge Merge with original hiddens data. Default is false
	 * @return string
	 **/
	public function build($hiddenData = array(), $merge = false)
	{
		if (!$this->add()) {
			return null;
		}

		return $this->builder->build($hiddenData, $merge);
	}

	/**
	 * Change the form field's value
	 *
	 * @param string $name Field name
	 * @param string $key Field's key name
	 * @param mixed $val Value for field's key
	 * @return \Reborn\Form\AbstractFormBuilder
	 */
	public function changeValue($name, $key, $val)
	{
		if (isset($this->fields[$name])) {
			$this->fields[$name][$key] = $val;
		}

		return $this;
	}

	/**
	 * Set the form template
	 *
	 * @param string $file Template file path
	 * @return \Reborn\Form\AbstractFormBuilder
	 **/
	public function template($file)
	{
		$this->builder->setTemplate($file);

		return $this;
	}

	/**
	 * Set the model object for form
	 *
	 * @param array|object $model Data Model
	 * @return \Reborn\Form\AbstractFormBuilder
	 **/
	public function setModel($model)
	{
		if (!is_array($model) and !is_object($model)) {
			throw new \InvalidArgumentException("Model must be array or object");
		}

		$this->model = $model;

		return $this;
	}

	/**
	 * Get the model object
	 *
	 * @return Model Object
	 **/
	public function getModel()
	{
		if (!$this->model) return null;

		if (is_string($this->model)) {
			$model = $this->model;
			$this->model = new $model;
		}

		return $this->model;
	}

	/**
	 * Hook function for pre saving the model data.
	 *
	 * @return void
	 **/
	protected function preSave() {}

	/**
	 * Save the current model object
	 *
	 * @return boolean
	 **/
	public function save()
	{
		if (! $this->model) return false;

		$model = $this->getModel();

		foreach ($this->fields as $name => $value) {
			if (in_array($name, $this->getSkipFields())) {
				continue;
			}
			$model->$name = Input::get($name);
		}

		// Call the Pre save hook
		$this->preSave();

		if ($model->save(array(), false)) {
			return true;
		}

		return false;
	}

	/**
	 * Get skip fields array
	 *
	 * @return array
	 **/
	protected function getSkipFields()
	{
		$btns = array();
		$btns['submit'] = isset($this->submit['name']) ? $this->submit['name'] : 'submit';
		$btns['reset'] = isset($this->reset['name']) ? $this->reset['name'] : 'reset';

		return array_merge($this->skipFields, $btns);
	}

	/**
	 * Add the Form fields to Builder
	 *
	 * @return boolean
	 */
	protected function add()
	{
		if (empty($this->fields)) {
			return false;
		}

		$old = null;

		if (Flash::has('_inputs')) {
			$old = Flash::get('_inputs');
		}

		// Add the Fileds
		foreach ($this->fields as $name => $val) {

			$val['label'] = isset($val['label']) ? $val['label'] : '';
			$val['info'] = isset($val['info']) ? $val['info'] : '';
			$val['attr'] = isset($val['attr']) ? $val['attr'] : array();
			$val['value'] = null;

			if (isset($old[$name])) {
				$val['value'] = $old[$name];
			} elseif ($this->model and !is_string($this->model)) {
				$model = $this->model;
				$val['value'] = (is_array($model)) ? $model[$name] : $model->{$name};
			} elseif (isset($val['value'])) {
				$val['value'] = $val['value'];
			}

			$this->builder->render($val['type'], $name, $val);
		}

		// Add the submit button
		$this->builder->addSubmit($this->submit);

		if (!empty($this->reset)) {
			$this->builder->addReset($this->reset);
		}

		if (!empty($this->cancel)) {
			$this->builder->addCancel($this->cancel);
		}

		if (!empty($this->legend)) {
			$this->builder->addLegend($this->legend);
		}

		return true;
	}

	/**
	 * Set form validation
	 *
	 * @return void
	 */
	protected function prepareValidation()
	{
		foreach ($this->fields as $name => $val) {
			// Set validation
			if (isset($val['rule'])) {
				$this->rules[$name] = $val['rule'];
			}
		}

		$this->validator = new Validation(\Input::get('*'), $this->rules);
	}

	/**
	 * Set Input Values to Flash for next request
	 *
	 * @return void
	 **/
	protected function setInputsInFlash()
	{
		$csrf = \Config::get('app.security.csrf_key');

		$all = Input::get('*');
		unset($all[$csrf]);

		Flash::set(self::INPUT_KEY, $all);
	}

} // END class FormBuilder
