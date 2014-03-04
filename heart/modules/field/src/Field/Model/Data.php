<?php

namespace Field\Model;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

/**
 * Field Data Collection
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Data implements ArrayAccess, IteratorAggregate
{

    /**
     * Variable for field data
     *
     * @var array
     **/
    protected $data = array();

    /**
     * Make Data Instance
     *
     * @param  array $data
     * @return void
     **/
    public function __construct($data = array())
    {
        if (! empty($data) ) {
            foreach ($data as $k => $v) {
                $val = is_json($v) ? (array) json_decode($v) : $v;
                $this->data[$k] = $val;
            }
        }
    }

    /**
     * Check given key has or not
     *
     * @param  string  $key Keyname
     * @return boolean
     **/
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Extra Field data with ul>li
     *
     * @param  array  $attrs Attributes for ul
     * @return string
     **/
    public function lists($attrs = array())
    {
        return \Html::ul($this->data, $attrs);
    }

    /**
     * Extra Field data with table
     *
     * @param  array  $attrs Attributes for table
     * @return string
     **/
    public function table($attrs = array(), $with_label = true)
    {
        if (empty($this->data)) return null;

        $table = '';

        if (is_bool($attrs)) {
            $with_label = $attrs;
            $attrs = array();
        }

        if ($with_label) {
            foreach ($this->data as $k => $v) {
                if (is_array($v)) {
                    $v = implode(', ', $v);
                }
                $table .= '<tr>';
                $table .= '<td>'.$k.'</td>';
                $table .= '<td>'.$v.'</td>';
                $table .= '<tr>';
            }
        } else {
            foreach ($this->data as $v) {
                if (is_array($v)) {
                    $v = implode(', ', $v);
                }
                $table .= '<tr>';
                $table .= '<td>'.$v.'</td>';
                $table .= '<tr>';
            }
        }

        return \Html::tag('table', $table, $attrs);
    }

    /**
     * Implode data string
     *
     * @param  string $glue Glue for implode
     * @return string
     **/
    public function implode($glue = "\n")
    {
        return implode($glue, $this->data);
    }

    public function offsetSet($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Get error message as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get error message as json
     *
     * @param  integer $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->data, $options);
    }

    /**
     * Get an iterator for the data.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Magic setter method
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     **/
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Magic getter method
     *
     * @param  string $key
     * @return mixed
     **/
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Magic toString method
     *
     * @return string
     **/
    public function __toString()
    {
        return static::implode("\n");
    }

} // END class Data implements ArrayAccess
