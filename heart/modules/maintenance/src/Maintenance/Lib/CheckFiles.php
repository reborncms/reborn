<?php 

namespace Maintenance\Lib;

class CheckFiles
{
	public $dir;

	protected $skip;

	protected $child;

	public $dir_list;

	public function __construct($dir_path, $skip, $child)
	{
		$this->dir = $dir_path;

		$this->skip = $skip;

		$this->child = $child;
	}

	public function dirList()
	{
		$dirs = new \DirectoryIterator(STORAGES);
		$l=0;
		$this->dir_list = array();
		foreach ($dirs as $dir) {
			if (! $dir->isDot() and $dir->isDir()) {
				if (! in_array($dir->getFilename(), $this->skip)) {
					$cache_file_count = count(glob($dir->getPathname().DS.'*.*'));
					if ($cache_file_count > 0) {
						$this->dir_list[$l]['name'] = $dir->getFilename();
						$this->dir_list[$l]['count'] = $cache_file_count;
					}
					if (in_array($dir->getFilename(), $this->child)) {
						$this->dir_list[$l]['name'] = $dir->getFilename();
						$sub_dir = new \DirectoryIterator(STORAGES.$dir->getFilename());
						$c = 0;
						$child_count = 0;
						foreach ($sub_dir as $child) {
							if (! $child->isDot() and $child->isDir()) {
								$child_cache_count = count(glob($child->getPathname().DS.'*.*'));
								$child_count += $child_cache_count;
								if ($child_cache_count > 0) {
									$this->dir_list[$l]['child'][$c]['name'] = $child->getFilename();
									$this->dir_list[$l]['child'][$c]['count'] = $child_cache_count;
									$c++;
								}
							}
						}
						$this->dir_list[$l]['count'] = $child_count;
						if ($child_count == 0) {
							unset($this->dir_list[$l]);
						}
					}
					$l++;
				}
			}		
		}

		return $this->dir_list;

	}

}