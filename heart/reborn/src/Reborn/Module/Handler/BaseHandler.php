<?php

namespace Reborn\Module\Handler;

use Reborn\Util\Str;
use Reborn\Cores\Application;
use Reborn\Module\Builder;
use Reborn\Module\AbstractInfo;
use Reborn\Module\AbstractBootstrap;
use Reborn\Module\AbstractInstaller;
use Reborn\Exception\ModuleException;
use Reborn\Connector\DB\DBManager as DB;

/**
 * Base (Normal) Module Handler
 *
 * @package Reborn\Module
 * @author MyanmarLinks Professional Web Development Team
 **/
class BaseHandler
{
	/**
	 * Application IOC Container
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * Module data lists
	 *
	 * @var array
	 **/
	protected $modules;

	/**
	 * Default instance method
	 *
	 * @param \Reborn\Cores\Application $app
	 * @return void
	 **/
	public function __construct(Application $app)
	{
		$this->app = $app;

		$all = $this->prepareFromDatabase();

		$this->modules = $this->createModuleBuilder($all);
	}

	/**
     * Register for Installed Modules
     *
     * @return void
     **/
    public function registerInstalledModules()
    {
        // First register the core modules
        $this->coreModulesRegister();

        // Second register the addon modules
        $this->otherModulesRegister();
    }

	/**
	 * Get all register module.
	 *
	 * @return array
	 **/
	public function getAll()
	{
		return $this->modules;
	}

	/**
	 * Get module by name or uri.
	 * Find with "uri" is fallback of "name".
	 * Because sometime need to find module with URI Segment.
	 *
	 * @param string $name
	 * @param string|null $key
	 * @return \Reborn\Module\Builder|null
	 **/
	public function get($name, $key = null)
	{
        $name = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $name));

		$found = null;

		if (isset($this->modules[$name])) {
			$found = $this->modules[$name];
		} else {
            // Fallback with "uri"
            foreach ($this->modules as $module) {
                if ($name === $module->uri) {
                    $found = $module;
                }
            }
        }

		if ( !is_null($key) and !is_null($found) ) {
			return $found->{$key};
		}

		return $found;
	}

	/**
	 * Check module has or not.
	 *
	 * @return boolean
	 **/
	public function has($name)
	{
		if (!is_null($this->get($name))) {
			return true;
		}

		return false;
	}

	/**
     * Get modules by filter on their key and value
     *
     * @param string $key Module's data key name
     * @param mixed $value Value for Module's data key
     * @return array
     **/
    public function getModulesByFilter($key, $value)
    {
        $all = $this->modules;

        $modules = array();

        $modules = array_filter($all, function($m) use($key, $value) {
            return ($value == $m->{$key});
        });

        return $modules;
    }

	/**
	 * Module load with name.
	 *
	 * @param string $name
	 * @return boolean
	 **/
	public function load($name)
	{
		$module = $this->get($name);

		if(is_null($module)) return false;

		return $module->load();
	}

	/**
	 * Bootable the Module
	 *
	 * @param string $name
	 * @return boolean
	 **/
	public function boot($name)
	{
		$module = $this->get($name);

		if(is_null($module)) {
			throw new ModuleException("Module $name not found to Boot!");
		}

		$class = $this->getBootstrap($module);

        if ($class) {
            $class->boot();

            return true;
        }

		return false;
	}

    /**
     * Find brand new modules.
     *
     * @return array
     **/
    public function findNews()
    {
        $installed = array_keys($this->modules);
        $all = $this->findAll();

        $news = array_diff(array_keys($all), $installed);

        $results  = array();

        foreach ($news as $m) {
            $path = $this->findPath($m);
            $results[$m] = $this->getModuleInfo($path, $m);
        }

        return $results;
    }

	/**
     * Find All modules from Module Folders
     * Core Modules, Shared Modules and Addon Modules
     *
     * @return array
     **/
    public function findAll()
    {
        $paths = $this->getModulePaths();

        return $this->findFrom($paths);
    }

    /**
     * Find All modules from given path
     *
     * @param string|array $paths
     * @return array
     **/
    public function findFrom($paths)
    {
        $paths = (array) $paths;

        $all = array();

        foreach ($paths as $path) {
            $iterator = new \DirectoryIterator($path);

            foreach ($iterator as $dir) {

                if (!$dir->isDot() and $dir->isDir()) {
                    $mod_name = $dir->getFileName();
                    $mod_path = $dir->getPath().DS.$mod_name.DS;

                    // Check module name is already exits
                    if (isset($all[$mod_name])) {
                        throw new ModuleException("Module slug {$mod_name} is already exits.");
                    }

                    $all[$mod_name] = $this->getModuleInfo($mod_path, $mod_name);
                }
            }
        }

        return $all;
    }

    /**
     * Get Module Info
     *
     * @return \Reborn\Module\AbstractInfo
     **/
    public function getModuleInfo($path, $name)
    {
        $name = Str::studly($name);

        $path = Str::endIs($path, DS);

        // Require the {ModuleName}Info.php
        if (! file_exists($path.$name.'Info.php') ) {
        	throw new ModuleException("Module Info file missing at $name");
        }

        $classname = $name.'\\'.$name.'Info';

        if (!class_exists($classname)) {
            require $path.$name.'Info.php';
        }

        $class = new $classname;

        if (! $class instanceof AbstractInfo) {
            throw new ModuleException(
                        sprintf('% Class must be instanceof AbstractInfo', $classname)
                    );

        }

        return $class->getAll();
    }

    /**
     * Install the given module to DB table
     *
     * @param string $name
     * @return boolean
     **/
    public function install($name)
    {
        if ($this->has($name)) {
            throw new ModuleException("Module is already installed!");
        }

        $path = $this->findPath($name);
        $info = $this->getModuleInfo($path, $name);

        $result = $this->createModuleBuilder(array($name => $info));

        try {
            $processor = $this->getInstaller($result[$name]);
            $processor->install();
        } catch (\Exception $e) {
            if ($this->app->runInProduction()) {
                return false;
            }
            return $e;
        }

        return DB::table($this->getTable())
                ->insert(
                    array(
                        'uri' => $result[$name]->uri,
                        'name' => $name,
                        'enabled' => 1,
                        'version' => $result[$name]->version
                    )
                );
    }

    /**
     * UnInstall the given module to DB table
     *
     * @param string $name
     * @return boolean
     **/
    public function uninstall($name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if ($module->isCore()) {
                throw new ModuleException("Core Module doesn't allow to uninstall");
            }

            try {
                $processor = $this->getInstaller($module);
                $processor->uninstall();
            } catch (\Exception $e) {
                if ($this->app->runInProduction()) {
                    return false;
                }
                return $e;
            }

            return DB::table($this->getTable())
                    ->where('uri', '=', $module->uri)
                    ->where('name', '=', $name)
                    ->delete();
        }

        return false;
    }

    /**
     * Upgrade the given module to DB table
     *
     * @param string $name
     * @return boolean
     **/
    public function upgrade($name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if (!$module->needToUpdate()) return false;

            try {
                $processor = $this->getInstaller($module);
                $processor->upgrade($module->db_version);
            } catch (\Exception $e) {
                if ($this->app->runInProduction()) {
                    return false;
                }
                return $e;
            }

            return DB::table($this->getTable())
                    ->where('uri', '=', $module->uri)
                    ->where('name', '=', $name)
                    ->update(array('version' => $module->version));
        }

        return false;
    }

    /**
     * Enable the given module to DB table
     *
     * @param string $name
     * @return boolean
     **/
    public function enable($name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if ($module->isEnabled()) {
                throw new ModuleException("Module is already enabled!");
            }

            $ok = DB::table($this->getTable())
                    ->where('uri', '=', $module->uri)
                    ->where('name', '=', $name)
                    ->update(array('enabled' => 1));

            if ($ok) {
                $module->enabled = true;
                return true;
            }
        }

        return false;
    }

    /**
     * Disable the given module to DB table
     *
     * @param string $name
     * @return boolean
     **/
    public function disable($name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if (!$module->isEnabled()) {
                throw new ModuleException("Module is already disabled!");
            }

            $ok = DB::table($this->getTable())
                    ->where('uri', '=', $module->uri)
                    ->where('name', '=', $name)
                    ->update(array('enabled' => 0));

            if ($ok) {
                $module->enabled = false;
                return true;
            }
        }

        return false;
    }

    /**
     * Module Toolbar for Admin Panel
     *
     * @param string $name
     * @return array|null
     **/
    public function moduleToolbar($name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if ($module->isEnabled()) {
                $class = $this->getBootstrap($module);

                return $class->moduleToolbar();
            }
        }

        return null;
    }

    /**
     * Admin Menu for the given Module
     *
     * @param \Reborn\Util\Menu $menu
     * @param string $name
     * @return array|null
     **/
    public function adminMenu(\Reborn\Util\Menu $menu, $name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if ($module->isEnabled()) {
                $class = $this->getBootstrap($module);

                return $class->adminMenu($menu, $module->uri);
            }
        }

        return null;
    }

    /**
     * Settings list from module
     *
     * @param string $name
     * @return array|null
     **/
    public function settings($name)
    {
        if ($this->has($name)) {
            $module = $this->get($name);

            if ($module->isEnabled()) {
                $class = $this->getBootstrap($module);

                return $class->settings();
            }
        }

        return null;
    }

	/**
	 * Get Database table name
	 *
	 * @return string
	 **/
	protected function getTable()
	{
		return 'modules';
	}

	/**
	 * Prepare modules from database.
	 *
	 * @return void
	 **/
	protected function prepareFromDatabase()
	{
		$all = array();

        $mods = DB::table($this->getTable())->get();

        foreach ($mods as $mod) {
            $name = strtolower($mod->name);
            $all[$name]['uri'] = $mod->uri;
            $all[$name]['enabled'] = ($mod->enabled == '0') ? false : true;
            $all[$name]['version'] = $mod->version;
            $all[$name]['installed'] = true;
        }

        return $all;
	}

	/**
	 * Create module lists with Builder
	 *
	 * @param array $all
	 * @return array
	 **/
	protected function createModuleBuilder(array $all)
	{
        $modules = array();

		foreach ($all as $name => $data) {
			$path = $this->findPath($name);

			if(is_null($path)) continue;

			$info =  $this->getModuleInfo($path, $name);

			if ($info['allow_uri_change']) {
				$info['uri'] = $data['uri'];
			}

			$info['enabled'] = isset($data['enabled']) ? $data['enabled'] : false;
			$info['db_version'] = $data['version'];

			$module = new Builder($path, $info);
			$module->load();

			$modules[$name] = $module;
		}

        return $modules;
	}

	/**
	 * Get module folder paths
	 *
	 * @return array
	 **/
	protected function getModulePaths()
	{
		return array(CORE_MODULES, MODULES, SHARED.'modules'.DS);
	}

    /**
     * Find module's directory path
     *
     * @return string|null
     **/
    protected function findPath($name)
    {
    	$paths = $this->getModulePaths();

    	foreach ($paths as $path) {
    		if (file_exists($path.$name)) {
    			return $path.$name;
    		}
    	}

    	return null;
    }

    /**
     * Register Core Modules.
     *
     * @return void
     **/
    protected function coreModulesRegister()
    {
        $cores = $this->getModulesByFilter('isCore', true);

        foreach ($cores as $name => $core) {

            if(!$core->isEnabled()) {
                continue;
            }

            $bootstrap = $this->getBootstrap($core);

            $bootstrap->register();
        }
    }

    /**
     * Register Addon Modules.
     *
     * @return void
     **/
    protected function otherModulesRegister()
    {
        $addons = $this->getModulesByFilter('isCore', false);

        foreach ($addons as $name => $addon) {

            if(!$addon->isEnabled()) {
                continue;
            }

            $bootstrap = $this->getBootstrap($addon);
            $bootstrap->register();
        }
    }

    /**
     * Get the Module's Bootstrap file
     *
     * @param string $module Module name
     * @return BootstrapClass|false|throw
     */
    protected function getBootstrap($module)
    {
        $bootFile = $module->path.DS.'Bootstrap.php';

        if (file_exists($bootFile)) {
            $classname = $module->ns.'\Bootstrap';

            if (! class_exists($classname)) {
                require $bootFile;
            }

            $class = new $classname($this->app);

            if ($class instanceof AbstractBootstrap) {
                return $class;
            } else {
                throw new ModuleException(
                	sprintf(
                		'% Class must be instanceof AbstractBootstrap',
                		$classname
                	)
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
        $insFile = $module->path.DS.$module->ns.'Installer.php';

        if (file_exists($insFile)) {
            $classname = $module->ns.'\\'.$module->ns.'Installer';
            if (! class_exists($classname)) {
                require $insFile;
            }
            $class = new $classname();

            if ($class instanceof AbstractInstaller) {
                return $class;
            } else {
                throw new ModuleException(
                    sprintf(
                		'% Class must be instanceof AbstractInstaller',
                		$classname
                	)
                        );
            }
        }

        return false;
    }

    /**
     * Dynamically accsess method from Builder.
     *
     * @param string $method
     * @param array $args
     * @return void
     **/
    public function __call($method, $args)
    {
    	$name = array_shift($args);
    	$module = $this->get($name);

    	if (is_null($module) || !is_callable(array($module, $method))) {
    		throw new \BadMethodCallException("Method [ $method ] not found!");
    	}

    	return call_user_func_array(array($module, $method), $args);
    }

} // END class BaseHandler
