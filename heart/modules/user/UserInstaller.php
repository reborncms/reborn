<?php

namespace User;

class UserInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null) {
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

	public function uninstall($prefix = null)
	{
		\Setting::delete('user_registration');
	}

	public function upgrade($v, $prefix = null)
	{
		return $v;
	}

}
