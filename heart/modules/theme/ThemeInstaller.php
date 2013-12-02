<?php

namespace Theme;

class ThemeInstaller extends \Reborn\Module\AbstractInstaller
{

	public function install($prefix = null) {}

	public function uninstall($prefix = null) {}

	public function upgrade($v, $prefix = null)
	{
		return $v;
	}

}
