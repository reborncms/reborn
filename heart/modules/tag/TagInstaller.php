<?php

namespace Tag;

class TagInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install() 
	{
		\Schema::table('tags', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('name',50);
		});

		\Schema::table('tags_relationship', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->integer('tag_id');
			$table->integer('object_id');
			$table->string('object_name', 32);
		});
	}

	public function uninstall() 
	{
		\Schema::drop('tags');
		\Schema::drop('tags_relationship');
	}

	public function upgrade($v) 
	{
		return $v;
	}

}
