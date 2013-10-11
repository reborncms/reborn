<?php 

namespace Reborn\Fileupload;

use Reborn\Exception\RbException as RbException;

/**
 * Uploader class for RebornCMS
 *
 * All right reserved. Copyright by RebornCMS Development Team.
 * www.reborncms.com
 *
 * @package Reborn\Fileupload
 * @author RebornCMS Development Team
 **/
class Uploader
{
	private static $files = null;

	/**
     * Checking file object
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    protected static function checkFileObject($fileObj)
    {
        if (! is_object($fileObj) and ! is_array($fileObj)) {
            throw new RbException("No files to upload! Check 'enctyp' of your
                form tag.");

            exit;
        }
    }

    /**
     * File Upload method for RebornCMS
     *
     * @param String $key Name of input field. The default is 'files'.
     *
     * @return mixed $filObject Return file object or file object array
     **/
	public static function fileUpload($key = 'files') 
	{
		$files = \Input::file($key);

		static::checkFileObject($files);

		if (is_array($files)) {
			$fileObj = array();

			for ($i=0; $i < count($files); $i++) { 
				$fileObj[$i] = new FileUpload($files[$i]);
			}
		} else {
			$fileObj = new FileUpload($files);
		}

		return $fileObj;
	}

	/**
	 * Initialize or setup method to upload files.
	 *
	 * @param String $key Name of file input.
	 * @param array $config Customize configuration for files
	 *
	 * @return mixed $initedFiles will return error array or null
	 **/
	public static function uploadInit ($key = 'files', $config = array())
	{
		$files = static::fileUpload($key);

		static::$files = $files;

		$initedFiles = null;

		if (is_array($files)) {
			$i = 0;

			foreach ($files as $file) {
				$file->setConfig($config);
				$file->uploadInit();

				$errors = $file->getErrors();

				if (! empty($errors)) {
					$fileInfo = $file->getFileInfo();

					$initedFiles[$i]['name'] = $fileInfo['originName'];
					$initedFiles[$i]['errors'] = $errors;

					$i++;
				}
			}
		} else {
			$files->setConfig($config);
			$files->uploadInit();

			$errors = $files->getErrors();

			if (! empty($errors)) {
				$fileInfo = $files->getfileInfo();

				$initedFiles['name'] = $fileInfo['originName'];
				$initedFiles['errors'] = $errors;
			}
		}

		return $initedFiles;
	}

	/**
	 * This method will upload files
	 *
	 * @return mixed $uploadedFiles will return null or uploaded files' data. 
	 **/
	public static function upload ()
	{
		$uploadedFiles = null;

		if (is_array(static::$files)) {
			for ($i=0; $i < count(static::$files); $i++) { 
				if (static::$files[$i]->upload()) {
					$uploadedFiles[$i] = static::$files[$i]->getFileInfo();
				}
			}
		} else {
			if (static::$files->upload()) {
				$uploadedFiles = static::$files->getFileInfo();
			}
		}

		return $uploadedFiles;
	}

	/**
     * This method will return the maximum uploadable file size which is defined in php.ini.
     *
     * @return String
     **/
    public static function maxUploadableFileSize ()
    {
        return \Symfony\Component\HttpFoundation\File\UploadedFile::getMaxFileSize();
    }

} // END class Uploader
