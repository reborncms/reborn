<?php

namespace Widgets;

class WidgetsInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install() 
	{
		\Schema::table('widgets', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('name', 50);
			$table->string('area', 50);
			$table->integer('widget_order')->default(0);
			$table->text('options')->nullable();
			$table->timestamps();
		});
	}

	public function uninstall() 
	{
		\Schema::drop('widgets');
	}

	public function upgrade($dbVersion) {}

}
