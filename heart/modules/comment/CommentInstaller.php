<?php

namespace Comment;

class CommentInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null)
	{

		\Schema::table($prefix.'comments', function($table)
	    {
	        $table->create();
	        $table->increments('id');
	        $table->string('name', 160)->nullable();
	        $table->string('email', 160)->nullable();
	        $table->string('url', 160)->nullable();
	        $table->integer('user_id')->nullable();
	        $table->text('value');
	        $table->string('module', 20);
	        $table->integer('content_id');
	        $table->string('content_title_field', 20)->default('title');
	        $table->integer('edit_user')->nullable();
	        $table->enum('status', array('pending','approved','spam'))->default('pending');
	        $table->integer('parent_id')->default(0);
	        $table->string('ip_address', 20);
	        $table->timestamps();
	        $table->softDeletes();
	         
	    });


		$data = array(
			'slug'		=> 'comment_gravatar_size',
			'name'		=> 'Gravatar size',
			'desc'		=> 'Gravatar size to show in comment',
			'value'		=> '',
			'default'	=> '100',
			'module'	=> 'Comment'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'akismet_api_key',
			'name'		=> 'Akismet API Key',
			'desc'		=> 'Get your API key at https://akismet.com/',
			'value'		=> '',
			'default'	=> '',
			'module'	=> 'Comment'
			);
	    \Setting::add($data);

	    $data = array(
	    	'slug' 		=> 'use_default_style',
	    	'name'		=> 'Use default style',
	    	'desc'		=> 'Use default style in frontend (uncheck this if you want to use your own style)',
	    	'value'		=> '1',
	    	'default'	=> '0',
	    	'module'	=> 'Comment'
	    );
	    \Setting::add($data);

	    $data = array(
	    	'slug' 		=> 'comment_enable',
	    	'name'		=> 'Comment Enable',
	    	'desc'		=> 'If you disabled here, comment will not avaliable even you open comment in single content.',
	    	'value'		=> '',
	    	'default'	=> 'enable',
	    	'module'	=> 'Comment'
	    );
	    \Setting::add($data);

	    $data = array(
	    	'slug'		=> 'comment_need_approve',
	    	'name'		=> 'Need admin approval for non-member',
	    	'desc'		=> 'Non-member comment will need admin approval',
	    	'value'		=> '1',
	    	'default'	=> '0',
	    	'module'	=> 'Comment'
	    );
	    \Setting::add($data);
		
	}

	public function uninstall($prefix = null)
	{
		\Schema::drop($prefix.'comments');

		\Setting::remove('comment_gravatar_size');

		\Setting::remove('akismet_api_key');

		\Setting::remove('use_default_style');

		\Setting::remove('comment_enable');

		\Setting::remove('comment_need_approve');
	}

	public function upgrade($v, $prefix = null)
	{
		if ($v == '1.0') {
			\Schema::table($prefix.'comments', function($table)
		    {
		     	$table->softDeletes();    
		    });
		}
	}

}
