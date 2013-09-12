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
		$field->description = Input::get('description', '');
		$options = Input::get('options', '');
		if (is_array($options)) {
			$options = json_encode($options);
		}
		$field->options = $options;
		$field->default = Input::get('default', '');

		return $field->save();
	}

	/**
	 * Delete field_data
	 *
	 * @param int $id Field Id
	 * @return void
	 **/
	public function delete($id)
	{
		// First delete field_data where field_id = $id
		$fields = FieldData::where('field_id', $id)->get();

		foreach ($fields as $f) {
			$f->delete();
		}
	}

} // END class FieldProvider
