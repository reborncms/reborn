<?php

namespace Navigation;

class NavigationInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null)
	{
		\Schema::table($prefix.'navigation', function($table)
	    {
	        $table->create();
	        $table->increments('id');
	        $table->string('title');
	        $table->string('slug');
	    });

	    \DB::table($prefix.'navigation')->insert(array('id' => 1, 'title' => 'Header', 'slug' => 'header'));
	    \DB::table($prefix.'navigation')->insert(array('id' => 2, 'title' => 'Footer', 'slug' => 'footer'));

	    \Schema::table($prefix.'navigation_links', function($table)
	    {
	        $table->create();
	        $table->increments('id');
	        $table->integer('navigation_id');
	        $table->string('link_type', 25);
	        $table->string('title', 100);
	        $table->string('url');
	        $table->integer('parent_id');
	        $table->integer('link_order');
	        $table->string('class')->default('');
	        $table->string('target', 10)->default('');
	        $table->string('permission')->default('');
	    });

	    \DB::table($prefix.'navigation_links')->insert(array(
		    					'navigation_id' => 1,
		    					'link_type' => 'Pages',
		    					'title' => 'Home',
		    					'url' => 'home',
		    					'parent_id' => 0,
		    					'link_order' => 0,
		    					'class' => '',
		    					'target' => '',
		    					'permission' => ''
	    					));
	}

	public function uninstall($prefix = null)
	{
		\Schema::drop($prefix.'navigation');
		\Schema::drop($prefix.'navigation_links');
	}

	public function upgrade($dbVersion, $prefix = null)
	{
		return true;
	}

}
