<?php

namespace SiteManager;

class SiteManagerInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null)
	{
		\Schema::table('sites_manager', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('name');
			$table->string('domain');
			$table->text('description');
			$table->timestamps();
		});
	}

	public function uninstall($prefix = null)
	{
		\Schema::dropIfExists('sites_manager');
	}

	public function upgrade($dbVersion, $prefix = null) {}

}
