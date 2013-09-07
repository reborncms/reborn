<?php

namespace Field\Util;

use Reborn\Util\Table;

/**
 * Field Group Table Generator
 *
 * @package Field
 * @author Nyan Lynn Htut
 **/
class FieldGroupTable
{

	protected static $actions = array(
				'edit' => array(
						'title' => 'Table Edit',
						'icon' => 'icon-edit',
						'btn-class' => 'btn btn-green'
					),
				'delete' => array(
						'title' => 'Table Delete',
						'icon' => 'icon-remove',
						'btn-class' => 'btn btn-red'
					)
			);

	protected static $opts = array(
				'check_all' => true,
				'class' => 'stripe',
				'btn_type' => 'icons-bar'
			);

	public static function make($obj)
	{
		static::$actions['edit']['url'] = adminUrl('field/group-edit/[:id]');
		static::$actions['delete']['url'] = adminUrl('field/group-delete/[:id]');
		static::$opts['actions'] = static::$actions;
		$table = new Table(static::$opts);
		$table->setObject($obj);
		$table->headers(array('Name', 'Relation', 'Description', array('name' => 'Actions', 'width' => '14%')));
		$table->columns(array('name', 'relation', 'description'));

		return $table;
	}

} // END class FieldGroupTable
