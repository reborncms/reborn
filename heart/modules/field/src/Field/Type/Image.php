<?php

namespace Field\Type;

/**
 * Image Upload and Set Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Image extends \Field\AbstractType
{

    public function filler($default = null, $options = null)
    {
        $options = json_decode($options);

        $width = isset($options->width) ? $options->width : '';
        $add = isset($options->add) ? $options->add : '';
        $remove = isset($options->remove) ? $options->remove : '';

        $f = <<<FIELD
        <label for="img_width">Thumbnail Width</label>
        <div class="form-right-block">
        <input type="text" id="img_width" name="options[width]" value="$width">
        <p class="info">
            Thumbnail width to show in form
        </p>
        </div>
        <label for="img_add">Add Btn Label</label>
        <div class="form-right-block">
        <input type="text" id="img_add" name="options[add]" value="$add">
        <p class="info">
            Thumbnail add button label
        </p>
        </div>
        <label for="img_remove">Remove Btn Label</label>
        <div class="form-right-block">
        <input type="text" id="img_remove" name="options[remove]" value="$remove">
        <p class="info">
            Thumbnail remove button label
        </p>
        </div>
FIELD;

        return $f;
    }

    public function displayForm($field, $value = null)
    {
        $opts = $this->makeOptions($field->options);
        $key = $field->field_slug;
        $label = \Form::label($field->field_name, $key);
        $info = $this->makeInfo($field->description);

        $value = $value ? $value : $this->getValue($key, $field->default);

        $field = \Form::thumbnail($key, $value, $opts['width'], $opts['labels']);

        $f = <<<FORM
        <div class="form-block">
            $label

            <div class="form-right-block">
                $field
                $info
            </div>
        </div>
FORM;

        return $f;
    }

    protected function makeOptions($str)
    {
        $options = json_decode($str);
        $result = array();
        $result['width'] = isset($options->width) ? (int) $options->width : null;
        $result['labels'] = array();
        if (isset($options->add)) {
            $result['labels']['add'] = $options->add;
        }
        if (isset($options->remove)) {
            $result['labels']['remove'] = $options->remove;
        }

        return $result;
    }

} // END class Image extends \Field\AbstractType
