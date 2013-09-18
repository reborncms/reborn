<?php 
	
namespace Field\Type;

/**
 * Radio group
 *
 * @package Field
 * @author Li Jia Li
 **/
class RadioGroup extends \Field\AbstractType
{
	
	public function filler($default = null, $options = null)
	{
		$field = <<<FIELD
		<label for="radio-options">Radio Options</label>
		<div class="form-right-block">
		<textarea id="radiogroup-options" name="options" value="$options"></textarea>
		<p class="info">
		Enter each option list on line by line. You can separate key value with "=" sign.
		example ::
		<br>
		one=One<br>two=Two
		</p>
		</div>
		<label for="ck-default">Default checked values</label>
		<div class="form-right-block">
		<input type="text" name="default" id="radio-default" value="$default"></input>
FIELD;

	return $field;
	}

	/**
	 * Get display form view to insert data
	 *
	 * @return string
	 **/
	public function displayForm($field, $value = null)
	{
		$label = \Form::label($field->field_name, $field->field_slug);

		$options = $this->makeOptions($field->options);

		$info = $this->makeInfo($field->description);

		$value = $value ? $value : $this->getValue($field->field_slug, $field->default);

		$radiogroup= \Form::radioGroup($field->field_slug ,$options, $value);

		$f = <<<FORM
		<div class="form-block">
			$label
			<div class="form-right-block">
			$radiogroup
			$info
			</div>
		</div>
FORM;
		return $f;
	}

}