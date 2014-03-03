<?php

namespace Reborn\Form;

use Illuminate\Support\Collection;
use ArrayAccess;

/**
 * ValidationError Class
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development
 **/
class ValidationError extends Collection
{
    /**
     * Set Errors for ValidationError
     *
     * @param  array           $errros Errors messages
     * @return ValidationError
     **/
    public function setErrors(array $errors)
    {
        $this->items = $errors;

        return $this;
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    /**
     * Magic getter method
     *
     * @return string|null
     **/
    public function __get($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        return null;
    }

    /**
     * Magic toString method, Override parent::__toString()
     *
     * @return string
     **/
    public function __toString()
    {
        $errors = implode("\n", $this->items);

        return $errors;
    }

} // END class ValidationError implements ArrayAccess
