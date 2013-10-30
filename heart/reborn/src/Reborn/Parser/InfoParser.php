<?php

namespace Reborn\Parser;

use Reborn\Filesystem\File;
use Config, Cache;

/**
 * Info File Parser Class
 *
 * @package Reborn
 * @author MyanmarLinks Professional Web Development Team
 **/
class InfoParser
{

	/**
	 * Info file variable
	 *
	 * @var string
	 **/
	protected $file;

	/**
	 * Compile cache file storage path
	 *
	 * @var string
	 **/
	protected $cache_path;

	/**
	 * Default instance method
	 *
	 * @param string $file Info file with full path
	 * @return void
	 **/
	public function __construct($file = null)
	{
		if (!is_null($file)) {
			$this->file = $file;
		}

		$this->cache_path = Config::get('cache.file.storage_path');
	}

	/**
     * Parse Info File (name.info) to array data
     *
     * @param string $file name.info file path
     * @return array
     **/
	public function parse($file = null)
	{
		if (!is_null($file)) {
			$this->file = $file;
		}

		if ($cache = $this->getFromCache($file)) {
			return $cache;
		}

		return $this->makeParsing($this->file);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author
	 **/
	protected function makeParsing($file)
	{
		$data = File::getContent($file);

        $lines = explode("\n", $data);

        $info = array();

        foreach ($lines as $line) {
            if ($line == '') {
                continue;
            } else {
                if (false !== strpos($line, '=')) {

                    list($key, $value) = explode('=', $line, 2);

                    $key = $this->clearString($key);

                    $info[$key] = $this->getValue($value);
                }
            }
        }

        // Make Cache File
        $this->makeCacheFile($info);

        return $info;
	}

    /**
     * Get Value with format
     *
     * @param string $value
     * @return mixed
     **/
    protected function getValue($value)
    {
    	$value = $this->clearString($value);

    	// Check array or not
        if ('[' == substr($value, 0, 1)) {
            return $this->parseArrayString($value);
        }

    	switch ($value) {
    		case 'true':
    			return true;
    			break;

    		case 'false':
    			return false;
    			break;

    		case 'null':
    			return null;
    			break;

    		default:
    			return $value;
    			break;
    	}
    }

    /**
     * Paser strin data to array data
     * example
     * <code>
     *      [key1 : value, key2 : value2, key3 : value3]
     *      // to
     *      array('key1' => 'value', 'key2' => 'value2', 'key3' => 'value3')
     * </code>
     *
     * @param string $string
     * @return array
     **/
    protected function parseArrayString($string)
    {
        preg_match('#\[(.*)\]#', $string, $matches);

        $lists = explode(',', $matches[1]);

        $values = array();

        if ('' === $matches[1]) {
        	return $values;
        }

        foreach ($lists as $list) {
            $list = $this->clearString($list);
            if (false !== strpos($list, ':')) {
                list($k, $v) = explode(':', $list);

                $k = $this->clearString($k);

                $values[$k] = $this->getValue($v);
            } else {
                $values[] = $this->getValue($list);
            }
        }

        return $values;
    }

    /**
     * Clean the given string
     *
     * @param string $value
     * @return string
     **/
    protected function clearString($value)
    {
    	$value = trim($value, ' ');
    	$value = str_replace(array("\r\n", "\r"), "\n", $value);
    	$value = trim($value, "\n");

    	return $value;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    protected function getFromCache($file)
    {
    	$time = filemtime($file);

    	if(! Cache::has($file) ) return false;

    	dump(Cache::get($file), true);

    	$cachefile = $this->cache_path.md5($file).'.cache';

    	if ($time > filemtime($cachefile)) {
    		$this->deleteCache($file);

    		return false;
    	}

    	return Cache::get($file);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    protected function makeCacheFile($info)
    {
    	Cache::set($this->file, $info);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function deleteCache($file)
    {
    	Cache::delete($file);
    }

} // END class InfoParser
