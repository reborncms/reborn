<?php

namespace Field\Type;

/**
 * Checkbox Field
 *
 * @package Field
 * @author RebornCMS Develpoement Team
 **/
class Checkbox extends \Field\AbstractType
{

	public function filler($default = null, $options = null)
	{
		$f = <<<FIELD
		<label for="checkbox-deafult">Default State</label>
		<div class="form-right-block">
		<select name="default" id="checkbox-default">
			<option value="1" selected="selected">Checked</option>
			<option value="0">Unchecked</option>
		</select>
		<p class="info">Choose default State for Check/Uncheck Checkbox</p>
		</div>
		<label for="checkbox-options">Options Value</label>
		<div class="form-right-block">
		<input type="text" id="checkbox-options" name="options" value="$options">
		<p class="info">This is Options Value for Checkbox. If this field is not empty, field name will not show. But Options Value show in right side of Checkbox. </p>
		</div>
FIELD;

		return $f;
	}

	public function displayForm($field, $value = null)
	{
		$options = $field->options;
		$key = $field->field_slug;
		if (empty($options)) {
			$label = \Form::label($field->field_name, $key);
		}else {
			$label = null;
		}
		$info = $this->makeInfo($field->description);
		$value = $value ? $value : $this->getValue($key, $field->default);

		$check = \Form::checkbox($key, '', $value);

		$f = <<<FORM
		<div class="form-block">
			$label

			<div class="form-right-block">
				$check $options
				$info
			</div>
		</div>
FORM;
		return $f;
	}

} // END class CheckBox extends \Field\AbstractType
