<?php

namespace Reborn\Cache;

/**
 * Interface for Cache Folder Storage
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
interface CacheFolderStoreInterface
{

    /**
     * Delete the cache by Folder
     *
     * @param  string  $key
     * @return boolean
     */
    public function deleteFolder($folder);

}
