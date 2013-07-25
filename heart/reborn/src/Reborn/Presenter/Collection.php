<?php

namespace Reborn\Presenter;

use Iterator;
use Countable;
use ArrayAccess;

/**
 * Presenter Object Collection Class
 *
 * @package Reborn\Presenter
 * @author Nyan Lynn Htut
 **/
class Collection implements Iterator, ArrayAccess
{

    /**
     * Model Item Collections
     *
     * @var array
     */
	protected $items = array();

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

        $models->each(function($model) use ($presenter, $isObject) {

            if ($isObject) {
                $this->items[] = $presenter->setModel($model);
            } else {
                $this->items[] = new $presenter($model);
            }
        });
	}

	public function offsetExists($key)
    {
        return array_key_exists($key, $this->items);
    }

    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->items[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    public function rewind() {
        $this->pos = 0;
    }

    public function current() {
        return $this->items[$this->pos];
    }

    public function key() {
        return $this->pos;
    }

    public function next() {
        ++$this->pos;
    }

    public function valid() {
        return isset($this->items[$this->pos]);
    }

} // END class Collection implements Iterator, ArrayAccess, Countable
