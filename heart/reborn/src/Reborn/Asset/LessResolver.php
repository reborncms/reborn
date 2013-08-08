<?php

namespace Reborn\Asset;

use lessc;
use Reborn\Cores\Registry;
use Reborn\Filesystem\Directory as Dir;

/**
 * LESS Css resolver class for the Reborn
 *
 * @package Reborn\Asset
 * @author Myanmar Links Professional Web Development Team
 **/
class LessResolver
{

	/**
	 * Less file path variable
	 *
	 * @var string
	 **/
	protected $filepath;

	/**
	 * Site Url variable
	 *
	 * @var string
	 **/
	protected $url;

	/**
	 * Create LessResolver Instance
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->url = Registry::get('app')->request->baseUrl();
	}

	/**
	 * Solve the Less to CSS
	 *
	 * @param string $file File with fullpath
	 * @return string
	 **/
	public function solve($file)
	{
		$this->filepath = pathinfo($file, PATHINFO_DIRNAME);
		return $this->cacheOrCompile($file);
	}

	/**
	 * Check Less is alredy cached or need to compile
	 * and get css file url
	 *
	 * @param string $file File with fullpath
	 * @return string
	 **/
	protected function cacheOrCompile($file)
	{
		// load the cache
		$cacheFile = $this->getCacheFile($file);

		// output File
		$outputFile = $this->getCacheFile($file, 'css');

		// Return CSS Url
		$cssUrl = str_replace(array(BASE, '\\'), array($this->url, '/'), $outputFile);

		if (file_exists($cacheFile)) {
			$cache = unserialize(file_get_contents($cacheFile));
		} else {
			$cache = $file;
		}

		$less = new lessc;
		$newCache = $less->cachedCompile($cache);

		if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
			file_put_contents($cacheFile, serialize($newCache));
			$content = $this->fixedUrl($newCache['compiled'], $file);
			file_put_contents($outputFile, $content);
		}

		return $cssUrl;
	}

	/**
	 * Fixed CSS's url() path for cache file
	 *
	 * @param string $contents CSS Contents
	 * @return string
	 */
	public function fixedUrl($contents)
	{
		$pathArray = explode(DS,$this->filepath);
		$pathCount = count($pathArray);

		$pattern = '/url\\(\\s*([^\\)]+?)\\s*\\)/x';

		preg_match_all($pattern, $contents, $m);

		$replace = array();
		foreach ($m[1] as $k => $path) {

			if ( (false === strpos($path, '//')) && (false === strpos($path, 'data:')) ) {
				$path = ltrim($path, '/');
				$uri = str_replace('/./', '/', $path);

				if (preg_match_all('/(\.\.\/)/', $uri, $matches)) {
					$dotLevels = count($matches[1]);
					$length = $pathCount - $dotLevels;
					$newpath = array_slice(explode(DS,$this->filepath), 0, $length);
					$newpath = implode(DS, $newpath);
				} else {
					$newpath = $this->filepath;
				}

				$url = str_replace(BASE, $this->url, $newpath);
				$clearurl = str_replace(array('../','\'', '"'), '', $uri);
				$clearurl = '"'.$url.'/'.$clearurl.'"';
				$replace[$k] = str_replace('\\', '/', $clearurl);
			} else {
				$replace[$k] = $path;
			}
		}

		$result = str_replace($m[1], $replace, $contents);

		$compressor = new MiniCompressor();

		return $compressor->make($result);
	}

	/**
	 * Get Less Cache File
	 *
	 * @param string $file File name
	 * @param string $type Cache File Type (css|cache)
	 * @return string
	 **/
	protected function getCacheFile($file, $type = 'cache')
	{
		if ($type == 'css') {
			$cachepath = BASE.'assets';
		} else {
			$cachepath = STORAGES.'less';
		}

		if (! Dir::is($cachepath)) {
			Dir::make($cachepath);
		}

		return $cachepath.DS.md5($file).'.'.$type;
	}

} // END class LessResolver
