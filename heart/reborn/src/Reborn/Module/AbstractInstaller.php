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
    abstract public function install($prefix = null);

    /**
     * Module uninstall process
     *
     * @return void
     **/
    abstract public function uninstall($prefix = null);

    /**
     * Module upgrade process
     *
     * @param  string $version Module version from the DB
     * @return void
     **/
    abstract public function upgrade($version, $prefix = null);

} // END abstract class AbstractInstaller
