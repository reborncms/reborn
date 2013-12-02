<?php

namespace Widgets;

class WidgetsInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null) 
	{
		\Schema::table($prefix.'widgets', function($table)
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

	public function uninstall($prefix = null) 
	{
		\Schema::drop($prefix.'widgets');
	}

	public function upgrade($dbVersion, $prefix = null) {}

}
