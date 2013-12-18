<?php

namespace Media\Facade;

use File;

/**
 * Media Facade Class
 *
 * @package Reborn\Module\Media
 * @author MyanmarLinks Professional Web Development Team
 **/
class Media extends \Facade
{

	/**
	 * @var  string  Preview Thumbnail's default width
	 */
	const THUMB_WIDTH = 200;

	/**
	 * undocumented class variable
	 *
	 * @var boolean
	 **/
	protected $used = false;

	protected static $instance;


	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	protected $template;

	/**
	 * undocumented class variable
	 *
	 * @var string
	 **/
	protected $view;

	public function __construct()
	{
		$this->template = static::$app->template;
		$this->view = static::$app->view;
	}

	/**
	 * Upload form
	 *
	 * 
	 *
	 * @return string
	 **/
	protected function uploadForm($name, $formName, $folderId, $fileType)
	{
		if (is_null($fileType)) {
			$fileType = '.jpg,.jpeg,.png,.gif,.bmp,.txt,.rtf,.doc,.docx,.xls,.xlsx,.pdf,.zip,.tar,.rar,.mp3,.wav,.wma';
		}

		$template = File::getContent(__DIR__.DS.'upload.html');

		if (is_null($formName)) {
			$formName = 'media_upload_form';
		}

		return $this->view->renderAsStr(
				$template, 
				compact('name', 'formName', 'buttonName', 'folderId',
					'fileType')
			);
	}

	/**
	 * Get Thumbnail Set Form UI
	 *
	 * @param string $name Field name
	 * @param mixed $value Field value
	 * @param int $width Thumbnail width
	 * @param array $labels Add/Remove Btn label
	 * @return string
	 **/
	protected function thumbnailForm($name, $value = null, $width = null, $labels = array())
	{
		// Now support Backend Only
		if (! defined('ADMIN')) {
			return null;
		}

		if(is_array($width)) {
			$labels = $width;
			$width = null;
		}

		// Set Default Width for Thumbnail Preview
		if(is_null($width)) {
			$width = \Media::THUMB_WIDTH;
		}

		// Set Add Btn Label
		if(!isset($labels['add'])) {
			$labels['add'] = t('media::media.lbl.add_thumb');
		}

		// Set Add Remove Label
		if(!isset($labels['remove'])) {
			$labels['remove'] = t('media::media.lbl.remove_thumb');
		}

		$template = File::getContent(__DIR__.DS.'template.html');

		if (! is_null($value)) {
			$values = explode('/', $value);
			$value = $values[0];
		}

		$data['labels'] = $labels;
		$data['value'] = $value;
		$data['name'] = $name;
		$data['width'] = $width;
		$data['used'] = $this->used;
		
		$this->used = true;

		return $this->view->renderAsStr($template, $data);
	}

	/**
	 * Get Media Facade Instance
	 *
	 * @return Media\Facade\Media
	 **/
	protected static function getInstance()
	{
		if (is_null(static::$instance)) {
			static::$instance = new static;
		}

		return static::$instance;
	}

} // END class Media extends \Facade
