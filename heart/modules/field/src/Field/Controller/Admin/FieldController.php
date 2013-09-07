<?php

namespace Field\Controller\Admin;

use Input, Redirect, Flash,
	Field\Model\Field,
	Field\Model\FieldGroup,
	Field\Model\FieldProvider,
	Field\Model\FieldGroupProvider,
	Field\Util\FieldTable,
	Field\Util\FieldGroupTable;

class FieldController extends \AdminController
{

	protected $field;

	public function __construct(\Field\Builder $field)
	{
		$this->field = $field;
	}

	public function before()
	{
		$this->menu->activeParent('content');

		$this->template->style('fields.css', 'field');
	}

	public function index()
	{
		$field = Field::all();

		$empty = $field->isEmpty();

		if (!$empty) {
			$this->template->table = FieldTable::make($field);
		}

		$this->template->title('Field')
						->set('empty', $empty)
						->setPartial('admin/field/index');
	}

	public function create()
	{
		$field = new Field();

		if(Input::isPost()) {

			$provider = new FieldProvider();

			if ($provider->save($field)) {
				Flash::success('Field is successfully created');
				return Redirect::toAdmin('field');
			} else {
				Flash::error('Error occured to create Field!');
			}
		}

		$this->template->title('Field Create')
						->set('field', $field)
						->set('method', 'create')
						->set('supported_type', supported_field_types())
						->setPartial('admin/field/form');
	}

	public function edit($id)
	{
		$field = Field::find($id);

		if (is_null($field)) return $this->notFound();

		if(Input::isPost()) {

			$provider = new FieldProvider();

			if ($provider->save($field)) {
				Flash::success('Field is successfully edited');
				return Redirect::toAdmin('field');
			} else {
				Flash::error('Error occured to edit Field!');
			}
		}

		$field_body = $this->getTypeDisplay($field->field_type, $field->default, $field->options);

		$this->template->title('Field Edit')
						->set('field', $field)
						->set('method', 'edit/'.$field->id)
						->set('supported_type', supported_field_types())
						->set('field_body', $field_body)
						->setPartial('admin/field/form');
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	public function getTypeDisplay($type, $default = null, $options = null)
	{
		$form = $this->field->getTypeForm($type, $default, $options);

		if($this->request->isAjax()) {
			return $this->json(array('text' => $form));
		}

		return $form;
	}

	/**
	 * Get all Field Group
	 *
	 * @return void
	 **/
	public function group()
	{
		$all = FieldGroup::all();
		$empty = $all->isEmpty();

		if (!$empty) {
			$this->template->table = FieldGroupTable::make($all);
		}

		$this->template->title('Field Group')
						->set('empty', $empty)
						->setPartial('admin/group/index');
	}

	/**
	 * Create Field Group
	 *
	 * @return void
	 **/
	public function groupCreate()
	{
		$group = new FieldGroup();

		$fields = Field::all();

		if(Input::isPost()) {

			$provider = new FieldGroupProvider($fields);

			if ($provider->save($group)) {
				Flash::success('Group '.$group->name.' is successfully created');
				return Redirect::toAdmin('field/group');
			} else {
				Flash::error('Error occured to create Group!');
			}
		}

		$this->template->title('Field Group Create')
						->set('group', $group)
						->set('fields', $fields)
						->set('method', 'group-create')
						->setPartial('admin/group/form');
	}

	/**
	 * Create Field Group
	 *
	 * @return void
	 **/
	public function groupEdit($id)
	{
		$group = FieldGroup::find($id);

		if (is_null($group)) return $this->notFound();

		$fields = Field::whereNotIn('id', $group->fields)->get();

		$group_fields = Field::whereIn('id', $group->fields)->get();

		if(Input::isPost()) {

			$provider = new FieldGroupProvider($fields);

			if ($provider->save($group)) {
				Flash::success('Group '.$group->name.' is successfully edited');
				return Redirect::toAdmin('field/group');
			} else {
				Flash::error('Error occured to edit Group!');
			}
		}

		$this->template->title('Field Group Edit')
						->set('group', $group)
						->set('fields', $fields)
						->set('group_fields', $group_fields)
						->set('method', 'group-edit/'.$group->id)
						->setPartial('admin/group/form');
	}
}

