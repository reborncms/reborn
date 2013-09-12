<?php

namespace Field\Model;

use Input;
use Illuminate\Database\Eloquent\Collection;

/**
 * Field Group Provider
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class FieldGroupProvider
{

	/**
	 * Field lists
	 *
	 * @var Illuminate\Database\Eloquent\Collection
	 */
	protected $fields;

	/**
	 * Default Instance method
	 *
	 * @param null|Illuminate\Database\Eloquent\Collection $fields
	 * @return void
	 * @author
	 **/
	public function __construct(Collection $fields = null )
	{
		$this->fields = $fields;
	}

	/**
	 * Save Field Group.
	 *
	 * @return boolean
	 **/
	public function save(&$group)
	{
		$group->name = Input::get('name');
		$group->description = Input::get('description', '');
		$group->relation = Input::get('relation');
		$group->relation_type = Input::get('relation_type');
		$group->fields = json_encode(Input::get('fields', array()));

		return $group->save();
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
		$fields = FieldData::where('group_id', $id)->get();

		foreach ($fields as $f) {
			$f->delete();
		}
	}

} // END class FieldGroupProvider
