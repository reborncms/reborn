<?php

namespace Reborn\Module;

use Reborn\Config\Config;
use Reborn\Filesystem\File;
use Composer\Autoload\ClassLoader as Loader;
use Reborn\Connector\DB\DBManager as DB;
use Reborn\Exception\ModuleException;
use Reborn\Util\Menu;
use Reborn\Route\ControllerMap;

class Module
{
	/**
     * Variable for the all modules array lists
     *
     * @var array
     **/
    protected $modules = array();

	/**
     * Variable for loaded modules
     *
     * @var array
     **/
    protected $loaded = array();

    /**
     * Module table name
     *
     * @var string
     **/
	protected $_table = 'modules';

    /**
     * Module Information file name
     *
     * @var string
     **/
    protected $infoFile = 'Info.php';

    /**
     * Module Installer file name
     *
     * @var string
     **/
    protected $installFile = 'Installer.php';

    /**
     * Module Bootstrap file name
     *
     * @var string
     **/
    protected $bootstrap = 'Bootstrap.php';

	/**
     * Variables for module exception message
     *
     * @var string
     */
    protected $notFound = 'Module name { %s } doesn\'t exits in module path.';
    protected $alreadyInstalled = 'Module { %s } is already installed.';
    protected $notAbstract = 'Module %s\'s %s Class must be instanceof Abstract%s Class';

	/**
	 * Default constructor.
	 *
	 * @param array $modules
	 * @return void
	 **/
	public function __construct($modules)
	{
        $this->modules = $modules;

        $autoload = Config::get('app.module.autoload');

        foreach ($autoload as $load) {
            $this->load($load);
        }

        // Register Installed Module
        $this->registerInstalledModules();
	}

	/**
     * Get the all modules
     *
     * @return array
     **/
    public function getAll()
    {
        return $this->modules;
    }

    /**
     * Get module data by given module name
     *
     * @param string $module Name of modules
     * @param string $key Key of the module's data attrs, Default is null and return all
     * @return mixed
     **/
    public function getData($module, $key = null)
    {
        if ($moduleName = $this->checkModule($module)) {
            if (is_null($key)) {
                return $this->modules[$moduleName];
            }
            if (isset($this->modules[$moduleName][$key])) {
                return $this->modules[$moduleName][$key];
            }
        }

        return null;
    }

    /**
     * Get the Module Data by Module's URI Prefix
     *
     * @param string $uri Module URI Prefix String
     * @param string $key Key of the module's data attrs, Default is null and return all
     * @return mixed
     **/
    public function getByUri($uri, $key = null)
    {
        foreach ($this->modules as $name => $value) {
            if ($uri == $value['uri']) {
                if (is_null($key)) {
                    return $this->modules[$name];
                } else {
                    return isset($this->modules[$name][$key])
                            ? $this->modules[$name][$key]
                            : null;
                }
            }
        }

        return null;
    }

	/**
     * Check the given module is exists or not
     *
     * @param string $module Name of module
     * @return bool
     **/
    public function has($module)
    {
        if ($this->checkModule($module)) {
            return true;
        }

        return false;
    }

    /**
     * Check the given module is core module or not.
     *
     * @param string $module Module Name(slug)
     * @return boolean
     */
    public function isCore($module)
    {
        if ($moduleName = $this->checkModule($module)) {
            return $this->modules[$moduleName]['isCore'];
        }

        return false;
    }

    /**
     * Set the given module is enable.
     *
     * @param string $module
     * @return void
     **/
    public function enable($module, $uri)
    {
        if ($moduleName = $this->checkModule($module)) {
            if (!$this->modules[$moduleName]['enabled']) {
                $uri = $this->modules[$moduleName]['uri'];
                if (DB::table($this->_table)->where('uri', '=', $uri)
                                    ->where('name', '=', $moduleName)
                                    ->update(array('enabled' => 1))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Set the given module is disable.
     *
     * @param string $module
     * @return void
     **/
    public function disable($module, $uri)
    {
        if ($moduleName = $this->checkModule($module)) {
            if ($this->modules[$moduleName]['enabled']) {
                $uri = $this->modules[$moduleName]['uri'];
                if (DB::table($this->_table)->where('uri', '=', $uri)
                                    ->where('name', '=', $moduleName)
                                    ->update(array('enabled' => 0))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check given module is enabled or not
     *
     * @param string $module
     * @param boolean $throw If you set true, throw the Exception message
     * @return boolean
     **/
    public function isEnabled($module, $throw = false)
    {
        if ($moduleName = $this->checkModule($module)) {
            return $this->modules[$moduleName]['enabled'];
        }

         if ($throw) {
            throw new ModuleException(sprintf($this->notFound, $moduleName));
        }
        return false;
    }

    /**
     * Check given module is disabled or not
     *
     * @param string $module
     * @param boolean $throw If you set true, throw the Exception message
     * @return bool
     **/
    public function isDisabled($module, $throw = false)
    {
        return ! $this->isEnabled($module, $throw);
    }

    /**
     * Load the given module
     *
     * @param string $module
     * @return boolean
     **/
    public function load($module)
    {
        if (!class_exists('Composer\Autoload\ClassLoader')) {
            throw new RbException("ModuleLoader need \"Composer\Autoload\ClassLoader\"");
        }

        if ($moduleName = $this->checkModule($module)) {
            if (isset($this->loaded[$moduleName])) {
                return true;
            }
        }

        $path = str_replace($moduleName.DS, '', $this->modules[$moduleName]['path']);

        if (($moduleName == 'Contact') || $this->modules[$moduleName]['enabled']) {
            $loader = new Loader();
            $loader->add($moduleName, $path);
            $loader->register();
            $this->loadedModules[$moduleName] = true;

            return true;
        }

        return false;
    }

    /**
     * Install the given module to DB table
     *
     * @param string $module Name of module(folder name)
     * @param uri $uri Module URI
     * @param boolean $setEnable Set the module is enable after install.
     * @param boolean $refresh Refresh the Controller Map.
     * @return boolean
     */
    public function install($module, $uri, $setEnable = false, $refresh = true)
    {
        if (false === $this->modules[$module]['installed']) {

            try {
                $class = $this->getInstaller($module);

                if ($class) {
                    if ($class instanceof AbstractInstaller) {
                        $class->install();
                    }
                }
            } catch (\Exception $e) {
                return false;
            }

            // Install the Module Data into DB table
            $id = DB::table($this->_table)
                    ->insertGetId(array(
                            'uri' => $uri,
                            'name' => $module,
                            'description' => $this->modules[$module]['description'],
                            'enabled' => ($setEnable) ? 1 : 0,
                            'version' => $this->modules[$module]['version']
                        )
                    );
            if ($id) {
                if ($refresh) {
                    // Refresh the map cache
                    $this->mapRefresh();
                }

                 return true;
            }
        } else {
            throw new ModuleException(sprintf($this->alreadyInstalled, $module));
        }

        return false;
    }

    /**
     * UnInstall the given module to DB table
     *
     * @param string $module Name of module(folder name)
     * @param string $uri URI of Module
     * @return boolean
     */
    public function uninstall($module, $uri)
    {
        $class = $this->getInstaller($module);

        // If given module has Initialize.php File, call the uninstall method
        if ($class) {
            if ($class instanceof AbstractInstaller) {

                try {
                    $class->uninstall();
                } catch (\Exception $e) {
                    return false;
                }

                DB::table($this->_table)
                    ->where('uri', '=', $uri)
                    ->where('name', '=', $module)
                    ->delete();

                // Refresh the map cache
                $this->mapRefresh();

                return true;
            }
        }
        return false;
    }

    /**
     * Upgrade the given module.
     *
     * @param string $module Name of Module
     * @param string $uri URI of Module
     * @return boolean
     **/
    public function upgrade($module, $uri)
    {
        $class = $this->getInstaller($module);

        if ($class) {
            if ($class instanceof AbstractInstaller) {
                try {
                    $class->upgrade($this->modules[$module]['dbVersion']);
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        // Upgrade the DB's Module Version
        $newVersion = $this->modules[$module]['version'];
        if (DB::table($this->_table)
                ->where('uri', '=', $uri)
                ->where('name', '=', $module)
                ->update(array('version' => $newVersion))) {

            // Refresh the map cache
            $this->mapRefresh();

            return true;
        }

        return false;
    }

    /**
     * Boot the Module
     *
     * @param string $module
     * @return boolean
     **/
    public function boot($module)
    {
        if ($moduleName = $this->checkModule($module)) {
            if ($this->isEnabled($moduleName)) {

                $class = $this->getBootstrap($moduleName);

                if ($class) {
                    $class->boot();

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Admin Menu Generate the Module
     *
     * @param Reborn\Util\Menu
     * @param string $module
     * @return void
     **/
    public function adminMenu(Menu $menu, $module)
    {
        $moduleName = $this->checkModule($module);

        if ($this->isEnabled($moduleName) and
            $this->modules[$moduleName]['backendSupport']) {

            $class = $this->getBootstrap($moduleName);

            if ($class) {
                $class->adminMenu($menu, $this->modules[$moduleName]['uri']);
            }
        } else {
            return false;
        }
    }

    /**
     * Settings list from module
     *
     * @param string $module
     * @return array
     **/
    public function settings($module)
    {
        if ($this->modules[$module]['installed']) {

            $class = $this->getBootstrap($module);

            if ($class) {
                return $class->settings();
            }
        } else {
            return false;
        }
    }

    /**
     * Admin Menu Toolbar for the given Module
     *
     * @param string $module
     * @return array
     **/
    public function adminToolbar($module)
    {
        if ($this->isEnabled($module) and $this->modules[$module]['backendSupport']) {

            $class = $this->getBootstrap($module);

            if ($class) {
                return $class->moduleToolbar();
            }
        } else {
            return false;
        }
    }

    /**
     * Register for Installed Modules
     *
     * @return void
     **/
    protected function registerInstalledModules()
    {
        // First register the core modules
        $this->coreModulesRegiister();

        // Second register the addon modules
        $this->otherModulesRegister();
    }

    /**
     * Register Core Modules.
     *
     * @return void
     **/
    protected function coreModulesRegiister()
    {
        $cores = $this->getModulesByFilter('isCore', true);

        foreach ($cores as $name => $core) {

            if(!$this->isEnabled($name)) {
                continue;
            }

            $bootstrap = $this->getBootstrap($name);
            $bootstrap->register();
        }
    }

    /**
     * Register Addon Modules.
     *
     * @return void
     **/
    protected function otherModulesRegiister()
    {
        $addons = $this->getModulesByFilter('isCore', false);

        foreach ($addons as $name => $addon) {

            if(!$this->isEnabled($name)) {
                continue;
            }

            $bootstrap = $this->getBootstrap($name);
            $bootstrap->register();
        }
    }

    /**
     * Get modules by filter on their key and value
     *
     * @param string $key Module's data key name
     * @param mixed $value Value for Module's data key
     * @return array
     **/
    protected function getModulesByFilter($key, $value)
    {
        $all = $this->modules;

        $modules = array();

        $modules = array_filter($all, function($m) use($key, $value) {
            return ($value == $m[$key]);
        });

        return $modules;
    }

    /**
     * Refresh the Controller Map Cache File
     *
     * @return void
     **/
    protected function mapRefresh()
    {
        $cmap = new ControllerMap();
        $cmap->refresh();
    }

    /**
     * Check the moduel.
     *
     * @param string $module Module name (or) Module URI
     * @return string|null
     **/
    protected function checkModule($module)
    {
        $moduleName = ucfirst($module);

        // if Given vlue is Module name
        if (isset($this->modules[$moduleName])) {
            return $moduleName;
        } else {
            foreach ($this->modules as $key => $value) {
                if ($module == $value['uri']) {
                    return $key;
                }
            }
        }

        return null;
    }

    /**
     * Get the Module's Bootstrap file
     *
     * @param string $module Module name
     * @return BootstrapClass|false|throw
     */
    protected function getBootstrap($module)
    {
        $path = $this->modules[$module]['path'];
        $bootFile = $this->modules[$module]['path'].$this->bootstrap;

        if (file_exists($bootFile)) {
            $classname = $module.'\Bootstrap';
            if (! class_exists($classname)) {
                require $bootFile;
            }

            $class = new $classname(\Registry::get('app'));

            if ($class instanceof AbstractBootstrap) {
                return $class;
            } else {
                throw new ModuleException(
                            sprintf($this->notAbstract, $module, $classname, 'Bootstrap')
                        );
            }
        }

        return false;
    }

    /**
     * Get the Module's Installer file
     *
     * @param string $module Module name
     * @return InstallerClass|false|throw
     */
    protected function getInstaller($module)
    {
        $path = $this->modules[$module]['path'];
        $insFile = $this->modules[$module]['path'].$module.$this->installFile;

        if (file_exists($insFile)) {
            $classname = $module.'\\'.$module.'Installer';
            if (! class_exists($classname)) {
                require $insFile;
            }
            $class = new $classname();

            if ($class instanceof AbstractInstaller) {
                return $class;
            } else {
                throw new ModuleException(
                            sprintf($this->notAbstract, $module, $classname, 'Installer')
                        );
            }
        }

        return false;
    }

}
