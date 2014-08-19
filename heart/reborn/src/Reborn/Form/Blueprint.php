<?php

namespace Reborn\Form;

use Reborn\Filesystem\File;

/**
 * FormBuilder Blueprint class for Reborn.
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development Team
 **/

class Blueprint
{

    /**
     * Form name
     *
     * @var string
     **/
    protected $name;

    /**
     * Form strat variable
     *
     * @var string
     **/
    protected $start;

    /**
     * Form element fields
     *
     * @var array
     **/
    protected $fileds = array();

    /**
     * External hidden data
     *
     * @var array
     **/
    protected $hiddens = array();

    /**
     * Form submit buttons
     *
     * @var array
     **/
    protected $submit = array();

    /**
     * Form reset button
     *
     * @var string
     **/
    protected $reset;

    /**
     * Cancel button for form
     *
     * @var string
     **/
    protected $cancel;

    /**
     * Form legend value
     *
     * @var string
     **/
    protected $legend;

    /**
     * Form validation errors
     *
     * @var array
     **/
    protected $errors = array();

    /**
     * Form label array
     *
     * @var array
     **/
    protected $labels = array();

    /**
     * Extend Form Elements array
     *
     * @var array
     **/
    protected $exElements = array();

    /**
     * Prepend Fields or HTML
     *
     * @var array
     **/
    protected $prepends = array();

    /**
     * Append Fields or HTML
     *
     * @var array
     **/
    protected $appends = array();

    /**
     * Instance method for Blueprint
     *
     * @param  string  $action   Form action url
     * @param  string  $name     Form name
     * @param  boolean $file     Use multipart/form-data
     * @param  array   $attrs    Form attributes
     * @param  boolean $honeypot Honeypot field use or not
     * @return void
     **/
    public function __construct($action, $name, $file, $attrs, $honeypot)
    {
        $default = \Config::get('form.default');
        $this->template = \Config::get('form.templates.'.$default);

        $this->start($action, $name, $file, $attrs, $honeypot);

        // Get the register element field type form config/form.php
        $elements = \Config::get('form.elements');

        foreach ($elements as $name => $v) {
            $this->addElement($name, $v);
        }

        // Add event binding for extraElement
        // This is easy to add extraElement from module
        if (\Event::has('reborn.form.generator.addExtraField')) {
            $results = \Event::call('reborn.form.generator.addExtraField');
            foreach ($results as $result) {
                foreach ($result as $name => $value) {
                    $this->addElement($name, $value);
                }

            }
        }
    }

    /**
     * Add new Element for the FormBuilder
     *
     * @param  string $name         Type name
     * @param  array  $classAndPath Element Class Name and File Path
     * @return void
     **/
    public function addElement($name, $classAndPath)
    {
        $this->exElements[$name]['class'] = $classAndPath[0];
        $this->exElements[$name]['path'] = $classAndPath[1];
    }

    /**
     * Set external hidden fields for Form Build
     *
     * @param  array   $data  Hidden data array
     * @param  boolean $merge Merge with original hiddens data. Default is false
     * @return void
     **/
    public function setHiddens(array $data, $merge = false)
    {
        if ($merge) {
            $this->hiddens = array_merge($this->hiddens, $data);
        } else {
            if (!empty($data)) {
                $this->hiddens = $data;
            }
        }
    }

    /**
     * Set the form template.
     *
     * @param  string $file Form template file name with path
     * @return void
     **/
    public function setTemplate($file)
    {
        if (File::is($file)) {
            $this->template = $file;
        }
    }

    /**
     * Set the form validation errors
     *
     * @param  array $errs Validation error array
     * @return void
     **/
    public function setErrors($errs = array())
    {
        $this->errors = $errs;
    }

    /**
     * Set Prepend Field or UI
     *
     * @param  string       $name
     * @param  array|string $content
     * @return void
     **/
    public function setPrepend($name, $content)
    {
        $this->prepends[$name] = $content;
    }

    /**
     * Set Append Field or UI
     *
     * @param  string       $name
     * @param  array|string $content
     * @return void
     **/
    public function setAppend($name, $content)
    {
        $this->appends[$name] = $content;
    }

    /**
     * Check Prepend field or ui
     *
     * @param  string  $name
     * @return boolean
     **/
    public function hasPrepend($name)
    {
        return isset($this->prepends[$name]);
    }

    /**
     * Check Append field or ui
     *
     * @param  string  $name
     * @return boolean
     **/
    public function hasAppend($name)
    {
        return isset($this->appends[$name]);
    }

    /**
     * Make Prepend field or ui
     *
     * @param  string  $name
     * @return boolean
     **/
    public function makePrepend($name)
    {
        if (! $this->hasPrepend($name)) {
            return null;
        }

        return $this->prepends[$name];
    }

    /**
     * Make Append field or ui
     *
     * @param  string  $name
     * @return boolean
     **/
    public function makeAppend($name)
    {
        if (! $this->hasAppend($name)) {
            return null;
        }

        return $this->appends[$name];
    }

    /**
     * Build the form.
     *
     * @param  array   $hiddenData External Hidden Data
     * @param  boolean $merge      Merge with original hiddens data. Default is false
     * @return string
     **/
    public function build($hiddenData = array(), $merge = false)
    {
        $this->setHiddens($hiddenData, $merge);

        ob_start();

        include $this->template;

        return ob_get_clean();
    }

    /**
     * Render form start and form end.
     *
     * @param  string  $action   Form action url
     * @param  string  $name     Form name
     * @param  string  $file     Use multipart/form-data or not
     * @param  array   $attrs    Form attribute array
     * @param  boolean $honeypot
     * @return void
     **/
    public function start($action, $name, $file, $attrs, $honeypot)
    {
        $this->start = Form::start($action, $name, $file, $attrs);

        if ($honeypot) {
            $this->start .= Form::honeypot();
        }
    }

    /**
     * Add submit button for form.
     *
     * @param  array $submits Submit button value array
     * @return void
     **/
    public function addSubmit($submits)
    {
        foreach ($submits as $name => $value) {
            $name = isset($value['name']) ? $value['name'] : $name;
            $attrs = isset($value['attr']) ? $value['attr'] : array();

            // Set Btn Class From Reborn Admin Theme
            if (!isset($attrs['class'])) {
                $attrs['class'] = 'btn btn-default btn-green';
            }

            $this->submit[] = Form::submit($name, $value['value'], $attrs);
        }
    }

    /**
     * Add reset button for form.
     *
     * @param  array $val Reset button value array
     * @return void
     **/
    public function addReset($val)
    {
        $name = isset($val['name']) ? $val['name'] : 'reset';
        $attrs = isset($val['attr']) ? $val['attr'] : array();
        $this->reset = Form::reset($name, $val['value'], $attrs);
    }

    /**
     * Add cancel button for form.
     *
     * @param  array $val Cancle value array
     * @return void
     **/
    public function addCancel($val)
    {
        $name = isset($val['name']) ? $val['name'] : 'Cancel';
        $class = isset($val['class']) ? $val['class'] : '';
        $id = isset($val['id']) ? $val['id'] : '';

        $this->cancel = '<a href="'.url($val['url']).'" class="'.$class.'" id="'.$id.'" >'.$name.'</a>';
    }

    /**
     * Add form legend.
     *
     * @param  string $val Legend value
     * @return void
     **/
    public function addLegend($val)
    {
        $this->legend = $val;
    }

    /**
     * Set data provider model to Form
     *
     * @param  mixed $provider
     * @return void
     **/
    public function setProvider($provider)
    {
        Form::provider($provider);
    }

    /**
     * Render the Form Element.
     *
     * @param  string $method Method name
     * @param  string $name   Field name
     * @param  array  $value  Field value array
     * @return void
     **/
    public function render($method, $name, $value)
    {
        // First step, search the type for the exElements and render
        if (array_key_exists($method, $this->exElements)) {
            $this->fields[$name]['type'] = $method;
            $this->fields[$name]['info'] = $value['info'];
            $this->labels[$name] = $this->getLabelHtml($value['label'], $name);

            if ( ! class_exists($this->exElements[$method]['class']) ) {
                require $this->exElements[$method]['path'];
            }

            $callback = new $this->exElements[$method]['class']();

            if ($callback instanceof BuilderElementInterface) {
                $this->fields[$name]['html'] = $callback->render($name, $value);
            }
        } else {
            $method = 'add'.ucfirst($method);

            $this->$method($name, $value);
        }
    }

    /** Lists for Normal Input Field **/
    protected function addText($name, $val)
    {
        $this->addInput($name, $val, 'text');
    }

    protected function addPassword($name, $val)
    {
        $this->addInput($name, $val, 'password');
    }

    protected function addHidden($name, $val)
    {
        $this->addInput($name, $val, 'hidden');
    }

    protected function addFile($name, $val)
    {
        if (isset($val['multiple']) and $val['multiple']) {
            $val['attr']['multiple'] = true;
            $name = $name.'[]';
        }
        $val['value'] = null;
        $this->addInput($name, $val, 'file');
    }

    protected function addEmail($name, $val)
    {
        $this->addInput($name, $val, 'email');
    }

    protected function addUrl($name, $val)
    {
        $this->addInput($name, $val, 'url');
    }

    protected function addTel($name, $val)
    {
        $this->addInput($name, $val, 'tel');
    }

    protected function addSearch($name, $val)
    {
        $this->addInput($name, $val, 'search');
    }

    protected function addRadio($name, $val)
    {
        $this->addInput($name, $val, 'radio');
    }

    protected function addRadioGroup($name, $val)
    {
        //$this->addInput($name, $val, 'radio');
        $this->fields[$name]['type'] = 'radioGroup';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        if (is_string($val['radio_label'])) {
            $val['radio_label'] = (array) $val['radio_label'];
        }
        $this->fields[$name]['html'] = Form::radioGroup($name,
                                        $val['radio_label'], $val['value']);
    }

    protected function addCheckbox($name, $val)
    {
        $this->addInput($name, $val, 'checkbox');
    }

    protected function addCheckboxGroup($name, $val)
    {
        $this->fields[$name]['type'] = 'checkboxGroup';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        if (is_string($val['checkbox_label'])) {
            $val['checkbox_label'] = (array) $val['checkbox_label'];
        }
        if (is_null($val['value'])) {
            $val['value'] = array();
        }

        $this->fields[$name]['html'] = Form::checkGroup($name, $val['checkbox_label'], $val['value']);
    }

    protected function addInput($name, $val, $type)
    {
        $this->fields[$name]['type'] = $type;
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = Form::input($name, $val['value'], $type, $val['attr']);
    }

    /** Textarea Field **/
    protected function addTextarea($name, $val)
    {
        $this->fields[$name]['type'] = 'textarea';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = Form::textarea($name, $val['value'], $val['attr']);
    }

    /** CkEditor Field **/
    protected function addCkeditor($name, $val)
    {
        $this->fields[$name]['type'] = 'ckeditor';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = UIForm::ckeditor($name, $val['value'], 'normal', $val['attr']);
    }

    /** CkEditor Mini Field **/
    protected function addCkmini($name, $val)
    {
        $this->fields[$name]['type'] = 'ckeditor';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = UIForm::ckeditor($name, $val['value'], 'mini', $val['attr']);
    }

    /** CkEditor Simple Field **/
    protected function addCksimple($name, $val)
    {
        $this->fields[$name]['type'] = 'ckeditor';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = UIForm::ckeditor($name, $val['value'], 'simple', $val['attr']);
    }

    /** DatePicker Field **/
    protected function addDatepicker($name, $val)
    {
        $format = isset($val['format']) ? $val['format'] : 'mm-dd-yy';
        $this->fields[$name]['type'] = 'datepicker';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = UIForm::datepicker($name, $val['value'], $format, $val['attr']);
    }

    /** Tag Field **/
    protected function addTags($name, $val)
    {
        $this->fields[$name]['type'] = 'tags';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $url = isset($val['url']) ? $val['url'] : null;
        $this->fields[$name]['html'] = UIForm::tags($name, $val['value'], $val['attr'], $url);
    }

    /** Country List Field **/
    protected function addCountryList($name, $val)
    {
        $this->fields[$name]['type'] = 'countryList';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = Form::CountryList($name, $val['value']);
    }

    /** Select Field **/
    protected function addSelect($name, $val)
    {
        $options = isset($val['option']) ? $val['option'] : array();

        $this->fields[$name]['type'] = 'select';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = Form::select($name, $options, $val['value'], $val['attr']);
    }

    /** Select Box with Select2 JS **/
    protected function addSelect2($name, $val)
    {
        $options = isset($val['option']) ? $val['option'] : array();

        $this->fields[$name]['type'] = 'select2';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $multi = (isset($val['multi'])) ? $val['multi'] : false;
        $ajax = (isset($val['ajax'])) ? $val['ajax'] : false;
        $js_opts = (isset($val['js_opts'])) ? $val['js_opts'] : array();
        $this->fields[$name]['html'] = UIForm::select2($name, $options, $val['value'], $js_opts, $multi, $ajax, $val['attr']);
    }

    /** Select Box with Select2 JS with Multi-select **/
    protected function addSelect2Multi($name, $val)
    {
        $options = isset($val['option']) ? $val['option'] : array();

        $this->fields[$name]['type'] = 'select2Multi';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $js_opts = (isset($val['js_opts'])) ? $val['js_opts'] : array();
        $this->fields[$name]['html'] = UIForm::select2Multi($name, $options, $val['value'], $js_opts, $val['attr']);
    }

    /** Select Box with Select2 JS with Ajax **/
    protected function addSelect2Ajax($name, $val)
    {
        $this->fields[$name]['type'] = 'select2Ajax';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $js_opts = (isset($val['js_opts'])) ? $val['js_opts'] : array();
        $multi = (isset($val['multi'])) ? $val['multi'] : false;
        $this->fields[$name]['html'] = UIForm::select2Ajax($name, $val['url'], $val['value'], $js_opts, $multi, $val['attr']);
    }

    /** Number Field **/
    protected function addNumber($name, $val)
    {
        if (!isset($val['min']) and !isset($val['max'])) {
            throw new \LogicException("Min and Max are require!");
        }
        $step = isset($val['step']) ? $val['step'] : null;
        $this->fields[$name]['type'] = 'number';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = Form::number($name, $val['min'], $val['max'], $val['value'], $step);
    }

    /** YesNo Radio Box **/
    protected function addYesno($name, $val)
    {
        $this->fields[$name]['type'] = 'yesno';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = $this->getLabelHtml($val['label'], $name);
        $this->fields[$name]['html'] = Form::radioGroup($name, array(
                                        '1' => 'Yes',
                                        '0' => 'No'
                                        ), $val['value']);
    }

    /**
     * Add Form Button.
     */
    protected function addButton($name, $val)
    {
        $this->fields[$name]['type'] = 'button';
        $this->fields[$name]['info'] = $val['info'];
        $this->labels[$name] = '';
        $type = isset($val['btn_type']) ? $val['btn_type'] : 'buttton';
        $this->fields[$name]['html'] = Form::button($name, $val['label'], $type, $val['attr']);
    }

    /**
     * Get Html string for Form label tag.
     *
     * @param string $label
     * @param string $for
     * @return string
     **/
    protected function getLabelHtml($label, $for)
    {
        if ( is_null($label) ) return '';
        return Form::label($label, $for, array('class' => 'control-label'));
    }
}
