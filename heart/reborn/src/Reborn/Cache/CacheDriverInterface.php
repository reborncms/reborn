<?php

namespace Reborn\Cache;

use Closure;

/**
 * Interface for Cache Driver
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
interface CacheDriverInterface
{

    /**
     * Set the Cache Data.
     * If Cache is already exists, make override
     * <code>
     *      // Make normal cache (store in cache path)
     *      Cache::set('app', 'Reborn CMS', 10)
     *
     *      // Make cache with namespace or folder name.
     *      // (store in cache_path/folder_name)
     *      Cache::set('Navigation::nav_groups', [1,2,3,4], 15)
     * </code>
     *
     * @param string $key Key name of Cache.
     * @param mixed $value
     * @param integer $time Cache ttl minutes.
     * @return void
     */
    public function set($key, $value, $time = 10080);

    /**
     * Get the cache data from cache by key
     *
     * @param string $key
     * @param mixed $default Default value for cache not found
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Get the cache data from cache or set the callback data.
     *
     * @param string $key
     * @param Closure $callback Callback method for solve cache value if require
     * @param integer $time Cache ttl minutes
     * @return mixed
     */
    public function solve($key, Closure $callback, $time = 10080);

    /**
     * Check the given cache key is has or not
     *
     * @param string $key
     * @return boolean
     */
    public function has($key);

    /**
     * Delete the cache data by key
     *
     * @param string $key
     * @return boolean
     */
    public function delete($key);

} // END interface CacheDriverInterface
