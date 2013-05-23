<?php

namespace Reborn\Module;

/**
 * Module Installer Abstract Class for Reborn Module
 *
 * @package Reborn\Module
 * @author Myanmar Links Professional Web Development Team
 **/
abstract class AbstractInstaller
{
	/**
	 * Module install process
	 *
	 * @return void
	 **/
	abstract function install();

	/**
	 * Module uninstall process
	 *
	 * @return void
	 **/
	abstract function uninstall();

	/**
	 * Module upgrade process
	 *
	 * @param string $version Module version from the DB
	 * @return void
	 **/
	abstract function upgrade($version);

} // END abstract class AbstractInstaller

