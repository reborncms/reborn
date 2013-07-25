<?php

namespace Reborn\Presenter;

/**
 * Data Presentation Layer for Reborn CMS
 *
 * @package Reborn\Presenter
 * @author Nyan Lynn Htut
 **/
class Presentation
{

	/**
	 * Data Model Object or Array
	 *
	 * @var object|array
	 **/
	protected $model;

	/**
	 * Model Key's name.
	 *
	 * @var string
	 **/
	protected $model_key = 'model';

	/**
	 * Default constructor method
	 *
	 * @param Object $model Data Model Object
	 * @return void
	 * @author
	 **/
	public function __construct($model)
	{
		$this->setModel($model);
	}

	/**
	 * Setter method for data model
	 *
	 * @param Object $model Data Model Object
	 * @return void
	 **/
	public function setModel($model)
	{
		$this->{$this->model_key} = $this->model = $model;
	}

	/**
	 * Static method to make the new Presentation Object
	 *
	 * @return $this
	 **/
	public static function make($model, $with_collection = false)
	{
		$class = get_called_class();

		if ($with_collection) {
			return new Collection($model, $class);
		}

		$ins = new $class($model);

		return $ins;
	}

	/**
	 * PHP's magic method to get the object variable
	 *
	 * @param string $name Property name
	 * @return mixed
	 **/
	public function __get($name)
	{
		if (method_exists($this, $name)) {
			return $this->{$name}();
		}

		return $this->model->{$name};
	}

	/**
	 * magic method __call
	 *
	 * @return void
	 * @author
	 **/
	public function __call($name, $args = array())
	{
		if (method_exists($this->model, $name)) {
			return call_user_func_array(array($this->model, $name), $args);
		}

		throw new \RbException('Presenter Error: '.get_called_class().'::'.$name.' method does not exist');
	}

} // END class Presentation
