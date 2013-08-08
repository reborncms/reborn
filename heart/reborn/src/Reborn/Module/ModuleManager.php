<?php

namespace Reborn\Module;

use Reborn\Connector\DB\DBManager as DB;
use Reborn\Exception\ModuleException;
use Reborn\Util\Menu;

/**
 * Module Manager Class for Reborn
 *
 * @package Cores
 * @author Myanmar Links Professional Web Development Team
 **/
class ModuleManager
{
    // Module Object
    protected static $mod;

    /**
     * Module table name
     *
     * @var string
     **/
    protected static $_table = 'modules';

    /**
     * Module Information file name
     *
     * @var string
     **/
    protected static $infoFile = 'Info.php';

    /**
     * Module Installer file name
     *
     * @var string
     **/
    protected static $installer = 'Installer.php';

    /**
     * Module Bootstrap file name
     *
     * @var string
     **/
    protected static $bootstrap = 'Bootstrap.php';

    /**
     * Variables for module exception message
     *
     * @var string
     */
    protected static $noInfoFile = '%s not found in given module { %s }.';
    protected static $modFileRule = '%s of { %s } is not match with Reborn Module rule';

    /**
     * Initialize method for module class
     */
    public static function initialize()
    {
        // Find all module from Module Folders
        $modulesFromFolder = static::findAll();

        // Find all module list from DB
        $modulesFromDB = static::findFromDB();

        // Setup the all modules
        $allMods = static::moduleSetup($modulesFromFolder, $modulesFromDB);

        static::$mod = new Module($allMods);
    }

    /**
     * Install the given module to DB table
     *
     * @param string $module slug of module(folder name)
     * @return boolean
     */
    public static function install($module, $uri, $setEnable = false, $refresh = true)
    {
        $module = ucfirst($module);

        // Check the given module is truely
        if (! static::checkModule($module)) {
            return false;
        }

        return static::$mod->install($module, $uri, $setEnable, $refresh);
    }

    /**
     * UnInstall the given module to DB table
     *
     * @param string $module slug of module(folder name)
     * @return boolean
     */
    public static function uninstall($module, $uri)
    {
        $module = ucfirst($module);
        // Check the given module is truely
        if (! static::checkModule($module)) {
            return false;
        }

        // If module is core, Don't allow to uninstall
        if (static::$mod->isCore($module)) {
            return false;
        }

        // If module is not install, return true
        if (false === static::$mod->getData($module, 'installed')) {
            return true;
        }

        return static::$mod->uninstall($module, $uri);
    }

    /**
     * Upgrade the given module.
     *
     * @param string $module Name of Module
     * @return boolean
     **/
    public static function upgrade($module, $uri)
    {
        $module = ucfirst($module);

        if (! static::checkModule($module)) {
            return false;
        }

        // If module doesn't need to upgrade, return false
        if (false === static::$mod->getData($module, 'upgradeRequire')) {
            return false;
        }

        return static::$mod->upgrade($module, $uri);
    }

    /**
     * Boot the Module
     *
     * @return void
     **/
    public static function boot($module)
    {
        $module = ucfirst($module);

        static::$mod->boot($module);
    }

    /**
     * Get the Admin Menu for Module
     *
     * @param Reborn\Util\Menu
     * @param string $module
     * @return mixed
     **/
    public static function adminMenu(Menu $menu, $module)
    {
        $module = ucfirst($module);

        $modUri = static::$mod->getData($module, 'uri');

        return static::$mod->adminMenu($menu, $modUri);
    }

    /**
     * undocumented function
     *
     * @param string $module Module name
     * @return void
     **/
    public static function settings($module)
    {
        $module = ucfirst($module);

        return static::$mod->settings($module);
    }

    /**
     * Get the toolbar (Use at adminpanel) for given module.
     *
     * @return array
     **/
    public static function moduleToolbar($module)
    {
        $module = ucfirst($module);

        return static::$mod->adminToolbar($module);
    }

    /**
     * Check the given module is truely or not.
     * First - check the module is really exits
     * Second - check the module's module.php file
     * Third - check the module.php format is correct?
     *
     * @param string $module
     * @return boolean
     **/
    protected static function checkModule($module)
    {
        $module = ucfirst($module);

        if (!static::find($module)) {
            return false;
        }

        return true;
    }

    /**
     * Setup the given modules. Module list from Forlder and DB.
     * Reborn make module is enable or disable at this stage
     *
     *
     * @param array $modulesFromFolder Module list array from module folder
     * @param array $modulesFromDB Modulelist array from module DB table
     * @return array
     **/
    protected static function moduleSetup($modulesFromFolder, $modulesFromDB)
    {
        foreach ($modulesFromFolder as $key => $mod) {
            // Check folder have on db? If true, this module is installed.
            if (isset($modulesFromDB[$key])) {

                $modulesFromFolder[$key]['installed'] = true;

                if ($modulesFromDB[$key]['enabled']) {
                    $modulesFromFolder[$key]['enabled'] = true;
                } else {
                    $modulesFromFolder[$key]['enabled'] = false;
                }

                if ($modulesFromDB[$key]['version'] == $modulesFromFolder[$key]['version'])
                {
                    $modulesFromFolder[$key]['upgradeRequire'] = false;
                } else {
                    $modulesFromFolder[$key]['upgradeRequire'] = true;
                }

                // Setup Module URI
                if ($modulesFromFolder[$key]['allowUriChange']) {
                    if ($modulesFromFolder[$key]['uri'] != $modulesFromDB[$key]['uri']) {
                        $modulesFromFolder[$key]['uri'] = $modulesFromDB[$key]['uri'];
                    }
                }

                $modulesFromFolder[$key]['dbVersion'] = $modulesFromDB[$key]['version'];
            } else {
                $modulesFromFolder[$key]['installed'] = false;
                $modulesFromFolder[$key]['enabled'] = false;
                $modulesFromFolder[$key]['upgradeRequire'] = false;
            }
        }

        return $modulesFromFolder;
    }

    /**
     * Find the module by given name
     *
     * @param string $module Name of module
     * @return boolean
     **/
    protected static function find($module)
    {
        if (static::$mod->has($module)) {
            return true;
        }

        $paths = array(CORE_MODULES, MODULES);

        foreach ($paths as $path) {
            if (is_dir($path.$module)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find All modules from DB table
     *
     * @return array
     **/
    protected static function findFromDB()
    {
        $modFromDB = array();

        $mods = DB::table(static::$_table)->get();

        foreach ($mods as $mod) {
            $name = $mod->name;
            $modFromDB[$name]['uri'] = $mod->uri;
            //$modFromDB[$name]['description'] = $mod->description;
            $modFromDB[$name]['enabled'] = ($mod->enabled == '0') ? false : true;
            $modFromDB[$name]['version'] = $mod->version;
        }

        return $modFromDB;
    }

    /**
     * Find All modules from Module Folders
     * Core Modules and Addon Modules
     *
     * @return array
     **/
    protected static function findAll()
    {
        $modPaths = array(CORE_MODULES, MODULES);

        $allFolder = array();

        foreach ($modPaths as $path) {
            $iterator = new \DirectoryIterator($path);

            foreach ($iterator as $dir) {

                if (!$dir->isDot() and $dir->isDir()) {
                    $mod_name = $dir->getFileName();
                    $mod_path = $dir->getPath().DS.$mod_name.DS;

                    // Check the require files for module
                    if (static::fileCheck($mod_path, $mod_name)) {

                        // Check module name is already exits
                        if (isset($allFolder[$mod_name])) {
                            throw new ModuleException("Module slug {$mod_name} is already exits.");
                        }

                        $modData = static::getModuleInfo($mod_path, $mod_name);

                        $allFolder[$mod_name] = $modData;
                    }
                }
            }
        }

        return $allFolder;
    }

    /**
     * Check the require files for module is exists or not.
     *
     * @param string $path Module path
     * @param string $name Module name
     * @return boolean
     **/
    protected static function fileCheck($path, $name)
    {
        // Check the {ModuleName}Info.php file at module main root
        if (!file_exists($path.$name.static::$infoFile)) {
            $msg = sprintf(static::$noInfoFile, static::$infoFile, $name);
            throw new ModuleException($msg);
        }

        // Check the {ModuleName}Installer.php file at module main root
        if (!file_exists($path.$name.static::$installer)) {
            $msg = sprintf(static::$noInfoFile, static::$installer, $name);
            throw new ModuleException($msg);
        }

        // Check the Bootstrap.php file at module main root
        if (!file_exists($path.static::$bootstrap)) {
            $msg = sprintf(static::$noInfoFile, static::$bootstrap, $name);
            throw new ModuleException($msg);
        }

        return true;
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public static function getModuleInfo($path, $name)
    {
        // Require the {ModuleName}Info.php
        require $path.$name.static::$infoFile;

        $classname = $name.'\\'.$name.'Info';

        $class = new $classname;

        if (! $class instanceof AbstractInfo) {
            throw new ModuleException(
                        sprintf('% Class must be instanceof AbstractInfo', $classname)
                    );

        }

        return $class->getAll();
    }

    /**
     * Magic method __callStatic
     */
    public static function __callStatic($method, $args)
    {
        if (is_callable(array(static::$mod, $method))) {
            return call_user_func_array(array(static::$mod, $method), (array)$args);
        }

        throw new \BadMethodCallException("{$method} is not callable");
    }

} // END class ModuleManager
