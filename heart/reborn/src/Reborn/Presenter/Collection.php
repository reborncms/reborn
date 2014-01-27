<?php

namespace Reborn\Presenter;

use Iterator;
use Countable;
use ArrayAccess;
use Reborn\Cores\Facade;

/**
 * Presenter Object Collection Class
 *
 * @package Reborn\Presenter
 * @author Nyan Lynn Htut
 **/
class Collection implements Iterator, ArrayAccess, Countable
{

    /**
     * Model Item Collections
     *
     * @var array
     */
	protected $items = array();

    /**
     * Model Collection Class
     *
     * @var \Illuminate\Database\Eloquent\Collection
     **/
    protected $collection;

    /**
     * Pointer Position
     *
     * @var int
     */
	private $pos = 0;

    /**
     * Constructor method for Collection
     *
     * @param Object $model Data Model Object
     * @param string|Pressentation $presenter Presenter Object or Presenter Class Name
     * @return void
     */
	public function __construct($models, $presenter)
	{
        $isObject = false;

        if ($presenter instanceof Presentation) {
            $isObject = true;
        }

        foreach ($models as $model) {
            if ($isObject) {
                $this->items[] = $presenter->model($model);
            } else {
                $this->items[] = new $presenter($model);
            }
        }

        $this->collection = $models;
	}

    /**
     * Count total items from data collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Check the item has or not at given offset.
     *
     * @param  string  $key
     * @return boolean
     */
	public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Get the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  string  $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->items[$key] = $value;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Move cursor to initial state.
     *
     * @return void
     */
    public function rewind() {
        $this->pos = 0;
    }

    /**
     * Return current cursor's data.
     *
     * @return mixed
     */
    public function current() {
        return $this->items[$this->pos];
    }

    /**
     * Return current cursor position.
     *
     * @return integer
     */
    public function key() {
        return $this->pos;
    }

    /**
     * Increase cursor to next position.
     *
     * @return void
     */
    public function next() {
        ++$this->pos;
    }

    /**
     * Check cursor postion is has or not.
     *
     * @return boolean
     */
    public function valid() {
        return isset($this->items[$this->pos]);
    }

    /**
     * Get the collection of items as a plain array data.
     *
     * @return array
     */
    public function toArray()
    {
        if (is_null($this->items)) {
            return array();
        }

        return array_map(function($val)
                {
                    return $val->toArray();

                }, $this->items);
    }

    /**
     * Get the collection of items as JSON string.
     *
     * @param  integer  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the collection items to its string data.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * PHP Magic method __call for dynamic method call
     *
     * @param string $method
     * @param array $params
     * @return mixed
     **/
    public function __call($method, $params)
    {
        if (! method_exists($this->collection, $method)) {
            if(Facade::getApplication()->runInDevelopment()) {
                throw new \BadMethodCallException("Method {$method}() not found!");
            }

            return null;
        }

        return call_user_func_array(array($this->collection, $method), $params);
    }

} // END class Collection implements Iterator, ArrayAccess, Countable
