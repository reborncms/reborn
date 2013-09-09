<?php

namespace Field\Type;

/**
 * Dropdown list Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Dropdown extends \Field\AbstractType
{

	public function filler($default = null, $options = null)
	{
		$f = <<<FIELD
		<label for="text-options">Options Value</label>
		<div class="form-right-block">
		<textarea id="dropdown-options" name="options" value="$options"></textarea>
		<p class="info">
			Enter each option list on line by line. You can separate key value with "=" sign.
			example ::
			<br>
			one=One<br>two=Two
		</p>
		</div>
		<label for="text-deafult">Default Value</label>
		<div class="form-right-block">
		<input type="text" name="default" id="dropdown-default"value="$default"></input>
		</div>
FIELD;

		return $f;
	}

	public function displayForm($field, $value = null)
	{
		$options = $this->makeOptions($field->options);
		$key = $field->field_slug;
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

	protected function makeOptions($str)
	{
		$lines = explode("\n", $str);

		$options = array();

		foreach ($lines as $line) {
			list($key, $value) = explode('=', $line);
			$options[$key] = $value;
		}

		return $options;
	}

} // END class Dropdown extends \Field\AbstractType
