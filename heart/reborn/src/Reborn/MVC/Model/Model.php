<?php

namespace Reborn\MVC\Model;

use Reborn\Http\Input;
use Reborn\Form\Validation;
use Reborn\Form\ValidationError;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Model Class for Reborn
 * This class is extended class of Illuminate\Eloquent
 *
 * @package Reborn\MVC\Model
 * @author Myanmar Links Professional Web Development Team
 **/
class Model extends BaseModel
{

    /**
     * Variable for validation rules
     *
     * @var array
     **/
    protected $rules = array();

    /**
     * Variable for validation errors
     *
     * @var array
     **/
    protected $validation_errors;

    /**
     * Change the rule by name.
     *
     * @param string $name Rule key name
     * @param string $rule Rule for given name
     * @return void
     **/
    public function changeRule($name, $rule)
    {
        $this->rules[$name] = $rule;
    }

    /**
     * Get validation rules.
     * If you have multiple validation rules base on action,
     * you can override these method for your requirement.
     *
     * @return void
     * @author
     **/
    protected function getRules()
    {
        return $this->rules;
    }

    /**
     * Make Validation
     *
     * @return boolean
     **/
    public function valid(array $inputs = array())
    {
        $inputs = empty($inputs) ? Input::get('*') : $inputs;

        $v = new Validation($inputs, $this->getRules());

        if($v->fail()) {
            $this->validation_errors = $v->getErrors();
            return false;
        }

        return true;
    }

    /**
     * Get Validation Errors
     *
     * @param null|string $key validation error key
     * @return mixed
     **/
    public function errors($key = null)
    {
        if( is_null($this->validation_errors) ) {
            return null;
        }

        if( is_null($key) ) {
            return $this->validation_errors;
        }

        return $this->validation_errors->{$key};
    }

    /**
     * Save the model to the database.
     * Reborn Model add validation process in this method.
     *
     * @param  array  $options
     * @param boolean $need_validation Need to check validation
     * @return bool
     */
    public function save(array $options = array(), $need_validation = true)
    {
        // check validation if needed
        if ($need_validation and ! $this->valid()) {
            return false;
        }

        return parent::save($options);
    }

} // END class Model
