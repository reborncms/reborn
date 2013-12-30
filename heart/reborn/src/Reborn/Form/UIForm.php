<?php

namespace Reborn\Form;

use Reborn\Config\Config;
use Reborn\Cores\Setting;
use Reborn\Cores\Facade;

/**
 * Advanced UI Element Form
 *
 * @package Reborn\Form
 * @author MyanmarLinks Professional Web Development Team
 **/
class UIForm extends Form
{

	/**
     * Variable for ckeditor js declare
     *
     * @var boolean
     **/
    protected static $ckeditor = false;

    /**
     * Variable for datepicker js declare
     *
     * @var boolean
     **/
    protected static $datepicker = false;

    /**
     * Variable for tag js declare
     *
     * @var boolean
     **/
    protected static $tag = false;

    /**
     * Variable for select2 js declare
     *
     * @var boolean
     **/
    protected static $select2 = false;

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

        $ck = global_asset('js', 'ckeditor/ckeditor.js');
        $ck_jq = global_asset('js', 'ckeditor/adapters/jquery.js');
        $rb = rbUrl();
        $ad = Setting::get('adminpanel');
        $jq = $rb.'global/assets/js/jquery-1.9.0.min.js';

        $toolbar = Config::get('ckconfig.toolbar');
        $mini_toolbar = Config::get('ckconfig.mini_toolbar');
        $simple_toolbar = Config::get('ckconfig.simple_toolbar');
        $extra = Config::get('ckconfig.extra');

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
        $('textarea.wysiwyg-mini').ckeditor({
            skin : 'reborn',
            toolbar: $mini_toolbar,
            width: '97%',
            height: 100,
            dialog_backgroundCoverColor: '#000',
        });

        $('textarea.wysiwyg-simple').ckeditor({
            skin : 'reborn',
            toolbar: $simple_toolbar,
            width: '97%',
            height: 200,
            dialog_backgroundCoverColor: '#000',
        });

        $('textarea.wysiwyg').ckeditor({
            skin : 'reborn',
            toolbar: $toolbar,
            extraPlugins: '$extra',
            resize_dir: 'vertical',
            width: '97%',
            height: 400,
            dialog_backgroundCoverColor: '#000'
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
                $id = array('class' => 'wysiwyg-mini');
                break;

            case 'simple':
                $id = array('class' => 'wysiwyg-simple');
                break;

            default:
                $id = array('class' => 'wysiwyg');
                break;
        }

        $attrs = array_merge($attrs, $id);
        $value = static::getValue($name, $value);

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
     * jQueryUI Datepicker field
     *
     * @param string $name Field name
     * @param string $value Field Value
     * @param string|arrau $format Date Picker Format (or) Options array
     * @param array $attrs Field attributes
     * @return string
     **/
    public static function datepicker($name, $value = null, $format = 'mm-dd-yy', $attrs = array())
    {
        if (!defined('ADMIN')) {
            $ui = global_asset('js', 'jqueryui/jquery-ui-1.10.3.custom.min.js');
            $ui_css = global_asset('css', 'jqueryui/stupid/jquery-ui-1.10.3.custom.css');
        } else {
            $ui = '';
            $ui_css = '';
        }
        $rb = rbUrl();
        $jq = $rb.'global/assets/js/jquery-1.9.0.min.js';

        $month = $year = false;

        if (is_array($format)) {
            $dateformat = isset($format['format']) ? $format['format'] : 'mm-dd-yy';
            $month = isset($format['month']) ? $format['month'] : false;
            $year = isset($format['year']) ? $format['year'] : false;
        } else {
            $dateformat = $format;
        }

        $options = 'dateFormat: "'.$dateformat.'"';

        if ($month) $options .= ', changeMonth: true';
        if ($year) $options .= ', changeYear: true';

        $dp_init = <<<dp
$ui_css
<script>
    window.jQuery || document.write('<script src="$jq"><\/script>')
</script>
$ui
<script type="text/javascript">
(function($) {
    $(function()
    {
        $( ".datepicker" ).datepicker({ $options });
    });
})(jQuery);
</script>

dp;
        $value = static::getValue($name, $value);
        $attrs = array_merge($attrs, array('class' => 'datepicker'));

        if (static::$datepicker) {
            return static::text($name, $value, $attrs);
        }

        // Make Datepicker is already used
        static::$datepicker = true;

        return $dp_init.static::text($name, $value, $attrs);
    }

    /**
     * Jquery Tagsinput field
     *
     * @param string $name Field name
     * @param string $value Field Value
     * @param array $attrs Field attributes
     * @param string $url Tag ajax URL. (Deafult is adminUrl('tag/autocomplete'))
     * @return string
     **/
    public static function tags($name, $value = null, $attrs = array(), $url = null)
    {
        $js = global_asset('js', 'jquery.tagsinput.min.js');
        $css = global_asset('css', 'jquery.tagsinput_custom.css');

        $rb = url();
        $jq = $rb.'global/assets/js/jquery-1.9.0.min.js';

        if (is_null($url)) {
        	$url = adminUrl('tag/autocomplete');
        }

        $tag_init = <<<TAG
$css
<script>
    window.jQuery || document.write('<script src="$jq"><\/script>')
</script>
$js
TAG;

$tag_script = <<<SCRIPT
<script type="text/javascript">
(function($) {
    $(function()
    {
        $('#$name').tagsInput({
            width:'auto',
            autocomplete_url: '$url'
        });
    });
})(jQuery);
</script>

SCRIPT;

        $attrs = array_merge($attrs, array('class' => 'tags'));

        $value = static::getValue($name, $value);
        $value = is_array($value) ? implode(',', $value) : $value;

        if (static::$tag) {
            return static::text($name, $value, $attrs).$tag_script;
        }

        static::$tag = true;

        return $tag_init.static::text($name, $value, $attrs).$tag_script;
    }

    /**
     * Select2 js dropdown field
     *
     * @param string $name Name of the select element
     * @param array $options Options tag data list for dropdown
     * @param mixed $value Value for select2 element
     * @param array $js_opts Options for select2 js script
     * @param boolean $multi Use multiple select.
     * @return string
     **/
    public static function select2($name, $options, $value = null, $js_opts = array(), $multi = false)
    {
        $js = global_asset('js', 'select2-3.4.5/select2.min.js');
        $css = global_asset('css', 'select2-3.4.5/select2.css');

        $rb = url();
        $jq = $rb.'global/assets/js/jquery-1.9.0.min.js';

        $e_attr = ($multi) ? array('multiple' => 'multiple') : array();

        $select2_opts = \Reborn\Util\ToolKit::jsEncode($js_opts);

        $select2_init = <<<SELECT
$css
<script>
    window.jQuery || document.write('<script src="$jq"><\/script>')
</script>
$js
SELECT;

$select_script = <<<SCRIPT
<script type="text/javascript">
(function($) {
    $(function()
    {
        $('#$name').select2($select2_opts);
    });
})(jQuery);
</script>

SCRIPT;
        $value = static::getValue($name, $value);
        $element = static::select($name, $options, $value, $e_attr);

        if (static::$select2) {
            return $element.$select_script;
        }

        static::$select2 = true;

        return $select2_init.$element.$select_script;
    }

    /**
     * Select2 js dropdown field with multi select
     *
     * @param string $name Name of the select element
     * @param array $options Options tag data list for dropdown
     * @param mixed $value Value for select2 element
     * @param array $js_opts Options for select2 js script
     * @return string
     **/
    public static function select2Multi($name, $options, $value = null, $js_opts = array())
    {
        return static::select2($name, $options, $value, $js_opts, true);
    }

} // END class UIForm extends Form
