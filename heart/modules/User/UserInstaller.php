<?php

namespace User;

class UserInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install() {}

	public function uninstall()
	{
		return false;
	}

	public function upgrade($v)
	{
		return $v;
	}

}
