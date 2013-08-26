<?php

namespace Contact;

class ContactInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install()
	{
		\Schema::table('contact', function($table)
	    {
	        $table->create();
	        $table->increments('id');
	        $table->string('name');
	        $table->string('email');
	        $table->string('subject');
	        $table->text('message');
	        $table->string('ip');
	        $table->text('attachment');
	        $table->integer('read_mail');
	        $table->timestamps();
	    });
	    \Schema::table('email_template', function($table)
	    {
	        $table->create();
	        $table->increments('id');
	        $table->string('slug');
	        $table->string('name');
	        $table->string('description');
	        $table->text('body');
	        $table->integer('detemp');
	        $table->timestamps();
	    });

	    \DB::table('email_template')->insert(array(
            'slug'             => 'contact',
            'name'             => 'Default Template',
            'description'      => 'Default Template for Contact Form',
            'body'             => '&lt;h5&gt;Message Detail&lt;/h5&gt;&lt;/br&gt; &lt;p&gt; Ip Address {{ip}}&lt;/p&gt;&lt;/br&gt; &lt;p&gt; Name = {{name}}&lt;/p&gt; &lt;/br&gt;&lt;p&gt; Email = {{email}}&lt;/p&gt; &lt;/br&gt;&lt;p&gt; Subject = {{subject}}&lt;/p&gt;&lt;/br&gt; &lt;p&gt; Message :&lt;/p&gt; &lt;/br&gt;&lt;p&gt; {{message}}&lt;/p&gt;',
            'detemp'       => '1',
            )
        );
        \DB::table('email_template')->insert(array(
            'slug'             => 'reply_email',
            'name'             => 'Default Reply Template',
            'description'      => 'Default Reply Template for Contact Form',
            'body'             => '&lt;h5&gt;Message Detail&lt;/h5&gt;&lt;/br&gt; &lt;p&gt; Name = {{name}}&lt;/p&gt; &lt;/br&gt;&lt;p&gt; Email = {{from}}&lt;/p&gt; &lt;/br&gt;&lt;p&gt; Subject = {{subject}}&lt;/p&gt;&lt;/br&gt; &lt;p&gt; Message :&lt;/p&gt; &lt;/br&gt;&lt;p&gt; {{message}}&lt;/p&gt;',
            'detemp'       => '1',
            )
        );
		$data = array(
			'slug'		=> 'sever_mail',
			'name'		=> 'Sever Mail',
			'desc'		=> 'Email for outgoing Email',
			'value'		=> '',
			'default'	=> 'admin@localhost.com',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'site_mail',
			'name'		=> 'Site Mail',
			'desc'		=> 'Contact for your Website',
			'value'		=> '',
			'default'	=> 'gaara@localhost.com',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'transport_mail',
			'name'		=> 'Mail Service',
			'desc'		=> 'Transoprt service for Mail',
			'value'		=> '',
			'default'	=> 'mail',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'smtp_host',
			'name'		=> 'SMTP Host',
			'desc'		=> 'Host name for SMTP',
			'value'		=> '',
			'default'	=> '',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'smtp_port',
			'name'		=> 'SMTP Port',
			'desc'		=> 'Port for SMTP',
			'value'		=> '',
			'default'	=> '',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'smtp_username',
			'name'		=> 'SMTP Username',
			'desc'		=> 'Username for SMTP',
			'value'		=> '',
			'default'	=> '',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'smtp_password',
			'name'		=> 'SMTP Password',
			'desc'		=> 'Password for SMTP',
			'value'		=> '',
			'default'	=> '',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'sendmail_path',
			'name'		=> 'Sendmail Path',
			'desc'		=> 'Path for Sendmail',
			'value'		=> '',
			'default'	=> '',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'contact_template',
			'name'		=> 'Contact Template',
			'desc'		=> 'Template for Contact Form from User or Guest',
			'value'		=> '',
			'default'	=> 'contact',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);

	    $data = array(
			'slug'		=> 'reply_template',
			'name'		=> 'Reply Mail Template',
			'desc'		=> 'Template for Reply Mail to User or Guest',
			'value'		=> '',
			'default'	=> 'reply_email',
			'module'	=> 'Contact'
			);
	    \Setting::add($data);
	}

	public function uninstall()
	{
		\Schema::drop('contact');
	}

	public function upgrade($v)
	{
		return $v;
	}

}
