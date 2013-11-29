<?php

namespace Reborn\Config;

use Reborn\Filesystem\File;

/**
 * Config Writer class for the Reborn
 *
 * @package Reborn\Config
 * @author Myanmar Links Professional Web Development Team
 **/
class Writer
{
	/**
	 * Config values variable
	 *
	 * @var array
	 **/
	protected $configs = array();

	/**
	 * Default instance method for Writer
	 *
	 * @param array|null $configs
	 * @return void
	 **/
	public function __construct(array $configs = null)
	{
		if (!is_null($configs)) {
			$this->configs = $configs;
		}
	}

	/**
	 * Set configs value.
	 *
	 * @param array $configs
	 * @param boolean $merge
	 * @return \Reborn\Config\Writer
	 **/
	public function set(array $configs, $merge = true)
	{
		if ($merge) {
			$this->configs = array_merge_recursive($this->configs, $configs);
		} else {
			$this->configs = $configs;
		}

		return $this;
	}

	/**
	 * Write the config to file.
	 *
	 * @param string $file Config file name with fullpath
	 * @return boolean
	 **/
	public function write($file)
	{
		$content = '<?php'.PHP_EOL.PHP_EOL.'return ';

		$content .= str_replace(array('  ', 'array ('), array("\t", 'array('), var_export($this->configs, true)).";\n";

		if(@File::put($file, $content)) {
			return true;
		}

		// Doesn't write. May be permission problem.
		return false;
	}

} // END class Writer
