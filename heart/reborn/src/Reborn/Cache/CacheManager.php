<?php

namespace Reborn\Cache;

use Reborn\Config\Config;
use Reborn\Cores\Facade;

/**
 * Cache Manager class for Reborn
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
class CacheManager
{

    /**
     * Cache Driver Object
     *
     * @var Reborn\Cache\CacheDriverInterface
     */
    protected $cacheInstance;

    /**
     * Cache driver lists
     *
     * @var array
     **/
    protected $driverList = array(
            'file'  => 'Reborn\Cache\Driver\File',
            'db'  => 'Reborn\Cache\Driver\Database',
        );

    /**
     * Default constructor method for cache class
     *
     * @return \Reborn\Cache\CacheManager
     */
    public function __construct()
    {
        $this->createDriver(Config::get('cache.default_driver'));

        return $this;
    }

    /**
     * Get the cache driver Instance
     *
     * @return \Reborn\Cache\DriverObject
     **/
    public function getDriver()
    {
        return $this->cacheInstance;
    }

    /**
     * Add New Driver for the Cache
     * example :
     * <code>
     *      $dri = array('memcache' => 'RB\MCache\Memcache');
     *      Cache::addNewDriver($dri);
     * </code>
     *
     * @param array $lists Driver list array
     * @return void
     **/
    public function addNewDriver($lists)
    {
        if (!is_array($lists)) {
            throw new \InvalidArgumentException("Argument must be array!");
        }

        $this->$driverList = array_merge($this->$driverList, $lists);
    }

    /**
     * Create the Cache Driver Instance
     *
     * @param string $driver Supported cache driver name
     * @return void
     */
    public function createDriver($driver)
    {
        if (array_key_exists($driver, $this->driverList)) {
            $this->cacheInstance = new $this->driverList[$driver]();
        } else {
            throw new \InvalidArgumentException("Driver type {$driver} is does't supprot.");
        }
    }

    /**
     * Set the Cache File Path
     *
     * @param string $path
     * @return \Reborn\Cache\CacheManager
     **/
    public function setCachePath($path)
    {
        $this->cacheInstance->setPath($path);

        return $this;
    }

    /**
     * Get the cache data with given key
     *
     * @param string $key Key name for the cache data
     * @return mixed
     */
    public static function get($key)
    {
        return static::getIns()->get($key);
    }

    /**
     * Check the given cache key is has or not
     *
     * @param string $key
     * @return boolean
     **/
    public static function has($key)
    {
        return static::getIns()->has($key);
    }

    /**
     * Get the cache data after given value is set.
     *
     * @param string $key Key name for the cache
     * @param mixed $value Cache data value
     * @param integer $time Expire time for cache
     * @return mixed
     **/
    public static function getAfterSet($key, $value, $time = 10080)
    {
        $ins = static::getIns();
        $ins->set($key, $value, $time);

        return $ins->get($key);
    }

    /**
     * Set the cache data.
     * (time is minute. default is 10080 min. 1 week)
     *
     * @param string $key Key name for the cache
     * @param mixed $value Cache data value
     * @param integer $time Expire time for cache
     * @return $this
     */
    public static function set($key, $value, $time = 10080)
    {
        return static::getIns()->set($key, $value, $time);
    }

    /**
     * Delete the cache
     *
     * @param string $key Key name for the cache
     * @return boolean
     */
    public static function delete($key)
    {
        return static::getIns()->delete($key);
    }

    /**
     * Delete the cache by Folder Name
     * Cache is save in the module_name folder.
     * If you want to delete all cache from module.
     * Use this method.
     *
     * @param string $folder Cache Folder Name
     * @return boolean
     */
    public static function deleteFolder($folder)
    {
        return static::getIns()->deleteFolder($folder);
    }

    /**
     * Get the cache driver instance for the static method call.
     *
     * @return Reborn\Cache\DriverObject
     **/
    protected static function getIns()
    {
        return Facade::getApplication()->cache->getDriver();
    }

} // END class CacheManager
