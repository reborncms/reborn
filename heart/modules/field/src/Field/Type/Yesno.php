<?php

namespace Field\Type;

/**
 * Dropdown list for Yes No Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Yesno extends \Field\AbstractType
{

	public function filler($default = null, $options = null)
	{
		$f = <<<FIELD
		<label for="text-deafult">Default Value</label>
		<div class="form-right-block">
		<select name="default" id="yesno-default">
			<option value="1">Yes</option>
			<option value="0" selected="selected">No</option>
		</select>
		<p class="info">Choose default value for Yes/No Dropdown</p>
		</div>
FIELD;

		return $f;
	}

	public function displayForm($field, $value = null)
	{
		$options = array('1' => 'Yes', '0' => 'No');
		$key = $this->makeKey($field->field_name);
		$label = \Form::label($field->field_name, $key);
		$info = $this->makeInfo($field->description);

		$value = $value ? $value : $this->getValue($key, $field->default);

		$select = \Form::select($key, $options, $value);

		$f = <<<FORM
		<div class="form-block">
			$label

			<div class="form-right-block">
				$select
				$info
			</div>
		</div>
FORM;
		return $f;
	}

} // END class Yesno extends \Field\AbstractType
