<?php

namespace Reborn\MVC\Model;

use Reborn\Cores\Facade;
use Reborn\Config\Config;
use Reborn\Http\Input;
use Reborn\Form\Validation;
use Reborn\Form\ValidationError;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Model Class for Reborn
 * This class is extended class of Illuminate\Eloquent\Model
 *
 * @package Reborn\MVC\Model
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class Model extends BaseModel
{

    /**
     * Slug column name for findBySug method
     *
     * @var string
     **/
    protected $slug_key = 'slug';

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
     * Allow to multisite with table prefix
     *
     * @var boolean
     **/
    protected $multisite = false;

    /**
     * Find a model by its slug key.
     *
     * @param  string  $slug
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|Collection|static
     */
    public static function findBySlug($slug, $columns = array('*'))
    {
        $instance = new static;

        return $instance->newQuery()->where($instance->getSlugKey(), '=', $slug)
                                    ->first($columns);
    }

    /**
     * Get slug key name
     *
     * @return string
     **/
    public function getSlugKey()
    {
        return $this->slug_key;
    }

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

        if (method_exists($this, 'beforeValidation')) {
            $newv = $this->beforeValidation($v);

            if (!is_null($newv) and $newv instanceof Validation) {
                $v = $newv;
            }
        }

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

    /**
     * Get the table associated with the model.
     * Override for multiple prefix
     *
     * @return string
     */
    public function getTable()
    {
        if (isset($this->table)) {
            $table = $this->table;
        } else {
            $table = str_replace('\\', '', snake_case(str_plural(class_basename($this))));
        }

        $manager = Facade::getApplication()->site_manager;

        if ($manager->isMulti() and $this->multisite) {
            $prefix = $manager->tablePrefix();

            return $prefix.$table;
        }

        return $table;
    }

    /**
     * Auto incremnt for unique slug value.
     *
     * @param string $key Input key name or slug value
     * @param boolean $from_key $key is Input key name. Default is true
     * @param string $separator Separator value for increment value. Default is '-'
     * @return string
     **/
    protected function autoSlug($key, $input_key = true, $separator = '-')
    {
        $value = ($input_key) ? $this->attributes[$key] : $key;
        $value = \Str::slug($value);
        $key = $this->getSlugKey();

        if (isset($this->attributes[$key]) and $this->attributes[$key] == $value) {
            return $value;
        }

        $find = $this->whereRaw($key.' REGEXP ?', array('^'.$value.'(\-[0-9]*)?$'))
                        ->get(array($key));

        if ($find->isEmpty()) {
            return $value;
        }

        $max = 1;
        foreach ($find->lists($key) as $slug) {
            if (preg_match('/^'.$value.'(\-([0-9]*))$/', $slug, $m)) {
                $max = $m[2] + 1;
            }
        }

        return $value.$separator.$max;
    }

} // END class Model
