<?php

namespace Setting\Lib;

use Closure;

/**
 * Setting Module Helper
 *
 * @package Setting(Module Package)
 * @author Myanmar Links Professional Web Development Team
 **/
class Helper
{

	/**
     * Generate the Form Field
     *
     * @return string|null
     **/
    public static function field($v)
    {
        $supports = array('text', 'password', 'textarea', 'select', 'radio', 'checkbox');

        $type = $v['type'];
        if (in_array($type, $supports)) {
            $method = $v['type'].'Field';
            return static::$method($v);
        } elseif ('callback:' == substr($type, 0, 9)) {
            list(,$name) = explode(':', $type);
            list($value, $attrs) = static::getClassAndValue($v);
            \Event::call($name, array($value, $attrs));
        }

        return null;
    }

    /**
     * Form Select Field Complie
     *
     * @return string
     **/
    protected static function selectField($v)
    {
    	list($value, $attrs) = static::getClassAndValue($v);

    	return \Form::select($v['slug'], $v['options'], $value, $attrs );
    }

    /**
     * Form Text Field Complie
     *
     * @return string
     **/
    protected static function textField($v)
    {
    	list($value, $attrs) = static::getClassAndValue($v);

    	return \Form::input($v['slug'], $value,'text', $attrs);
    }

    /**
     * Form Password Field Complie
     *
     * @return string
     **/
    protected static function passwordField($v)
    {
        list($value, $attrs) = static::getClassAndValue($v);

        return \Form::password($v['slug'], $value, $attrs);
    }

    /**
     * Form Textarea Field Complie
     *
     * @return string
     **/
    protected static function textareaField($v)
    {
        list($value, $attrs) = static::getClassAndValue($v);

        return \Form::textarea($v['slug'], $value, $attrs);
    }

    /**
     * Form Check Box Field Complie
     *
     * @return string
     **/
    protected static function checkboxField($v)
    {
        list($value, $attrs) = static::getClassAndValue($v);

        if ($value == '1' or $value === true) {
            $attrs = $attrs + array('checked' => 'checked');
        }

        return \Form::checkbox($v['slug'], $value, $attrs);
    }

    /**
     * Form Radio Field Complie
     *
     * @return string
     **/
    protected static function radioField($v)
    {
        list($class, $value) = static::getClassAndValue($v);

        return \Form::radioGroup($v['slug'], $v['options'], $value);
    }

    /**
     * Get the Class and Value for Field
     *
     * @param array $v Value array
     * @return array
     **/
    protected static function getClassAndValue($v)
    {
        $class = $v['require'] ? 'required' : '';
        $class = isset($v['class']) ? $v['class'].' '.$class : $class;
        $value = $v['value'] ? $v['value'] : $v['default'];
        $attrs = isset($v['attrs']) ? $v['attrs'] : array(0);

        $attrs = array_merge(array('class' => $class), $attrs);

        return array($value, $attrs);
    }

} // END class
