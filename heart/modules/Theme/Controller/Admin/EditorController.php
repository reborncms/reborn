<?php

namespace Theme\Controller\Admin;

class EditorController extends \AdminController
{
	public function before() {
		$this->template->style('theme.css','theme');
	}

	public function index()
	{	
		$themePath = THEMES.\Setting::get('public_theme');

		$files = self::listFiles($themePath);		

		foreach ($files as $f) {
			if (basename($f) == 'style.css') {
			    $currentFile = $f;
			}
			$editableFiles[] = pathinfo($f);
		}

		$themeFile = self::getThemeFile($editableFiles);

		$content = htmlentities(file_get_contents($currentFile));

		$this->template->title(\Translate::get('theme::editor.title'))
					->breadcrumb(\Translate::get('theme::editor.title'))
					->setPartial('admin/editor/index')
					->set('currentFile', $currentFile)
					->set('content', $content)
					->set('files', $themeFile);
	}

	public function edit($ext = null, $file = null)
	{
		if ($ext == null or $file == null) return \Redirect::to(adminUrl('theme/editor'));

		if (\Input::isPost()) {

		}

		$themePath = THEMES.\Setting::get('public_theme');
		$files = self::listFiles($themePath);		
		$currentFile = $file.'.'.$ext;

		foreach ($files as $f) {
			if ( $currentFile == basename($f)) {
			    $currentFile = $f;
			}
			$editableFiles[] = pathinfo($f);
		}

		if (!file_exists($currentFile)) {
			\Flash::error('There is no such file in your current theme folder.');
			return \Redirect::to(adminUrl('theme/editor'));	
		}

		$themeFile = self::getThemeFile($editableFiles);
		$content = htmlentities(file_get_contents($currentFile));

		$this->template->title(\Translate::get('theme::editor.title'))
					->breadcrumb(\Translate::get('theme::editor.title'))
					->setPartial('admin/editor/index')
					->set('currentFile', $currentFile)
					->set('content', $content)
					->set('files', $themeFile);
	}

	/**
	* Get all css, js and template files from current theme
	*
	* @param $editableFiles array
	* @return object
	*/
	protected function getThemeFile($editableFiles)
	{
		foreach ($editableFiles as $eF) {
			if ($eF['extension'] == "css") {
				$themeFile->css[] = $eF;
			} elseif ($eF['extension'] == "js") {
				$themeFile->js[] = $eF;
			} else {
				$themeFile->template[] = $eF;
			}
		}

		return $themeFile;
	}

	/**
	* List all .css and .php files in active theme directory
	*
	* @param $themePath string
	* @return array
	*/
	protected function listFiles($themePath)
	{
		$di = new \RecursiveDirectoryIterator($themePath);

		foreach (new \RecursiveIteratorIterator($di) as $filename => $file) {

			$fileInfo = pathinfo($filename);

			if ($fileInfo['extension'] == "css" or $fileInfo['extension'] == "html" or $fileInfo['extension'] == "js") {
				$editableFiles[] = $filename;
			}
		}

		return $editableFiles;
	}

}