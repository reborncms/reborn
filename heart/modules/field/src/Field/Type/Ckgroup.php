<?php 
	
namespace Field\Type;

/**
 * Checkbox group
 *
 * @package Field
 * @author Li Jia Li
 **/
class Ckgroup extends \Field\AbstractType
{
	
	public function filler($default = null, $options = null)
	{
		$field = <<<FIELD
		<label for="ck-options">Checkbox Options</label>
		<div class="form-right-block">
		<textarea id="checkgroup-options" name="options" value="$options"></textarea>
		<p class="info">
		Enter each option list on line by line. You can separate key value with "=" sign.
		example ::
		<br>
		one=One<br>two=Two
		</p>
		</div>
		<label for="ck-default">Default checked values</label>
		<div class="form-right-block">
		<input type="text" name="default" id="checkgroup-default" value="$default"></input>
		<p class="info">Separate the default checked keys with comma(,). Eg: one, two</p>
		</div>
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

		$ckvalues = explode(',', $field->default);

		if ($value != null) {
			$value = (array)json_decode($value);
		}

		foreach ($ckvalues as $val) {
			$default[] = trim($val);
		}

		$value = $value ? $value : $this->getValue($field->field_slug, $default);

		$ckgroup= \Form::ckboxGroup($field->field_slug ,$options, $value);

		$f = <<<FORM
		<div class="form-block">
			$label
			<div class="form-right-block">
			$ckgroup
			$info
			</div>
		</div>
FORM;
		return $f;
	}

	/**
	 * Pre Check before saving.
	 *
	 * @param Field\Model\Field $field Field Model Object
	 * @return boolean
	 **/
	public function preSaveCheck($field)
	{
		$key = $field->field_slug;

		$value = \Input::get($key);

		if ($value == null) {
			$value = array();
		}

		$value = is_array($value) ? json_encode($value) : $value;

		if ('' == $value) return false;

		if (\Event::has('field.'.$key.'save_check')) {
			$res = \Event::call('field.'.$key.'save_check', array($value));

			return isset($res[0]) ? $res[0] : false;
		}

		return $value;
	}

	/**
	 * Pre Check before updating.
	 *
	 * @param Field\Model\Field $field Field Model Object
	 * @param mixed $value Field value form db
	 * @return mixed
	 **/
	public function preUpdateCheck($field, $value)
	{
		$key = $field->field_slug;

		$new_value = \Input::get($key);

		if ($new_value == null) {
			$new_value = array();
		}

		$new_value = is_array($new_value) ? json_encode($new_value) : $new_value;

		if ('' == $new_value) return false;

		// Same value, not need to update
		if ($value == $new_value) return false;

		if (\Event::has('field.'.$key.'update_check')) {
			$res = \Event::call('field.'.$key.'update_check', array($new_value));

			return isset($res[0]) ? $res[0] : false;
		}

		return $new_value;
	}

}