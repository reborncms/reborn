<?php

namespace Reborn\Cache;

/**
 * Interface for Cache Namespace Storage
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
interface CacheNamespaceStoreInterface
{

    /**
     * Delete the cache by Namespace
     *
     * @param string $namespace Namespace of the cache
     */
    public function deleteNamespace($namespace);

}
