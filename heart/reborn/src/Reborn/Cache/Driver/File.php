<?php

namespace Reborn\Cache\Driver;

use Closure;
use Reborn\Config\Config;
use Reborn\Filesystem\File as FileSystem;
use Reborn\Filesystem\Directory as Dir;
use Reborn\Cache\CacheDriverInterface;
use Reborn\Cache\CacheFolderStoreInterface;

/**
 * File Cache Driver for Reborn
 *
 * @package Reborn\Cache
 * @author Myanmar Links Professional Web Development Team
 **/
class File implements CacheDriverInterface, CacheFolderStoreInterface
{
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
    protected $extension = 'cache';

    /**
     * Default constructor method
     *
     */
    public function __construct($app)
    {
        $this->path = $app['config']->get('cache.file.storage_path');;

        if (!is_dir($this->path)) {
            $this->createFolder($this->path);
        }
    }

    /**
     * Set the cache data from given key name with given data
     *
     * @param  string  $key
     * @param  mixed   $value Data for cache
     * @param  integer $time  Cache life time(expire)
     * @return mixed
     */
    public function set($key, $value, $time = 10080)
    {
        list($filename, $filepath) = $this->keyParse($key);

        $path = str_replace($filename.'.'.$this->getExtension(), '', $filepath);

        if (!is_dir($path)) {
            $this->createFolder($path);
        }

        if (file_exists($filepath)) {
            FileSystem::delete($filepath);
        }

        $this->write($path, $filename, $value, $time);
    }

    /**
     * Get the cache data from given key name
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($filename, $filepath) = $this->keyParse($key);

        if (file_exists($filepath)) {
            $data = FileSystem::getContent($filepath);

            if ($this->checkExpire($data)) {
                return $this->unserializer(substr($data, 10));
            } else {
                $this->delete($key);
            }
        }

        return value($default);
    }

    /**
     * Get the cache data from cache or set the callback data.
     *
     * @param  string  $key
     * @param  Closure $callback Callback method for solve cache value if require
     * @param  integer $time     Cache ttl minutes
     * @return mixed
     */
    public function solve($key, Closure $callback, $time = 10080)
    {
        $data = $this->get($key);

        if (! is_null($data) ) {
            return $data;
        }

        // We need to make Solve the Callback and Set the Cache
        $data = $callback();

        $this->set($key, $data, $time);

        return $data;
    }

    /**
     * Check the given cache is has or not
     *
     * @param  string  $key
     * @return boolean
     **/
    public function has($key)
    {
        if ( is_null($this->get($key)) ) {
            return false;
        }

        return true;
    }

    /**
     * Delete the cache file
     *
     * @param  string  $key Cache file key name
     * @return boolean
     **/
    public function delete($key)
    {
        list(, $filepath) = $this->keyParse($key);

        return FileSystem::delete($filepath);
    }

    /**
     * Delete the cache by folder.
     * Notice :: Folder name is case sensative.
     *
     * @param  string  $folder Cache folder name
     * @return boolean
     **/
    public function deleteFolder($folder)
    {
        return Dir::delete($this->path.$folder);
    }

    /**
     * Set the Cache Folder Path
     *
     * @param  string $path
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
     * Set Cache File Extension. No need (dot) sign
     * <code>
     *      Cache::setExtension('jpg');
     * </code>
     *
     * @param  string                    $ext
     * @return \Reborn\Cache\Driver\File
     **/
    public function setExtension($ext)
    {
        $this->extension = $ext;

        return $this;
    }

    /**
     * Get Cache File Extension
     *
     * @return string
     **/
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Parse the given key to filename and full_file_path
     *
     * @param  string $key
     * @return array
     **/
    protected function keyParse($key)
    {
        if (false !== strpos($key, '::')) {
            list($folder, $key) = explode('::', $key);
        }

        $key = md5($key);

        if (isset($folder)) {
            $file = $this->path.$folder.DS.$key.'.'.$this->getExtension();
        } else {
            $file = $this->path.$key.'.'.$this->getExtension();
        }

        return array($key, $file);
    }

    /**
     * Write the cache file in given path
     *
     * @param  string  $path     Cache file saving path
     * @param  string  $filename Cache file name
     * @param  mixed   $value    Cache data value
     * @param  integer $time     Cache expire time with minute
     * @return void
     */
    protected function write($path, $filename, $value, $time)
    {
        $value = $this->serializer($value);

        $expire = time() + ($time * 60); // Change minute to second

        FileSystem::put($path.$filename.'.'.$this->getExtension(), $expire.$value);

        @chmod($path.$filename.'.'.$this->getExtension(), 0777);
    }

    /**
     * Check the given cache is expire or not
     *
     * @param  string  $data Cache data
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
     * @param  array|object $data
     * @return string
     */
    protected function serializer($data)
    {
        return serialize($data);
    }

    /**
     * Unserialize the given data
     *
     * @param  string $data
     * @return array
     */
    protected function unserializer($data)
    {
        return unserialize($data);
    }

    /**
     * Create the folder for cache file
     *
     * @param  string  $path
     * @return boolean
     */
    protected function createFolder($path)
    {
        return (Dir::make($path, 0777, true)) ? true : false;
    }

} // END class File implements CacheDriverInterface
