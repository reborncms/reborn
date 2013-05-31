<?php

namespace Theme\Controller\Admin;
Use \Theme\Model\Theme as Theme;

class ThemeController extends \AdminController
{
	public function before() {
		$this->template->style('theme.css','theme');
	}

	public function index()
	{
		$themes = Theme::all();
		$active = \Setting::get('public_theme');

		$this->template->title(\Translate::get('theme::theme.title'))
					->breadcrumb(\Translate::get('theme::theme.title'))
					->set('themes', $themes)
					->set('active', $active)
					->setPartial('admin/index');
	}

	public function activate($name)
	{
		$themes = ThemeModel::all();

		if (array_key_exists($name, $themes)) {
			\Setting::set('public_theme', $name);
			\Flash::success(sprintf(\Translate::get('theme::theme.activate.success'), $themes[$name]['name']));
		} else {
			\Flash::error(\Translate::get('theme::theme.activate.error'));
		}
		return \Redirect::to(ADMIN_URL.'/theme');
	}

	public function delete($name)
	{
		$themes = Theme::all();

		if (array_key_exists($name,$themes)) {
			if (is_dir(THEMES.$name)) {
				$delete = \Dir::delete(THEMES.$name);

				if ($delete) {
					\Flash::success(\Translate::get('theme::theme.delete.success'));
				} else {
					\Flash::error(\Translate::get('theme::theme.delete.error'));
				}
			} else {
				\Flash::error(\Translate::get('theme::theme.delete.error'));
			}
		} else {
			\Flash::error(\Translate::get('theme::theme.delete.error'));
		}
		return \Redirect::to(ADMIN_URL.'/theme');
	}

	public function upload()
	{
		\Flash::error("Theme upload is still on progress!");
		return \Redirect::to(ADMIN_URL.'/theme');

		// Still need to write unzip for theme upload, dun use it before it's ok
		if (\Input::isPost()) 
		{
			$config = array(
				'savePath' => CONTENT."uploads".DS."tmp".DS,
			    'allowedExt' => array('zip')
			);

			\Uploader::initialize('files', $config);

			// if there are any valid files
			if (\Uploader::isSuccess()) {

			    // save them according to the config
			    \Uploader::upload('files');

			    $zip = \Upload::get_files();
			    $zip_path = $zip[0]['saved_to'].$zip[0]['saved_as'];

			    $unzip = new \Unzip();
		    	$unzip->allow(array('xml', 'html', 'css', 'js', 'png', 'gif', 'jpeg', 'jpg', 'swf', 'ico', 'php'));

		    	$unzip->extract($zip_path, CONTENTPATH.'themes/', true)
					? \Session::set_flash('success', \Lang::get('theme.upload_success'))
					: \Session::set_flash('error', $unzip->error_string());
				
				@unlink($zip_path);
				\Response::redirect(ADMIN.'/themes');
			}

			foreach (\Upload::get_errors() as $file)
			{
			    \Session::set_flash('error', $file['errors']);
				\Response::redirect(ADMIN.'/themes');
			}

			\Session::set_flash('error', \Lang::get('theme.upload_error'));
			\Response::redirect(ADMIN.'/themes');
		}

		$this->template->title(\Translate::get('theme::theme.title'))
					->breadcrumb(\Translate::get('theme::theme.title'))
					->setPartial('admin/upload');
	}
}