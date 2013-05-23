<?php

namespace Reborn\Cache;

/**
 * Interface for Cache Driver
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
interface CacheDriverInterface
{

    /**
     * Set the cache
     */
    public function set($key, $value, $time);

    /**
     * Get the cache
     */
    public function get($key);

    /**
     * Get the cache data after given value is set.
     */
    public function getAfterSet($key, $value, $time = 10080);

    /**
     * Check the given cache key is has or not
     */
    public function has($key);

    /**
     * Delete the cache File
     */
    public function delete($key);

    /**
     * Delete the cache by Folder
     */
    public function deleteFolder($folder);

} // END interface CacheDriverInterface
