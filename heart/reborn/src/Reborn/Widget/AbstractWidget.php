<?php

namespace Reborn\Widget;

/**
 * Abstract Widget Class
 *
 * @package Reborn\Widget
 * @author Myanmar Links Web Development Team
 **/
abstract Class AbstractWidget
{
    /**
     * Widget Attributes array
     *
     * @var array
     **/
    protected $attrs = array();

    /**
     * Widget properties array
     *
     * @var array
     **/
    protected $properties = array();

    /**
     * Render the Widget View
     *
     * @return string
     **/
    public function render() {}

    /**
     * Options array for Widget Setting
     *
     * @return array
     */
    public function options() {}

    /**
     * Set the Widget Attribute
     *
     * @param  string|array $name
     * @param  mixed        $value
     * @return void
     **/
    public function set($name, $value = null)
    {
        $owner = $this->getOwner();

        if (is_null($value) and is_array($name)) {
            foreach ($name as $k => $val) {
                $this->attrs[$owner][$k] = $val;
            }
        } else {
            $this->attrs[$name] = $value;
        }
    }

    /**
     * Get the Widget Attribute
     *
     * @param  string $name
     * @param  mixed  $default Default will return when $name not found
     * @return mixed
     **/
    public function get($name, $default = null)
    {
        $owner = $this->getOwner();

        if (isset($this->attrs[$owner][$name])) {
            return $this->attrs[$owner][$name];
        }

        return $default;
    }

    /**
     * Get the properties array
     *
     * @return array
     **/
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get Owner (Widget Class) Name
     *
     * @return string
     **/
    protected function getOwner()
    {
        $owner = get_called_class();

        return $owner;
    }

    /**
     * Show the Widget View
     *
     * @param  array       $data
     * @param  null|string $filename View filename. Default is display
     * @return string
     **/
    protected function show($data = array(), $filename = "display")
    {
        $name = str_replace('\Widget', '', $this->getOwner());

        if (! is_array($data)) {
            $data = (array) $data;
        }

        return \Widget::view($name, $data, $filename);
    }

} // END abstract Class AbstractWidget
