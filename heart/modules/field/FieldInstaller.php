<?php

namespace Field;

class FieldInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null)
	{
		\Schema::table($prefix.'fields', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('field_name');
			$table->string('field_slug');
			$table->string('field_type');
			$table->text('description');
			$table->text('options');
			$table->text('default');
		});

		\Schema::table($prefix.'field_data', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->integer('field_id');
			$table->integer('post_id');
			$table->integer('group_id');
			$table->string('field_name');
			$table->text('field_value');
		});

		\Schema::table($prefix.'field_groups', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('name');
			$table->text('description');
			$table->string('relation'); // relation type name
			$table->string('relation_type'); //module or content (in next)
			$table->text('fields');
		});
	}

	public function uninstall($prefix = null)
	{
		\Schema::drop($prefix.'fields');
		\Schema::drop($prefix.'field_data');
		\Schema::drop($prefix.'field_groups');
	}

	public function upgrade($dbVersion, $prefix = null) {}

}
