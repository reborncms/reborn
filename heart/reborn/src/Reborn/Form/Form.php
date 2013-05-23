<?php

namespace Reborn\Form;

use Reborn\Http\Uri;
use Reborn\Config\Config;

/**
 * Form class for Reborn CMS
 *
 * @package Reborn\Form
 * @author Reborn CMS Development Team
 **/
class Form
{
    /**
     * Open Form element
     *
     * @return string
     * @param string $action Form Action
     * @param string $name Form Name
     * @param boolean $file if true enctype='multipart/form-data will be added'
     * @param string $attrs Form Attributes
     **/
    public static function start($action = '', $name = null, $file = false, $attrs=array())
    {
        if ($file == true && isset($attrs['enctype'])) {
            unset($attrs['enctype']);
        }
        $method = (isset($attrs['method'])) ? '' : ' method="post"';
        $attr = static::getAttr($attrs);
        if (!strstr($action,'://')) {
            $action = Uri::create($action);
        }
        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';
        $enctype = ($file == true) ? "enctype='multipart/form-data'" : "";

        return '<form name="'.$name.'"'.$method.' action="'.$action.'"'.$id.$attr.$enctype.'>';
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
        $attr = static::getAttr($attrs);
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
     * Input Field
     *
     * @return string
     * @param string $name Input Field Name
     * @param string $type Input Field Type
     * @param string $attrs Attributes
     **/
    public static function input($name, $value = null, $type = 'text', $attrs = array())
    {
        $attr = static::getAttr($attrs);

        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';

        $val = ($value != null) ? ' value = "'.$value.'"' : '';

        return '<input type="'.$type.'" name="'.$name.'"'.$id.$val.$attr.' />';

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
        $attr = static::getAttr($attrs);

        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';

        return '<button name="'.$name.'" type="'.$type.'"'.$attr.'>'.$id.$text.'</button>';
    }

    /**
     * Form Label
     *
     * @param string $for Label For
     * @param string $text Label Text
     * @return string
     **/
    public static function label ($text, $for = null)
    {
        $labelFor = ($for != null) ? ' for = "'.$for.'"' : '';
        return '<label'.$labelFor.'>'.$text.'</label>';
    }

    /**
     * Radio Input
     *
     * @param string $name Radio Name
     * @param string $value Radio Value
     * @param array $attrs other attributes
     * @return string
     **/
    public static function radio($name, $value, $attrs = array())
    {
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

            if (($val == $checkedVal) and (!is_null($checkedVal)) ) {
                $checked = array('checked' => 'checked');
                $attr = array_merge($attr, $checked);
            }

            $radios .= '<label for="'.$label.'">';
            $radios .= static::radio($name, $val, $attr);
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
     * @param array $attrs other attributes
     * @return string
     **/
    public static function checkbox($name, $value, $attrs = array())
    {
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
    public static function ckboxGroup($name, $val_lab = array(), $checkVals = array())
    {
        $ckbox = '';
        $n = 1;
        foreach ($val_lab as $val => $lab) {
            $label = $name . '_' . $val;
            $attr = array('id' => $label);
            if (in_array($val, $checkVals)) {
                $checked = array('checked' => 'checked');
                $attr = array_merge($attr, $checked);
            }
            $real_name = $name.'['.$n.']';
            $ckbox .= '<label for="'.$label.'">';
            $ckbox .= static::checkbox($real_name, $val, $attr);
            $ckbox .= $lab;
            $ckbox .= '</label>';
            $n++;
        }

        return $ckbox;
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
        $attr = self::getAttr($attrs);

        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';

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
    public static function select ($name, $options, $defaultSel = null, $attrs = array())
    {
        $attr = static::getAttr($attrs);
        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';
        $selbox = '<select name="'.$name.'"'.$id.$attr.'>';
        foreach ($options as $val => $label) {
            if (is_array($label)) {
                $selbox .= '<optgroup label="'.$val.'">';
                foreach ($label as $key => $val) {
                    $selbox .= static::outOpt($defaultSel,$key,$val);
                }
                $selbox .= '</optgroup>';
            } else {
                $selbox .= static::outOpt($defaultSel,$val,$label);
            }

        }
        $selbox .= '</select>';

        return $selbox;
    }

    /**
     * Country List Select
     *
     * @return string
     **/
    public static function CountryList()
    {
        $config = Config::load('country');

        return static::select('country_list', $config[0]);
    }


    /**
     * output options
     *
     * @return string
     **/
    protected static function outOpt($defaultSel,$key,$val)
    {
        if (is_array($defaultSel)) {
            $selected = (in_array($key, $defaultSel)) ? " selected=selected" : "";
        } else {
            $selected = ($key == $defaultSel) ? " selected=selected" : "";
        }

        return '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
    }

    /**
     * Get attributes
     *
     * @return string
     **/
    protected static function getAttr($attrs)
    {
        $attr_str = '';
        foreach ($attrs as $key => $val) {
            $attr_str .= ' ';
            $attr_str .= $key.'="'.$val.'"';
        }

        return $attr_str;
    }
} // END class Form
