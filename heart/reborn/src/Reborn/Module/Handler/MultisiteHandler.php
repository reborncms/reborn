<?php

namespace Reborn\Module\Handler;

use Reborn\Cores\Application;
use Reborn\Connector\DB\DBManager as DB;

/**
 * Base (Normal) Module Handler
 *
 * @package Reborn\Module
 * @author MyanmarLinks Professional Web Development Team
 **/
class MultisiteHandler extends BaseHandler
{
	/**
	 * Database prefix name.
	 *
	 * @var string
	 **/
	protected $prefix;

	/**
	 * Module content path for site.
	 *
	 * @var string
	 **/
	protected $module_path;

	/**
	 * Override Default instance from BaseHandler
	 *
	 * @param \Reborn\Cores\Application $app
	 * @param string|null $prefix
	 * @return void
	 **/
	public function __construct(Application $app, $prefix = null)
	{
		$this->prefix = $prefix;

		parent::__construct($app);
	}

	/**
	 * Build for new site.
	 *
	 * @return boolean
	 **/
	public function buildForNewSite()
	{
		list($public, $private) = $this->findModuleForNewSite();

		$this->installForNewSite($public);

		return $this->installForNewSite($private, true);
	}

	/**
	 * Remove modules of site.
	 *
	 * @return boolean
	 **/
	public function removeSiteModules()
	{
		list($public, $private) = $this->findModuleForNewSite();

		$this->removeForNewSite($public);

		return $this->removeForNewSite($private, true);
	}

	/**
	 * Set table prefix name.
	 *
	 * @param string $prefix
	 * @return void
	 **/
	public function setModulePath($path)
	{
		$this->module_path = $path.DS.'modules'.DS;
	}

	/**
	 * Set table prefix name.
	 *
	 * @param string $prefix
	 * @return void
	 **/
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * Get table prefix for module table.
	 *
	 * @return string
	 **/
	public function getPrefix()
	{
		if (is_null($this->prefix)) {
			return $this->app->site_manager->tablePrefix();
		}

		return $this->prefix.'_';
	}

	/**
	 * Get Database table name
	 *
	 * @return string
	 **/
	protected function getTable()
	{
		return $this->getPrefix().'modules';
	}

	/**
	 * Get module folder paths
	 *
	 * @return array
	 **/
	protected function getModulePaths()
	{
		if (is_null($this->module_path)) {
			return array(CORE_MODULES, MODULES, SHARED.'modules'.DS);
		}

		return array(CORE_MODULES, BASE_CONTENT.$this->module_path, SHARED.'modules'.DS);
	}

	/**
	 * Find Public (Core and Shared) and Private Modules.
	 *
	 * @return array
	 **/
	protected function findModuleForNewSite()
	{
		$public = $this->findFrom(array(CORE_MODULES, SHARED.'modules'.DS));
		$private = $this->findFrom(BASE_CONTENT.$this->module_path);

		return array(
			$this->createModuleBuilder($public),
			$this->createModuleBuilder($private)
		);
	}

	/**
	 * Install modules for new site.
	 *
	 * @param array $all
	 * @param boolean $force
	 * @return boolean
	 **/
	protected function installForNewSite(array $all, $force = false)
	{
		foreach ($all as $name => $module) {

			if ($name !== 'setting' and $name !== 'module') {
				// Force install for private module and
				// install only not sharable data for public
				if ($force) {
					$this->moduleProcessing($module);
				} elseif (!$module->shared_data) {
					$this->moduleProcessing($module);
				}
			}

        	DB::table($this->getTable())
                ->insert(
                    array(
                        'uri' => $module->uri,
                        'name' => $name,
                        'enabled' => 1,
                        'version' => $module->version
                    )
                );
		}

		return true;
	}

	/**
	 * UnInstall modules from site.
	 *
	 * @param array $all
	 * @param boolean $force
	 * @return boolean
	 **/
	protected function removeFromSite(array $all, $force = false)
	{
		foreach ($all as $name => $module) {

			if ($name !== 'setting' and $name !== 'module') {
				// Force uninstall for private module and
				// uninstall only not sharable data for public
				if ($force) {
					$this->moduleProcessing($module, 'uninstall');
				} elseif (!$module->shared_data) {
					$this->moduleProcessing($module, 'uninstall');
				}
			}

			DB::table($this->getTable())
                    ->where('uri', '=', $module->uri)
                    ->where('name', '=', $name)
                    ->delete();
		}

		return true;
	}

	/**
	 * Make Module Install?Uninstall processing.
	 *
	 * @param \Reborn\Module\Builder $module
	 * @param string $method Processing method. (install or uninstall)
	 * @return void
	 **/
	protected function moduleProcessing($module, $method = 'install')
	{
		$prefix = $this->getPrefix();

		try {
            $processor = $this->getInstaller($module);
            $processor->{$method}($prefix);
        } catch (\Exception $e) {
            return $e;
        }
	}

} // END class MultisiteHandler
