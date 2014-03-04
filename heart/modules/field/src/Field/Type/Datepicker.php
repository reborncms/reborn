<?php

namespace Field\Type;

/**
 * Datepicker Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Datepicker extends \Field\AbstractType
{

    public function filler($default = null, $options = null)
    {
        $formats = array(
                'mm/dd/yy' => 'mm/dd/yy',
                'd M, y' => 'd M, y',
                'd MM, y' => 'd MM, y',
                'DD, d MM, yy' => 'DD, d MM, yy'
            );
        $field = \Form::select('options[select]', $formats, $options);
        $custom = \Form::text('options[custom]');
        $f = <<<FIELD
        <p class="info">
            You can choose dateformat form select or type custom format.
        </p>
        <label for="text-options">Choose Date Format</label>
        <div class="form-right-block">
        $field
        <p class="info">
            Select Date Format for jQueryUI Datepicker
        </p>
        </div>
        <label for="text-options">Custom Date Format</label>
        <div class="form-right-block">
        $custom
        <p class="info">
            Enter Date Format for jQueryUI Datepicker
        </p>
        </div>
FIELD;

        return $f;
    }

    public function displayForm($field, $value = null)
    {
        $key = $field->field_slug;
        $label = \Form::label($field->field_name, $key);
        $info = $this->makeInfo($field->description);

        $value = $value ? $value : $this->getValue($key, $field->default);
        $format = $this->makeFormat($field->options);
        $element = \Form::datepicker($key, $value, $format);

        $f = <<<FORM
        <div class="form-block">
            $label

            <div class="form-right-block">
                $element
                $info
            </div>
        </div>
FORM;

        return $f;
    }

    /**
     * Make Datepicker format from options
     *
     * @param  string $options Datepicker Field options
     * @return string
     **/
    protected function makeFormat($options)
    {
        $options = json_decode($options);

        if (isset($options->custom) and ('' != $options->custom)) {
            $format = $options->custom;
        } else {
            $format = $options->select;
        }

        return  $format;
    }

} // END class Datepicker extends \Field\AbstractType
