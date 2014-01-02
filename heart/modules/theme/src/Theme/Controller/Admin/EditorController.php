<?php

namespace Theme\Controller\Admin;

class EditorController extends \AdminController
{
	/**
	 * Before function for Theme Editor
	 *
	 * @return void
	 **/
	public function before() 
	{
		$this->menu->activeParent('appearance');
		$this->template->style('theme.css','theme');
		$this->template->style(array(
	                    'plugins/codemirror/codemirror.css'
	                ))
	                ->script(array(
	                    'plugins/codemirror/codemirror.js',
	                    'plugins/codemirror/css.js',
	                    'plugins/codemirror/javascript.js'
	                ));
	}

	/**
	 * Show all editable files with associated file types
	 * Default file is default.html
	 *
	 * @return void
	 **/
	public function index()
	{	
		if (!user_has_access('theme.editor')) return $this->notFound();

		$handler = \Facade::getApplication()->theme;
		$themePath = $handler->findTheme(\Setting::get('public_theme'));

		$files = self::listFiles($themePath);		

		foreach ($files as $f) {
			if (basename($f) == 'default.html') {
			    $currentFile = $f;
			}
			$editableFiles[] = pathinfo($f);
		}

		$themeFile = $this->getThemeFile($editableFiles);
		$content = htmlentities(file_get_contents($currentFile));
		$currentFile = pathinfo($currentFile);

		$this->template->title(t('theme::editor.title'))
					->breadcrumb(t('theme::editor.title'))
					->setPartial('admin/editor/index')
					->set('currentFile', $currentFile)
					->set('content', $content)
					->set('files', $themeFile);
	}

	/**
	 * Get a file with filename and extension
	 * Edit html, css and js files
	 *
	 * @param string $ext
	 * @param string $file
	 * @return void
	 **/
	public function edit($ext = null, $file = null)
	{
		if (!user_has_access('theme.editor')) return $this->notFound();
		if ($ext == null or $file == null) return \Redirect::to(adminUrl('theme/editor'));

		$handler = \Facade::getApplication()->theme;
		$themePath = $handler->findTheme(\Setting::get('public_theme'));		
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
			return \Redirect::toAdmin('theme/editor');	
		}

		if (\Input::isPost()) {
			if(is_writable($currentFile)) {
				$text = stripslashes(\Input::get('content'));			
				$fileOpen = fopen($currentFile,"w");
				if (fputs($fileOpen, html_entity_decode($text, ENT_QUOTES))) {
					fclose($fileOpen);
					\Flash::success(t('theme::editor.success'));
				}
				else{
					\Flash::error(t('theme::editor.error'));
				}
			} else {
				\Flash::error(t('theme::editor.permission'));
			}
		}

		$themeFile = self::getThemeFile($editableFiles);
		$content = file_get_contents($currentFile);
		$currentFile = pathinfo($currentFile);

		$this->template->title(t('theme::editor.title'))
					->breadcrumb(t('theme::editor.title'))
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