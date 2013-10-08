<?php

namespace Maintenance\Controller\Admin;

use Reborn\Filesystem;

use Maintenance\Lib\CheckFiles;

class MaintenanceController extends \AdminController
{
	public function before() {

		$this->menu->activeParent('utilities');

		$this->template->style('maintenance.css','maintenance');

	}

	public function index() 
	{
		$skip = array('timezones', 'logs');
		$has_child = array('cache');

		$dir = new CheckFiles(STORAGES, $skip, $has_child);
		$dir_list = $dir->dirList();
		$this->template->title("Maintenance")
						->setPartial('index')
						->set('dir_list', $dir_list);
	}

	public function clear($folder_name, $child = null) 
	{
		if ($child !== null) {
			$dir = STORAGES.$folder_name.DS.$child;	
		} else {
			$dir = STORAGES.$folder_name;
		}
		$fold_msg = str_replace(STORAGES, '', $dir);
		if (is_dir($dir)) {
			$deleted = array_map('unlink', glob($dir.DS.'*'));
			if ($folder_name == 'less') {
				$clear_css = array_map('unlink', glob(BASE.DS.'assets'.DS.'*.css'));
			}
			if ($deleted) {
				\Flash::success($fold_msg." is successfully deleted.");
			} else {
				\Flash::error($fold_msg." connot be deleted.");
			}
			return \Redirect::to(adminUrl('maintenance'));
		}
	}

	public function after($response)
	{
		return parent::after($response);
	}
}
