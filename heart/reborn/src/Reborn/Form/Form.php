<?php

namespace Reborn\Form;

use Config;
use Reborn\Http\Uri;
use Reborn\Util\Str;
use Reborn\Util\Html;
use Reborn\Util\Flash;

/**
 * Form class for Reborn CMS
 *
 * @package Reborn\Form
 * @author Reborn CMS Development Team
 **/
class Form
{
    /**
     * Extended Form Element Type
     *
     * @var array
     **/
    protected static $extends;

    /**
     * Data for form filed.
     * This "data" may be Array, Object or Model.
     *
     * @var mixed
     **/
    protected static $data;

    /**
     * Data from Flash::inputs().
     *
     * @var arrya|null
     **/
    protected static $flashdata;

    /**
     * Extend the Form Element.
     *
     * @param string $name Element name
     * @param Closure $callback Callback function for this element
     * @return void
     **/
    public static function extend($name, $callback)
    {
        if(!is_callable($callback)) {
            throw new \RbException("Second parameter for Form::extend() must be callable!");
        }

        static::$extends['ext_'.$name] = $callback;
    }

    /**
     * Form open tag element
     *
     * @param string $action Form action
     * @param string $name Form name
     * @param boolean $file Enable for enctype='multipart/form-data'
     * @param string $attrs Form tag attributes
     * @return string
     **/
    public static function start($action = '', $name = null, $file = false, $attrs=array())
    {
        // Skip $file
        if(is_array($file)) {
            $attrs = $file;
            $file = false;
        }

        if ($file == true && isset($attrs['enctype'])) {
            unset($attrs['enctype']);
        }
        $method = (isset($attrs['method'])) ? '' : ' method="post"';
        $attr = Html::buildAttributes($attrs);

        if ('' === $action) {
            $action = Uri::current();
        } elseif (!strstr($action,'://')) {
            $action = Uri::create($action);
        }

        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';
        $enctype = ($file == true) ? " enctype='multipart/form-data'" : "";

        return '<form name="'.$name.'"'.$method.' action="'.$action.'"'.$id.$attr.$enctype.'>';
    }

    /**
     * From open tag element for "$data".
     *
     * @param string $action Form action
     * @param string $name Form name
     * @param boolean $file Enable for enctype='multipart/form-data'
     * @param string $attrs Form tag attributes
     * @return string
     **/
    public static function startFor($data, $action = '', $name = null, $file = false, $attrs=array())
    {
        static::$data = $data;

        return static::start($action, $name, $file, $attrs);
    }

    /**
     * Set Data Provider (Model) for Form
     *
     * @param mixed $provider
     * @return void
     **/
    public static function provider($provider)
    {
        static::$data = $provider;
    }

    /**
     * Close element
     *
     * @return string
     **/
    public static function end()
    {
        return '</form>';
    }

    /**
     * Fieldset start
     *
     * @param string $legend Legend Text
     * @param string $name Fieldset name
     * @param array $attrs Attributes
     * @return string
     **/
    public static function fieldsetStart($legend = null, $name = null, $attrs = array())
    {
        $attr = Html::buildAttributes($attrs);
        $fs = '<fieldset name="'.$name.'"'.$attr.'>';
        $fs .= '<legend>'.$legend.'</legend>';

        return $fs;
    }

    /**
     * Fieldset End
     *
     * @return string
     **/
    public static function fieldsetEnd()
    {
        return '</fieldset>';
    }

    /**
     * Honey Pot Filed for Spam Filter
     *
     * @param string|null $name Field name. Default name is "honey_pot"
     * @return string
     **/
    public static function honeypot($name = null)
    {
        $name = is_null($name) ? 'honey_pot' : $name;
        $attr = array('style' => 'display:none;');
        return static::input($name, '', 'text', $attr);
    }

    /**
     * Input Field
     *
     * @return string
     * @param string $name Input Field Name
     * @param string $type Input Field Type
     * @param string $attrs Attributes
     **/
    public static function input($name, $value = null, $type = 'text', $attrs = array())
    {
        $attr = Html::buildAttributes($attrs);

        if (isset($attrs['id'])) {
            $id = '';
        } else {
            $id = ' id="'.Str::sanitize($name, 'A-Za-z-0-9-_\s').'"';
        }

        $value = static::getValue($name, $value);

        $val = ' ';
        if (!is_null($value)) {
            // Convert string with comma separated value , if value is array
            // This problem is cause at Form::tags() with autocomplete
            $value = (is_array($value)) ? implode(',', $value) : $value;

            $val = ' value="'.$value.'" ';
        }

        return '<input type="'.$type.'" name="'.$name.'"'.$id.$val.$attr.'/>';

    }

    /**
     * Text Input
     *
     * @param string $name Text input name
     * @param mixed $value Value of text input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function text($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'text', $attrs);
    }

    /**
     * Password Input
     *
     * @param string $name Password input name
     * @param mixed $value Value of password input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function password($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'password', $attrs);
    }

    /**
     * Hidden Input
     *
     * @param string $name hidden input name
     * @param mixed $value Value of hidden input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function hidden($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'hidden', $attrs);
    }

    /**
     * File Input
     *
     * @param string $name file input name
     * @param array $attrs Attributes
     * @return string
     **/
    public static function file($name, $attrs = array())
    {
        return static::input($name, null, 'file', $attrs);
    }

    /**
     * Email Input
     *
     * @param string $name email input name
     * @param mixed $value Value of email input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function email($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'email', $attrs);
    }

    /**
     * Url Input
     *
     * @param string $name url input name
     * @param mixed $value Value of url input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function url($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'url', $attrs);
    }

    /**
     * Tel Input
     *
     * @param string $name tel input name
     * @param mixed $value Value of tel input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function tel($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'tel', $attrs);
    }

    /**
     * Search Input
     *
     * @param string $name search input name
     * @param mixed $value Value of search input
     * @param array $attrs Attributes
     * @return string
     **/
    public static function search($name, $value = null, $attrs = array())
    {
        return static::input($name, $value, 'search', $attrs);
    }

    /**
     * Number Input
     *
     * @param string $name number input name
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @param int $value Default value
     * @param int $step Specific legal number intervals
     * @return string
     **/
    public static function number($name, $min, $max, $value = null, $step = null)
    {
        $attrs = array(
            'min' => $min,
            'max' => $max,
        );
        if ($step != null) {
            $attrs['step'] = $step;
        }
        return static::input($name, $value, 'number', $attrs);
    }

    /**
     * Submit button
     *
     * @param string $name Submit Button
     * @param mixed $value Value of Submit button
     * @param array $attrs Attributes
     * @return string
     **/
    public static function submit($name, $value, $attrs = array())
    {
        return static::input($name, $value, 'submit', $attrs);
    }

    /**
     * Reset button
     *
     * @param string $name Reset Button
     * @param mixed $value Value of Reset button
     * @param array $attrs Attributes
     * @return string
     **/
    public static function reset($name, $value, $attrs = array())
    {
        return static::input($name, $value, 'reset', $attrs);
    }

    /**
     * Button
     *
     * @param string $name Button Name
     * @param string $value Button Text
     * @param string $type Button Type
     * @param string $attrs Attributes
     * @return string
     **/
    public static function button($name, $text, $type = 'button', $attrs=array())
    {
        $attr = Html::buildAttributes($attrs);

        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';

        return '<button name="'.$name.'" type="'.$type.'"'.$attr.$id.'>'.$text.'</button>';
    }

    /**
     * Form Label
     *
     * @param string $for Label For
     * @param string $text Label Text
     * @return string
     **/
    public static function label($text, $for = null, $attrs=array())
    {
        $attr = Html::buildAttributes($attrs);

        $labelFor = ($for != null) ? ' for = "'.Str::sanitize($for, 'A-Za-z0-9-_').'"' : '';
        return '<label'.$labelFor.$attr.'>'.$text.'</label>';
    }

    /**
     * Radio Input
     *
     * @param string $name Radio Name
     * @param string $value Radio Value
     * @param boolean $checked Checked radio or not
     * @param array $attrs other attributes
     * @return string
     **/
    public static function radio($name, $value, $checked = false, $attrs = array())
    {
        if ($checked == true) {
           $checked = array('checked' => 'checked');
           $attrs = array_merge($attrs, $checked);
        }
        return static::input($name, $value, 'radio', $attrs);
    }

    /**
     * Radio Group
     *
     * @param string $name Radio input name
     * @param array $val_lab Value and Label for Radio input array('value' => 'label')
     * @param string $checkedVal default checked value for radio input
     * @return string
     **/
    public static function radioGroup($name, $val_lab = array(), $checkedVal = null)
    {
        $radios = '';
        foreach ($val_lab as $val => $lab) {
            $label = $name . '_' . $val;
            $attr = array('id' => $label);

            $checkedVal = static::getValue($name, $checkedVal);

            if (($val == $checkedVal) and (!is_null($checkedVal)) ) {
                $checked = true;
            } else {
                $checked = false;
            }

            $radios .= '<label for="'.$label.'">';
            $radios .= static::radio($name, $val, $checked, $attr);
            $radios .= $lab;
            $radios .= '</label>';
        }

        return $radios;
    }

    /**
     * Checkbox Input
     *
     * @param string $name Checkbox Name
     * @param string $value Checkbox Value
     * @param boolean $checked Checked checkbox or not
     * @param array $attrs other attributes
     * @return string
     **/
    public static function checkbox($name, $value, $checked = false, $attrs = array())
    {
        if($checked == true) {
            $checked = array('checked' => 'checked');
            $attrs = array_merge($attrs, $checked);
        }

        return static::input($name, $value, 'checkbox', $attrs);
    }

    /**
     * Checkbox Group
     *
     * @param string $name Checkbox Name
     * @param array $val_lab Checkbox Value and Label
     * @param array $checkVals Default Checked Values
     * @return string
     **/
    public static function checkGroup($name, $val_lab = array(), $checkVals = array())
    {
        $ckbox = '';
        $n = 1;
        foreach ($val_lab as $val => $lab) {
            $label = $name . '_' . $val;
            $attr = array('id' => $label);
            if (in_array($val, $checkVals)) {
                $checked = true;
            } else {
                $checked = false;
            }
            $real_name = $name.'['.$n.']';
            $ckbox .= '<label for="'.$label.'">';
            $ckbox .= static::checkbox($real_name, $val, $checked, $attr);
            $ckbox .= $lab;
            $ckbox .= '</label>';
            $n++;
        }

        return $ckbox;
    }

    /**
     * Oldies alies for checkGroup
     *
     **/
    public static function ckboxGroup($name, $val_lab = array(), $checkVals = array())
    {
        return static::checkGroup($name, $val_lab, $checkVals);
    }

    /**
     * TextArea
     *
     * @param string $name TextArea Name
     * @param string $value TextArea Value
     * @param array $attrs Attributes
     * @return string
     **/
    public static function textarea($name, $value = null, $attrs = array())
    {
        $attr = Html::buildAttributes($attrs);

        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';

        $value = static::getValue($name, $value);

        return '<textarea name="'.$name.'"'.$id.$attr.'>'.$value.'</textarea>';
    }

    /**
     * Select Box
     *
     * @param string $name Selectbox Name
     * @param array $options Select Options
     * @param boolean $multisel Enable multiple select
     * @param mixed $selected Default Selected Value
     * @param array $attrs Attributes
     * @return string
     **/
    public static function select($name, $options, $defaultSel = null, $attrs = array())
    {
        $attr = Html::buildAttributes($attrs);
        $id = (!isset($attrs['id'])) ? ' id = "'.Str::sanitize($name, 'A-Za-z0-9-_').'"' : '';

        $tag_name = $name;
        // Add [] for multi select
        if (isset($attrs['multiple'])) {
            if (! preg_match('/(\[.*\])/', $name)) {
                $tag_name = $name.'[]';
            }
        }

        $selbox = '<select name="'.$tag_name.'"'.$id.$attr.'>';
        $defaultSel = static::getValue($name, $defaultSel);

        foreach ($options as $val => $label) {
            if (is_array($label)) {
                $selbox .= '<optgroup label="'.$val.'">';
                foreach ($label as $key => $val) {
                    $selbox .= static::makeOptions($defaultSel,$key,$val);
                }
                $selbox .= '</optgroup>';
            } else {
                $selbox .= static::makeOptions($defaultSel,$val,$label);
            }

        }
        $selbox .= '</select>';

        return $selbox;
    }

    /**
     * Draft and Live Status Dropdown List
     *
     * @param string $name
     * @param mixed $value
     * @param array $attrs
     * @return string
     **/
    public static function status($name, $value = null, $attrs = array())
    {
        return static::select($name,
                            array('draft' => 'Draft', 'live' => 'Live'),
                            static::getValue($name, $value),
                            $attrs
                        );
    }

    /**
     * Country List Select
     *
     * @return string
     **/
    public static function countryList()
    {
        $config = Config::load('country');

        return static::select('country_list', $config[0]);
    }


    /**
     * Make output options
     *
     * @return string
     **/
    protected static function makeOptions($defaultSel, $key, $val)
    {
        if (is_array($defaultSel)) {
            $selected = (in_array($key, $defaultSel)) ? " selected=selected" : "";
        } else {
            $selected = ($key == $defaultSel) ? " selected=selected" : "";
        }

        return '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
    }

    /**
     * Magic method for static call.
     *
     * @return null|string
     **/
    public static function __callStatic($method, $args)
    {
        if(! isset(static::$extends['ext_'.$method])) {
            return null;
        }

        return call_user_func_array(static::$extends['ext_'.$method], $args);
    }

    /**
     * Get value for form element tag
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     **/
    protected static function getValue($name, $value = null)
    {
        $result = null;

        $result = static::getFromFlash($name);

        if ( is_null($result) and !is_null(static::$data) ) {
            $result = static::getFromData($name);
        }

        if(is_null($result)) return $value;

        return $result;
    }

    /**
     * Get element value from Flash::inputs()
     *
     * @param string $name
     * @return mixed
     **/
    protected static function getFromFlash($name)
    {
        if (is_null(static::$flashdata)) {
            static::$flashdata = Flash::getInputs();
        }

        if (isset(static::$flashdata[$name])) {
            return static::$flashdata[$name];
        }

        return null;
    }

    /**
     * Get element value from static::$data
     *
     * @param string $name
     * @return mixed
     **/
    protected static function getFromData($name)
    {
        if ( is_array(static::$data) ) {
            return array_get(static::$data, $name);
        }

        if ( is_object(static::$data) ) {
            if (strpos($name, '.')) {
                return static::getRelationData($name);
            }

            return static::$data->{$name};
        }

        return null;
    }

    /**
     * Get realtion object data.
     * This method is base on Laravel's object_get()
     *
     * @param string $name
     * @return mixed
     **/
    protected static function getRelationData($name)
    {
        $object = static::$data;

        foreach (explode('.', $name) as $segment)
        {
            $object = $object->{$segment};

            if (! is_object($object) ) {
                return $object;
            }
        }
    }

} // END class Form
