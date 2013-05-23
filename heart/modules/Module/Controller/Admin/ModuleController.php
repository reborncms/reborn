<?php

namespace Module\Controller\Admin;

use Reborn\Util\Uploader as Upload;

/**
 * Module Manager Admin Controller
 *
 * @package Reborn\Modules
 * @author Nyan Lynn Htut
 **/

class ModuleController extends \AdminController
{
	public function before()
	{
		$this->menu->activeParent('utilities');

		$this->modules = $this->moduleSpliter();

		$this->template->style('modules.css', 'module');
	}

	public function index()
	{
		$this->template->title(t('module::module.title'))
					->set('system', $this->modules['system'])
					->set('plugged', $this->modules['plugged'])
					->set('unplug', $this->modules['unplug'])
					->set('news', $this->modules['news']);

		$drag = $this->template->partialRender('module::plugged');
		$news = $this->template->partialRender('module::news');
		$system = $this->template->partialRender('module::system');

		$this->template->set('drag_view', $drag);
		$this->template->set('news_view', $news);
		$this->template->set('system_view', $system);

		$this->template->setPartial('index');
	}

	public function install($name, $uri)
	{
		if (! $this->checkAction($name, $uri) ) {
			return $this->notFound();
		}

		if (\Module::install($name, $uri, 1)) {
			$msg = sprintf(t('module::module.install_success'), $name);
			\Flash::success($msg);
		} else {
			$msg = sprintf(t('module::module.install_error'), $name);
			\Flash::error($msg);
		}

		return \Redirect::toAdmin('module');
	}

	public function uninstall($name, $uri)
	{
		if (! $this->checkAction($name, $uri) ) {
			return $this->notFound();
		}

		if (\Module::uninstall($name, $uri)) {
			$msg = sprintf(t('module::module.uninstall_success'), $name);
			\Flash::success($msg);
		} else {
			$msg = sprintf(t('module::module.uninstall_error'), $name);
			\Flash::error($msg);
		}

		return \Redirect::toAdmin('module');
	}

	public function enable($name, $uri)
	{
		if (! $this->checkAction($name, $uri) ) {
			return $this->notFound();
		}

		if (\Module::enable($name, $uri)) {
			$msg = sprintf(t('module::module.enable_success'), $name);
			\Flash::success($msg);
		} else {
			$msg = sprintf(t('module::module.enable_error'), $name);
			\Flash::error($msg);
		}
		return \Redirect::toAdmin('module');
	}

	public function disable($name, $uri)
	{
		if (! $this->checkAction($name, $uri) ) {
			return $this->notFound();
		}

		if (\Module::disable($name, $uri)) {
			$msg = sprintf(t('module::module.disable_success'), $name);
			\Flash::success($msg);
		} else {
			$msg = sprintf(t('module::module.disable_error'), $name);
			\Flash::error($msg);
		}
		return \Redirect::toAdmin('module');
	}

	public function delete($name, $uri)
	{
		if (! $this->checkAction($name, $uri) ) {
			return $this->notFound();
		}

		if (\Module::has($name)) {
			$mod = \Module::getData($name);

			if ($mod['installed']) {
				\Module::uninstall($name, $uri);
			}

			if (\Dir::is($mod['path'])) {
				if (\Dir::delete($mod['path'])) {
					$msg = sprintf(t('module::module.delete_success'), $name);
					\Flash::success($msg);
				} else {
					$msg = sprintf(t('module::module.manually_remove'), $name);
					\Flash::error($msg);
				}
			}
		}

		return \Redirect::toAdmin('module');
	}

	public function upload()
	{

		$tmp_path = STORAGES.'tmp'.DS;

		$extract_path = MODULES;

		if (\Input::isPost()) {
			$config = array(
				'savePath' => $tmp_path,
				'createDir' => true,
				'allowedExt' => array('zip')
			);

			if (\Security::CSRFvalid()) {
				$v = \Validation::create(array('file' => \Input::file('fileselect')),
										array('file' => 'required'));

				if ($v->valid()) {
					// Start the Upload File
					Upload::initialize('fileselect', $config);

					if (Upload::isSuccess()) {
						$data = Upload::upload('fileselect');
						$data[0]['status'] = 'success';
					} else {
						$v = Upload::errors();
						$data[0]['status'] = 'fail';
						$error = '';
						foreach ($v[0] as $k => $s) {
							if (is_int($k)) {
								$error .= $s.' ';
							}
						}
						$data[0]['errors'] = $error;

						\Flash::error($error);

						return \Redirect::toAdmin('module/upload');
					}

					try {
						$zip_file = $tmp_path.$data[0]['savedName'];

						$filename = str_replace('.zip', '', $data[0]['savedName']);

						// create object
						$zip = new \ZipArchive() ;

						// open archive
						if ($zip->open($zip_file) !== TRUE) {
							\Flash::error(sprintf(t('module::module.unzip_error'),$filename));
							\File::delete($zip_file);
							return \Redirect::toAdmin('module/upload');
						}

						// extract contents to destination directory
						$zip->extractTo($extract_path);

						// close archive
						$zip->close();

						\File::delete($zip_file);
						\Flash::success(sprintf(t('module::module.upload_success'),$filename));

						return \Redirect::toAdmin('module/upload');
					} catch (\Exception $e) {
						\Flash::error($e);

						return \Redirect::toAdmin('module/upload');
					}
				} else {
					\Flash::error(implode("\n\r", $v->getErrors()));
				}
			} else {
				\Flash::error(t('module::module.csrf_error'));
			}
		}

		$this->template->title(t('module::module.upload_title'))
						->setPartial('upload');
	}

	protected function checkAction($name, $uri)
	{
		$systems = \Config::get('app.module.system');

		if (in_array($name, $systems)) {
			return false;
		}

		return true;
	}

	protected function moduleSpliter()
	{
		$m = array();
		$m['system'] = $m['plugged'] = $m['unplug'] = $m['news'] = array();
		$sys = \Config::get('app.module.system');
		$mods = \Module::getAll();

		foreach ($mods as $k => $mod) {
			if ($mod['installed']) {
				if (in_array($k, $sys)) {
					$m['system'][$k] = $mod;
				} else {
					$m['plugged'][$k] = $mod;
				}
			} else {
				$m['news'][$k] = $mod;
			}
		}

		return $m;
	}
}
