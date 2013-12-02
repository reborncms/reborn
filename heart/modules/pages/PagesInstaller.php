<?php

namespace Pages;

class PagesInstaller extends \Reborn\Module\AbstractInstaller
{

    public function install($prefix = null)
    {
    	\Schema::table($prefix.'pages', function($table)
    	{
    		$table->create();
    		$table->increments('id');
    		$table->string('title');
    		$table->string('slug')->unique();
    		$table->string('uri');
    		$table->text('content')->nullable();
    		$table->string('page_layout', 50);
    		$table->integer('parent_id')->nullable();
    		$table->string('meta_title')->nullable();
    		$table->string('meta_keyword')->nullable();
    		$table->text('meta_description')->nullable();
    		$table->text('css')->nullable();
    		$table->text('js')->nullable();
    		$table->integer('comments_enable');
    		$table->enum('status', array('draft','live'))->default('draft');
    		$table->integer('author_id');
    		$table->integer('page_order')->default(0);
    		$table->timestamps();
    	});

        \DB::table($prefix.'pages')->insert(array(
            'title'             => 'Home',
            'slug'              => 'home',
            'uri'               => 'home',
            'content'           => 'Welcome ! This is home page. Please edit me.',
            'page_layout'       => 'default',
            'meta_title'        => 'Home',
            'meta_keyword'      => 'reborncms, home',
            'meta_description'  => 'This is reborncms default home page.',
            'comments_enable'   => 0,
            'status'            => 'live',
            'author_id'         => 1,
            'page_order'        => 0,
            )
        );

        $data = array(
            'slug'      => 'home_page',
            'name'      => 'Home Page',
            'desc'      => 'Home Page for your site',
            'value'     => '',
            'default'   => 'home',
            'module'    => 'system'
            );
        \Setting::add($data);
    }

    public function uninstall($prefix = null)
    {
    	\Schema::drop($prefix.'pages');

        \Setting::remove('home_page');
    }

    public function upgrade($v, $prefix = null)
    {
    	return $v;
    }

}
