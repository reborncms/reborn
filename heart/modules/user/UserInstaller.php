<?php

namespace User;

class UserInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install() {
		$data = array(
	    	'slug'		=> 'user_registration',
	    	'name'		=> 'Allow user registration',
	    	'desc'		=> 'Anyone can register',
	    	'value'		=> '1',
	    	'default'	=> '1',
	    	'module'	=> 'User'
	    );
	    \Setting::add($data);
	}

	public function uninstall()
	{
		return false;
	}

	public function upgrade($v)
	{
		return $v;
	}

}
