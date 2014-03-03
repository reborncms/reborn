<?php

namespace Reborn\Cache;

use Reborn\Cores\Application;

/**
 * Cache Manager class for Reborn
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
class CacheManager
{
    /**
     * Cache Driver Object Instance
     *
     * @var \Reborn\Cache\CacheDriverInterface
     */
    protected $cacheInstance;

    /**
     * Cache driver lists
     *
     * @var array
     **/
    protected $driverList = array(
            'file'  => 'Reborn\Cache\Driver\File',
            //'db'  => 'Reborn\Cache\Driver\Database',
        );

    /**
     * Default constructor method for cache class
     *
     * @return \Reborn\Cache\CacheManager
     */
    public function __construct(Application $app)
    {
        $path = $app['config']->get('cache.file.storage_path');

        $this->createDriver($app['config']->get('cache.default_driver'), $app);

        return $this;
    }

    /**
     * Get the cache driver Instance
     *
     * @return \Reborn\Cache\CacheDriverInterface
     **/
    public function getDriver()
    {
        return $this->cacheInstance;
    }

    /**
     * Extend New Driver for the Cache
     * example :
     * <code>
     *      $dri = array('memcache' => 'RB\MCache\Memcache');
     *      Cache::extend($dri);
     * </code>
     *
     * @param  array $lists Driver list array
     * @return void
     **/
    public function extend($lists)
    {
        if (!is_array($lists)) {
            throw new \InvalidArgumentException("Argument must be array!");
        }

        $this->driverList = array_merge($this->driverList, $lists);
    }

    /**
     * Create the Cache Driver Instance
     *
     * @param  string                    $driver Supported cache driver name
     * @param  \Reborn\Cores\Application $app
     * @return void
     */
    public function createDriver($driver, $app)
    {
        if (array_key_exists($driver, $this->driverList)) {
            $this->cacheInstance = new $this->driverList[$driver]($app);
        } else {
            throw new \InvalidArgumentException("Driver type {$driver} is does't supprot.");
        }
    }

    /**
     * Set the Cache File Path
     *
     * @param  string                     $path
     * @return \Reborn\Cache\CacheManager
     **/
    public function setCachePath($path)
    {
        $this->cacheInstance->setPath($path);

        return $this;
    }

    /**
     * Dynamically method call for static
     *
     * @param  string $method
     * @param  array  $param
     * @return mixed
     **/
    public function __call($method, $param)
    {
        return call_user_func_array(array($this->getDriver(), $method), (array) $param);
    }

} // END class CacheManager
