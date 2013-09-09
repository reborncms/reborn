<?php

namespace Field\Type;

/**
 * Wysiwyg Field
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class Wysiwyg extends \Field\AbstractType
{

	public function filler($default = null, $options = null)
	{
		$types = array('normal' => 'Normal', 'mini' => 'Mini', 'sample' => 'Sample');
		$select = \Form::select('options', $types, $options, array('id' => 'wysiwyg-options'));
		$f = <<<FIELD
		<label for="wysiwyg-options">Choose CkEditor Type</label>
		<div class="form-right-block">
		$select
		<p class="info">
			Reborn support three type of CkEditor. See detail at Reborn\Form Class.
		</p>
		</div>
		<label for="text-deafult">Default Value</label>
		<div class="form-right-block">
		<input type="text" name="default" id="wysiwyg-default"value="$default"></input>
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

		$element = $this->makeElement($field->options, $key, $value);

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

	protected function makeElement($option, $key, $value)
	{
		switch ($option) {
			case 'mini':
				$ele = \Form::ckmini($key, $value);
				break;

			case 'simple':
				$ele = \Form::cksimple($key, $value);
				break;

			default:
				$ele = \Form::ckeditor($key, $value);
				break;
		}

		return $ele;
	}

} // END class Wysiwyg extends \Field\AbstractType
