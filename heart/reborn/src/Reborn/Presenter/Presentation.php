<?php

namespace Reborn\Presenter;

use Reborn\Util\Str;
use Reborn\Exception\RbException;

/**
 * Data Presentation Layer for Reborn CMS
 *
 * @package Reborn\Presenter
 * @author Nyan Lynn Htut
 **/
class Presentation
{

    /**
     * Data Model Object or Array Resource
     *
     * @var object|array
     **/
    protected $resource;

    /**
     * Disable(skip) method lists from Data Model
     *
     * @var array
     **/
    protected $skip_methods = array('save', 'delete', 'update', 'insert', 'destory');

    /**
     * Default constructor method
     *
     * @param  Object $model Data Model Object
     * @return void
     **/
    public function __construct($model)
    {
        $this->model($model);

        // Call extend skip method if exists
        if (method_exists($this, 'extendSkipMethods')) {
            $this->extendSkipMethods();
        }
    }

    /**
     * Setter method for data model
     *
     * @param  Object                         $model Data Model Object
     * @return \Reborn\Presenter\Presentation
     **/
    public function model($model)
    {
        $this->resource = $model;

        return $this;
    }

    /**
     * Static method to make the new Presentation Object
     *
     * @param  array|object                   $model
     * @param  boolean                        $is_collection
     * @return \Reborn\Presenter\Presentation
     **/
    public static function make($model, $is_collection = false)
    {
        $class = get_called_class();

        if ($is_collection) {
            return new Collection($model, $class);
        }

        $ins = new $class($model);

        return $ins;
    }

    /**
     * Check Model Object is empty or not.
     *
     * @return boolean
     **/
    public function isEmpty()
    {
        return empty($this->resource);
    }

    /**
     * Convert the model from this object to Array
     *
     * @return array
     **/
    public function toArray()
    {
        return $this->resource->toArray();
    }

    /**
     * Convert the model from this object to Json String
     *
     * @param  integer $options
     * @return string
     **/
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Magic method __toString
     *
     * @return string (Json String)
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * PHP's magic method to get the object variable
     * example :
     * <code>
     * 		class UserPresenter extends Presenter
     * 		{
     * 			public function attributeFullName()
     * 			{
     * 				return $this->resource->first_name.' '.$this->resource->last_name;
     * 			}
     * 		}
     *
     * 		$p = UserPresenter::make(User::find(1));
     *
     * 		echo $p->full_name;
     * </code>
     *
     * @param  string $name Property name
     * @return mixed
     **/
    public function __get($name)
    {
        $method = 'attribute'.Str::studly($name);
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return is_array($this->resource) ? $this->resource[$name] : $this->resource->{$name};
    }

    /**
     * magic method __call
     *
     * @return mixed
     **/
    public function __call($name, $args = array())
    {
        if (in_array($name, $this->skip_methods)) {
            throw new RbException("Method name \"{$name}\" is disabled!");
        }

        if (method_exists($this->resource, $name)) {
            return call_user_func_array(array($this->resource, $name), $args);
        }

        throw new RbException('Presenter Error: '.get_called_class().'::'.$name.' method does not exist');
    }

} // END class Presentation
