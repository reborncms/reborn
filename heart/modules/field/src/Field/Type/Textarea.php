<?php

namespace Field\Type;

/**
 * Textarea Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Textarea extends \Field\AbstractType
{

	public function filler($default = null, $options = null)
	{
		$f = '<label for="text-deafult">Default Value</label>';
		$f .= '<div class="form-right-block">';
		$f .= '<textarea id="textarea-default" name="default" value="'.$default.'"></textarea>';
		$f .= '</div>';
		return $f;
	}

	public function displayForm($field, $value = null)
	{
		$key = $field->field_slug;
		$label = \Form::label($field->field_name, $key);
		$info = $this->makeInfo($field->description);
		$value = $value ? $value : $this->getValue($key, $field->default);

		$area = \Form::textarea($key, $value);

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

} // END class Textarea extends \Field\AbstractType
