<?php

namespace Reborn\Cache\Driver;

use Reborn\Config\Config;
use Reborn\Cache\CacheDriverInterface;
use Reborn\Cores\Facade;
use Reborn\Filesystem\File as FileSystem;
use Reborn\Filesystem\Directory as Dir;

/**
 * File Cache Driver for Reborn
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
class File implements CacheDriverInterface
{

    /**
     * Request Object
     *
     * @var Reborn\Http\Request
     **/
    protected $request = null;

    /**
     * Cache storage file path
     *
     * @var string
     **/
    protected $path;

    /**
     * File Extension for cache file
     *
     * @var string
     **/
    protected $extension = '.cache';

    /**
     * Default constructor method
     *
     */
    public function __construct()
    {
        $this->path = Config::get('cache.file.storage_path');

        if (is_null($this->request)) {
            $this->request = Facade::getApplication()->request;
        }

        if (!is_dir($this->path)) {
            $this->createFolder($this->path);
        }
    }

    /**
     * Set the cache data from given key name with given data
     *
     * @param string $key
     * @param mixed $value Data for cache
     * @param string $module Module name
     * @param integer $time Cache life time(expire)
     * @return mixed
     */
    public function set($key, $value, $module = null, $time = 10080)
    {
        list($filename, $file) = $this->keyParse($key, $module);

        $path = str_replace($filename.$this->extension, '', $file);

        if (!is_dir($path)) {
            $this->createFolder($path);
        }

        if (file_exists($file)) {
            FileSystem::delete($file);
        }

        $this->write($path, $filename.$this->extension, $value, $time);
    }

    /**
     * Get the cache data from given key name
     *
     * @param string $key
     * @param string $module Module name
     * @return mixed
     */
    public function get($key, $module = null)
    {
        list($filename, $file) = $this->keyParse($key, $module);

        if (file_exists($file)) {
            $data = FileSystem::getContent($file);

            if ($this->checkExpire($data)) {
                return $this->unserializer(substr($data, 10));
            } else {
                $this->delete($key);

                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Get the cache data after given value is set.
     *
     * @param string $key Key name for the cache
     * @param mixed $value Cache data value
     * @param integer $time Expire time for cache
     * @return mixed
     **/
    public function getAfterSet($key, $value, $time = 10080)
    {
        $this->set($key, $value, $time);

        return $this->get($key);
    }

    /**
     * Check the given cache is has or not
     *
     * @param string $key
     * @param string $module Module name
     * @return boolean
     **/
    public function has($key, $module = null)
    {
        if (is_null($this->get($key, $module))) {
            return false;
        }

        return true;
    }

    /**
     * Delete the cache file
     *
     * @param string $key Cache file key name
     * @return void
     **/
    public function delete($key)
    {
        list($filename, $file) = $this->keyParse($key);

        return FileSystem::delete($file);
    }

    /**
     * Delete the cache by folder.
     * Notice :: Folder name is case sensative.
     *
     * @param string $folder Cache folder name
     * @return void
     **/
    public function deleteFolder($folder)
    {
        return Dir::delete($this->path.$folder);
    }

    /**
     * Set the Cache Folder Path
     *
     * @param string $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the Cache Folder Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Parse the given key to filename and full_file_path
     *
     * @param string $key
     * @param string $module Module name
     * @return array
     **/
    protected function keyParse($key, $module = null)
    {
        $module = (is_null($module)) ? $this->request->module : ucfirst($module);

        if (! is_null($module)) {
            $filename = md5($key);
            $file = $this->path.$module.DS.$filename.$this->extension;
        } else {
            $filename = md5($key);
            $file =  $this->path.$filename.$this->extension;
        }

        return array($filename, $file);
    }

    /**
     * Write the cache file in given path
     *
     * @param string $path Cache file saving path
     * @param string $filename Cache file name with file extension
     * @param mixed $value Cache data value
     * @param integer $time Cache expire time with minute
     * @return void
     */
    protected function write($path, $filename, $value, $time)
    {
        $data = $this->serializer($value);

        $expire = time() + ($time * 60); // Change minute to second

        $data = $expire.$data;
        FileSystem::put($path.$filename, $data);

        @chmod($path.$filename, 0777);
    }

    /**
     * Check the given cache is expire or not
     *
     * @param string $data Cache data
     * @return boolean
     */
    protected function checkExpire($data)
    {
        $time = substr($data, 0, 10);

        return ($time > time());
    }

    /**
     * Serialize the given data
     *
     * @param array|object $data
     * @return string
     */
    protected function serializer($data)
    {
        return serialize($data);
    }

    /**
     * Unserialize the given data
     *
     * @param string $data
     * @return array
     */
    protected function unserializer($data)
    {
        return unserialize($data);
    }

    /**
     * Create the folder for cache file
     *
     * @param string $path
     * @return boolean
     */
    protected function createFolder($path)
    {
        return (Dir::make($path, 0777, true)) ? true : false;
    }

} // END class File implements CacheDriverInterface
