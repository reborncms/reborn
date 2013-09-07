<?php
namespace Field\Model;

use Input;

/**
 * Field Provider Class
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class FieldProvider
{

	/**
	 * Save the Field Model Data
	 *
	 * @param \Field\Model\Field $field Field Model
	 * @return boolean
	 **/
	public function save(&$field)
	{
		$field->field_name = Input::get('field_name');
		$field->field_slug = Input::get('field_slug');
		$field->field_type = Input::get('field_type');
		$field->description = Input::get('description');
		$field->options = Input::get('options', '');
		$field->default = Input::get('default', '');

		return $field->save();
	}

} // END class FieldProvider
