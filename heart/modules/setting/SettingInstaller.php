<?php

namespace Setting;

class SettingInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install()
	{
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
