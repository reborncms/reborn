<?php

namespace Blog;

class BlogInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install()
	{
		\Schema::table('blog', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('title');
			$table->string('slug');
			$table->integer('category_id');
			$table->text('body');
			$table->text('excerpt');
			$table->string('attachment')->nullable();
			$table->integer('author_id');
			$table->enum('comment_status', array('open', 'close'))->default('open');
			$table->enum('status', array('draft', 'live'))->default('draft');
			//type
			$table->integer('view_count')->default(0);
			$table->integer('lang_ref')->nullable();
			$table->string('lang', 20)->nullable();
			$table->softDeletes();
			$table->timestamps();
		});

		\Schema::table('blog_categories', function($table)
		{
			$table->create();
			$table->increments('id');
			$table->string('name', 32);
			$table->string('slug', 32);
			$table->text('description');
			$table->integer('parent_id')->default(0);
			$table->integer('order')->default(0);
		});

		\DB::table('blog_categories')->insert(array(
			'name' 			=> 'Default',
    		'slug' 			=> 'default',
    		'description' 	=> 'default category',
    		'parent_id' 	=> 0,
    		'order'			=> 0
    		)
		);

		$data = array(
			'slug'		=> 'blog_per_page',
			'name'		=> 'Blog Posts per page',
			'desc'		=> 'Number of blog post to show at frontend',
			'value'		=> '',
			'default'	=> '5',
			'module'	=> 'Blog'
			);
	    \Setting::add($data);

	    $data = array(
	    	'slug' 		=> 'blog_rss_items',
	    	'name'		=> 'Blog RSS Items',
	    	'desc'		=> 'Number of blog post to show in RSS Feed',
	    	'value'		=> '',
	    	'default' 	=> '10',
	    	'module'	=> 'Blog'
	    );
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'excerpt_length',
			'name'		=> 'Length of blog excerpt',
			'desc'		=> 'Blog excerpt word count',
			'value'		=> '',
			'default'	=> '50',
			'module'	=> 'Blog'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'blog_content_default_lang',
			'name'		=> 'Contents default language',
			'desc'		=> 'Default language for blog contents',
			'value'		=> '',
			'default'	=> 'English',
			'module'	=> 'Blog'
			);
	    \Setting::add($data);
	}

	public function uninstall()
	{
		\Schema::drop('blog');
		\Schema::drop('blog_categories');
	}

	public function upgrade($v)
	{
		if ($v == '1.0') {
			\Schema::table('blog', function($table)
			{
				$table->integer('lang_ref')->nullable();
				$table->string('lang', 20)->nullable();
				$table->softDeletes();
			});

			$data = array(
			'slug'		=> 'blog_content_default_lang',
			'name'		=> 'Contents default language',
			'desc'		=> 'Default language for blog contents',
			'value'		=> '',
			'default'	=> 'English',
			'module'	=> 'Blog'
			);
	    	\Setting::add($data);
		}

	}

}
