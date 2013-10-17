<?php

namespace Setting;

class SettingInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install()
	{
		return true;
	}

	public function uninstall()
	{
		return false;
	}

	public function upgrade($dbVersion)
	{
		return true;
	}

}
