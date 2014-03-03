<?php

namespace Reborn\Form;

use Reborn\Http\Uri;
use Reborn\Http\Input;
use Reborn\Util\Str;

/**
 * FormBuilder class for Reborn.
 *
 * @package Reborn\Form
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class AbstractFormBuilder
{
    /**
     * Name of form. You can define multiple form with name.
     *
     * @var string
     **/
    protected $name;

    /**
     * Form name or Form process mode
     *
     * @var string
     **/
    protected $mode = 'create';

    /**
     * Use multipart/form-data
     *
     * @var boolean
     **/
    protected $file = false;

    /**
     * Use Honey pot field or not
     *
     * @var boolean
     **/
    protected $honeypot = false;

    /**
     * Form legend string
     *
     * @var name
     **/
    protected $legend;

    /**
     * Field list array for form
     *
     * @var array
     **/
    protected $fields = array();

    /**
     * Submit button data array
     *
     * @var array
     **/
    protected $submit = array('submit' => array('value' => 'Submit'));

    /**
     * Reset button data array
     *
     * @var array
     **/
    protected $reset = array();

    /**
     * Cancel <a> tag data array
     *
     * @var array
     **/
    protected $cancel = array();

    /**
     * Form builder object
     *
     * @var Reborn\Form\Blueprint
     **/
    protected $builder;

    /**
     * Form validtion object
     *
     * @var Reborn\Form\Validation
     **/
    protected $validator;

    /**
     * Eloquent Model Object for form field value
     *
     * @var Eloquent
     **/
    protected $model = false;

    /**
     * Skip fields when saving to the model
     *
     * @var string
     **/
    protected $skipFields = array();

    /**
     * Static method to create new form Builder class
     *
     * @param  string                           $action From action URL
     * @param  string                           $name   Form name
     * @param  string                           $attrs  Form Attributes
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public static function create($action = '', $name = 'default', $attrs = array())
    {
        $class = get_called_class();

        return new $class($action, $name, $attrs);
    }

    /**
     * Creat the FormBuilder Object.
     *
     * @param  string|null $action From action URL
     * @param  string      $name   Form name
     * @param  string      $attrs  Form Attributes
     * @return void
     **/
    public function __construct($action = null, $name = 'default', $attrs = array())
    {
        $this->name = $name;

        // Hook for elements setter
        if (method_exists($this, 'setFields')) {
            $this->setFields($name);
        }

        if (Str::isBlank($action)) {
            $action = Uri::current();
        }

        $this->builder = new Blueprint($action, $name, $this->file, $attrs, $this->honeypot);
    }

    /**
     * Set Form Process Mode
     *
     * @param  string                           $mode
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function mode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Set legend string
     *
     * @param  string                           $legend Legend string
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Set form element field.
     *
     * @param  string                           $name    Form element field name
     * @param  string                           $type    Field type (eg: text)
     * @param  string|null                      $rules   Field rule string
     * @param  array                            $options Other field element attributes
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function field($name, $type, $rules = null, $options = array())
    {
        $data = array(
            'type' => $type,
            'rule' => $rules
        );

        // Field order after exists field name
        if (isset($options['after'])) {
            $after = $options['after'];
            unset($options['after']);
        }

        // Field order before exists field name
        if (isset($options['before'])) {
            $before = $options['before'];
            unset($options['before']);
        }

        $data = array_merge($data, $options);

        if ( isset($after) ) {
            return $this->fieldAfter($after, $name, $data);
        } elseif ( isset($before) ) {
            return $this->fieldBefore($before, $name, $data);
        }

        $this->fields[$name] = $data;

        return $this;
    }

    /**
     * Set form element field after "$after" field.
     *
     * @param  string                           $after
     * @param  string                           $name
     * @param  array                            $field_data
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function fieldAfter($after, $name, array $field_data)
    {
        if (! isset($this->fields[$after]) ) {
            $this->fields[] = $field_data;
        } else {
            $this->fieldReOrdering('after', $after, $name, $field_data);
        }

        return $this;
    }

    /**
     * Set form element field before "$before" field.
     *
     * @param  string                           $before
     * @param  string                           $name
     * @param  array                            $field_data
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function fieldBefore($before, $name, array $field_data)
    {
        if (! isset($this->fields[$before]) ) {
            $this->fields[] = $field_data;
        } else {
            $this->fieldReOrdering('before', $before, $name, $field_data);
        }

        return $this;
    }

    /**
     * Reorder field order with given "type".
     *
     * @param  string $type       Reorder type (before || after)
     * @param  string $check      Exists field name to check
     * @param  string $name       New insert field's name
     * @param  array  $field_data New insert field's data array
     * @return void
     **/
    protected function fieldReOrdering($type, $check, $name, $field_data)
    {
        $reorder = array();

        foreach ($this->fields as $field => $data) {
            if ($check === $field) {
                if ('before' === $type) {
                    $reorder[$name] = $field_data;
                    $reorder[$field] = $data;
                } else {
                    $reorder[$field] = $data;
                    $reorder[$name] = $field_data;
                }
            } else {
                $reorder[$field] = $data;
            }
        }

        $this->fields = $reorder;
    }

    /**
     * Prepend the ui before $name field
     *
     * @param  string                           $name
     * @param  array|string                     $contnet
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function prepend($name, $content)
    {
        $this->builder->setPrepend($name, $content);

        return $this;
    }

    /**
     * Append the ui before $name field
     *
     * @param  string                           $name
     * @param  array|string                     $contnet
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function append($name, $content)
    {
        $this->builder->setAppend($name, $content);

        return $this;
    }

    /**
     * Check the form is valid or not
     *
     * @return boolean
     **/
    public function valid()
    {
        // Check for method is POST
        if (! Input::isPost()) {
            return false;
        }

        $this->prepareValidation();

        if ($this->validator->valid()) {
            return true;
        }

        // Save Old data in Flash
        Input::capture();

        $this->builder->setErrors($this->validator->getErrors());

        return false;
    }

    /**
     * Set external hidden fields for Form Build
     *
     * @param  array                            $data  Hidden data array
     * @param  boolean                          $merge Merge with original hiddens data. Default is false
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function setHiddens(array $data, $merge = false)
    {
        $this->builder->setHiddens($data, $merge);

        return $this;
    }

    /**
     * Build the form. Final step :D
     *
     * @param  array   $hiddenData External Hidden Data
     * @param  boolean $merge      Merge with original hiddens data. Default is false
     * @return string
     **/
    public function build($hiddenData = array(), $merge = false)
    {
        if (!$this->assignToBuilder()) {
            return null;
        }

        return $this->builder->build($hiddenData, $merge);
    }

    /**
     * Change the form field's value
     *
     * @param  string                           $name Field name
     * @param  string                           $key  Field's key name
     * @param  mixed                            $val  Value for field's key
     * @return \Reborn\Form\AbstractFormBuilder
     */
    public function changeValue($name, $key, $val)
    {
        if (isset($this->fields[$name])) {
            $this->fields[$name][$key] = $val;
        }

        return $this;
    }

    /**
     * Set the form template
     *
     * @param  string                           $file Template file path
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function template($file)
    {
        $this->builder->setTemplate($file);

        return $this;
    }

    /**
     * Set the data provider model object for form
     *
     * @param  array|object                     $model Data Model
     * @return \Reborn\Form\AbstractFormBuilder
     **/
    public function provider($model)
    {
        if (!is_array($model) and !is_object($model)) {
            throw new \InvalidArgumentException("Model must be array or object");
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set the model object for form.
     * Alias of $this->provider()
     *
     **/
    public function setModel($model)
    {
        return $this->provider($model);
    }

    /**
     * Get the model object
     *
     * @return mixed
     **/
    public function getProvider()
    {
        if (!$this->model) return null;

        if (is_string($this->model)) {
            $model = $this->model;
            $this->model = new $model;
        }

        return $this->model;
    }

    /**
     * Alias of $this->getProvider()
     *
     **/
    public function getModel()
    {
        return $this->getProvider();
    }

    /**
     * Hook function for pre saving the model data.
     *
     * @return void
     **/
    protected function preSave() {}

    /**
     * Save the current model object
     *
     * @return boolean
     **/
    public function save()
    {
        if (! $this->model) return false;

        $model = $this->getModel();

        foreach ($this->fields as $name => $value) {

            if (in_array($name, $this->getSkipFields())) {
                continue;
            }
            $model->$name = Input::get($name);
        }

        // Call the Pre save hook
        $this->preSave();

        if ($model->save(array(), false)) {
            return true;
        }

        return false;
    }

    /**
     * Get skip fields array
     *
     * @return array
     **/
    protected function getSkipFields()
    {
        $btns = array();
        foreach ($this->submit as $n => $v) {
            $name = isset($v['name']) ? $v['name'] : $n;
            $btns[$name] = $name;
        }

        $btns['reset'] = isset($this->reset['name']) ? $this->reset['name'] : 'reset';

        return array_merge($this->skipFields, $btns);
    }

    /**
     * Assign the Form fields to Builder
     *
     * @return boolean
     */
    protected function assignToBuilder()
    {
        if (empty($this->fields)) {
            return false;
        }

        if ($this->model) {
            $this->builder->setProvider($this->model);
        }

        // Add the Fileds
        foreach ($this->fields as $name => $attrs) {

            $attrs['label'] = isset($attrs['label']) ? $attrs['label'] : '';
            $attrs['info'] = isset($attrs['info']) ? $attrs['info'] : '';
            $attrs['attr'] = isset($attrs['attr']) ? $attrs['attr'] : array();

            // For Radio Group
            if ('radioGroup' === $attrs['type']) {
                $attrs['radio_label'] = array();

                if ( isset($attrs['radio_label']) ) {
                    $attrs['radio_label'] = $attrs['radio_label'];
                }
            }

            $attrs['value'] = isset($attrs['value']) ? $attrs['value'] : null;

            $this->builder->render($attrs['type'], $name, $attrs);
        }

        $this->assignFormButtons();

        return true;
    }

    /**
     * Assign Form action buttons to Builder
     *
     * @return void
     **/
    protected function assignFormButtons()
    {
        $actionMode = $this->mode.'Actions';
        if (method_exists($this, $actionMode)) {
            list($submit, $reset, $cancel) = $this->{$actionMode}();
        } else {
            $submit = $this->submit;
            $reset = $this->reset;
            $cancel = $this->cancel;
        }

        $this->builder->addSubmit($submit);

        if (!empty($this->reset)) {
            $this->builder->addReset($reset);
        }

        if (!empty($this->cancel)) {
            $this->builder->addCancel($cancel);
        }

        if (!empty($this->legend)) {
            $this->builder->addLegend($this->legend);
        }
    }

    /**
     * Set form validation
     *
     * @return void
     */
    protected function prepareValidation()
    {
        //
        foreach ($this->fields as $name => $val) {
            // Set validation
            if (isset($val['rule'])) {
                $this->rules[$name] = $val['rule'];
            }
        }

        if ($this->honeypot) {
            $this->rules['honey_pot'] = 'honeypot';
        }

        $this->validator = new Validation(Input::get('*'), $this->rules);
    }

} // END class FormBuilder
