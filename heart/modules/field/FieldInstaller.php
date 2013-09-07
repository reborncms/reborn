<?php

namespace Field;

class FieldInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install()
	{
		\Schema::table('fields', function($table)
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

		\Schema::table('field_data', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->integer('field_id');
			$table->integer('post_id');
			$table->integer('group_id');
			$table->string('field_name');
			$table->text('field_value');
		});

		\Schema::table('field_groups', function($table)
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

	public function uninstall()
	{
		\Schema::drop('fields');
		\Schema::drop('field_data');
		\Schema::drop('field_groups');
	}

	public function upgrade($dbVersion) {}

}
