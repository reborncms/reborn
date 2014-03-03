<?php

namespace SiteManager\Services;

use Reborn\Cores\Setting;
use Reborn\Cores\Application;

class SiteMaker
{
    /**
     * Domain name for site
     *
     * @var string
     **/
    protected $domain;

    /**
     * Applicaion IOC container instance
     *
     * @var \Reborn\Cores\Application
     **/
    protected $app;

    /**
     * Register site data lists
     *
     * @var array
     **/
    protected $sites;

    /**
     * Shared by force module lists
     *
     * @var array
     **/
    protected $by_force = array();

    /**
     * Default instance method
     *
     * @param  \Reborn\Cores\Application $app
     * @param  string                    $domain
     * @return void
     **/
    public function __construct(Application $app, $domain, $modules = array())
    {
        $this->domain = $domain;

        $this->app = $app;

        $this->sites = $this->app['sites'];

        $this->by_force = $modules;
    }

    /**
     * Make new site
     *
     * @return boolean
     **/
    public function make()
    {
        $prefix = $this->getDbPrefix();

        $folder = $this->getContentPath();

        $this->makeSettingTable($prefix.'_');

        \Module::createNewModuleTable($prefix.'_');

        return $this->buildModules($prefix, $folder);
    }

    /**
     * Delete the site
     *
     * @return void
     **/
    public function delete()
    {
        $prefix = $this->getDbPrefix();

        $folder = $this->getContentPath();

        $this->removeModules($prefix, $folder);

        $this->removeSettingTable($prefix.'_');

        \Schema::dropIfExists($prefix.'_modules');
    }

    /**
     * Get database prefix name
     *
     * @return string
     **/
    protected function getDbPrefix()
    {
        $db = $this->sites['prefix'];

        if (isset($db[$this->domain])) {
            return $db[$this->domain];
        }

        throw new \RbException("Can't found database prefix for domain!");
    }

    /**
     * Get content path for site
     *
     * @return string
     **/
    protected function getContentPath()
    {
        if (isset($this->sites['content_path'][$this->domain])) {
            return $this->sites['content_path'][$this->domain];
        }

        throw new \RbException("Can't found content path at sites.php!");
    }

    /**
     * Make setting table for site
     *
     * @param  string $prefix
     * @return void
     **/
    public function makeSettingTable($prefix)
    {
        Setting::setTableForMultisite($prefix);
    }

    /**
     * Remove setting table of site
     *
     * @param  string $prefix
     * @return void
     **/
    public function removeSettingTable($prefix)
    {
        \Schema::dropIfExists($prefix.'settings');
    }

    /**
     * Build modules for site
     *
     * @param  string $prefix
     * @param  string $path
     * @return void
     **/
    protected function buildModules($prefix, $path)
    {
        $handler = new \Reborn\Module\Handler\MultisiteHandler($this->app, $prefix);
        $handler->setModulePath($path);

        return $handler->buildForNewSite($this->by_force);
    }

    /**
     * Remove modules of site
     *
     * @param  string $prefix
     * @param  string $path
     * @return void
     **/
    protected function removeModules($prefix, $path)
    {
        $handler = new \Reborn\Module\Handler\MultisiteHandler($this->app, $prefix);
        $handler->setModulePath($path);

        return $handler->removeSiteModules($this->by_force);
    }
}
