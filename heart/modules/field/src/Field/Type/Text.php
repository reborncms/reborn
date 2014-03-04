<?php

namespace Field\Type;

/**
 * Text Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Text extends \Field\AbstractType
{

    public function filler($default = null, $options = null)
    {
        $f = '<label for="text-deafult">Default Value</label>';
        $f .= '<div class="form-right-block">';
        $f .= '<input id="text-default" name="default" value="'.$default.'">';
        $f .= '</div>';

        return $f;
    }

    public function displayForm($field, $value = null)
    {
        $key = $field->field_slug;
        $label = \Form::label($field->field_name, $key);
        $info = $this->makeInfo($field->description);
        $value = $value ? $value : $this->getValue($key, $field->default);

        $area = \Form::text($key, $value);

        $f = <<<FORM
        <div class="form-block">
            $label

            <div class="form-right-block">
                $area
                $info
            </div>
        </div>
FORM;

        return $f;
    }

} // END class Text extends \Field\AbstractType
