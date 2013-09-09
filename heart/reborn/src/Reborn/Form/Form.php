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
     * Extended Form Element Type
     *
     * @var array
     **/
    protected static $extends;

    /**
     * Variable for ckeditor js declare
     *
     * @var boolean
     **/
    protected static $ckeditor = false;

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
        // Skip $file
        if(is_array($file)) {
            $attrs = $file;
            $file = false;
        }

        if ($file == true && isset($attrs['enctype'])) {
            unset($attrs['enctype']);
        }
        $method = (isset($attrs['method'])) ? '' : ' method="post"';
        $attr = static::getAttr($attrs);
        if (!strstr($action,'://')) {
            $action = Uri::create($action);
        }
        $id = (!isset($attrs['id'])) ? ' id = "'.$name.'"' : '';
        $enctype = ($file == true) ? " enctype='multipart/form-data'" : "";

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
        $attr = static::getAttr($attrs);

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
        return static::input($name, null, 'checkbox', $attrs);
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
     * Get textarea with CkEditor
     *
     * @param string $name TextArea Name
     * @param string $value TextArea Value
     * @param string $type Ckeditor config type (mini, sample, normal)
     * @param array $attrs Attributes
     * @return string
     **/
    public static function ckeditor($name, $value = null, $type = 'normal', $attrs = array())
    {
        static::$ckeditor;

        $app = \Facade::getApplication();
        $ck = global_asset('js', 'ckeditor/ckeditor.js');
        $ck_jq = global_asset('js', 'ckeditor/adapters/jquery.js');
        $rb = rbUrl();
        $ad = \Setting::get('adminpanel');
        $jq = $rb.'global/assets/js/jquery-1.9.0.min.js';

        $ck_init = <<<ck
<script>
    if (SITEURL == 'undefined') {
        var SITEURL = '$rb';
        var ADMIN = '$ad';
    }
    window.jQuery || document.write('<script src="$jq"><\/script>')
</script>
$ck
$ck_jq
<script type="text/javascript">
    var instance;

    function update_instance()
    {
        instance = CKEDITOR.currentInstance;
    }
</script>
<script type="text/javascript">
(function($) {
    $(function()
    {
        $('textarea#wysiwyg-mini').ckeditor({
            skin : 'rb',
            toolbar: [
                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
              ],
            width: '97%',
            height: 100,
            dialog_backgroundCoverColor: '#000',
        });

        $('textarea#wysiwyg-simple').ckeditor({
            skin : 'rb',
            toolbar: [
                ['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink','-', 'Source']
              ],
            width: '97%',
            height: 200,
            dialog_backgroundCoverColor: '#000',
        });

        $('textarea#wysiwyg').ckeditor({
            skin : 'rb',
            theme : 'reborn',
            toolbar: [
                ['Maximize'],
                ['Image', 'Smiley'],
                ['Undo','Redo','-','Find','Replace'],
                ['Bold','Italic', 'Underline','Strike'],
                ['Link','Unlink'],
                ['Subscript','Superscript', 'NumberedList','BulletedList','Blockquote'],

                ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                ['ShowBlocks', 'RemoveFormat', 'Source','rbmedia'],
            ],
            extraPlugins: 'rbmedia',
            resize_dir: 'vertical',
            width: '97%',
            height: 400,
            dialog_backgroundCoverColor: '#000',
            removePlugins: 'elementspath',
        });
    });
})(jQuery);
</script>

ck;

        if (is_array($type)) {
            $type = 'normal';
            $attrs = $type;
        }

        switch ($type) {
            case 'mini':
                $id = array('id' => 'wysiwyg-mini');
                break;

            case 'simple':
                $id = array('id' => 'wysiwyg-simple');
                break;

            default:
                $id = array('id' => 'wysiwyg');
                break;
        }

        $attrs = array_merge($attrs, $id);

        if (static::$ckeditor) {
            return static::textarea($name, $value, $attrs);
        }

        // Make Wysiwyg is already used
        static::$ckeditor = true;

        return $ck_init.static::textarea($name, $value, $attrs);
    }

    /**
     * Helper for ckeditor mini
     *
     * @param string $name TextArea Name
     * @param string $value TextArea Value
     * @param array $attrs Attributes
     * @return string
     **/
    public static function ckmini($name, $value = null, $attrs = array())
    {
        return static::ckeditor($name, $value, 'mini', $attrs);
    }

    /**
     * Helper for ckeditor simple
     *
     * @param string $name TextArea Name
     * @param string $value TextArea Value
     * @param array $attrs Attributes
     * @return string
     **/
    public static function cksimple($name, $value = null, $attrs = array())
    {
        return static::ckeditor($name, $value, 'simple', $attrs);
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

} // END class Form
