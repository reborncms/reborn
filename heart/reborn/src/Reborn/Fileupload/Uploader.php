<?php

namespace Reborn\Fileupload;

use RbException, Input, Dir, Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Reborn\Fileupload\Exception\DirectoryNotCreatedException;

/**
 * Uploader class for RebornCMS
 *
 * All right reserved. Copyright by RebornCMS Development Team.
 * www.reborncms.com
 *
 * Example Usage:
 * <code>
 *
 * $config = array(
 *           'encName'   => true,
 *           'rename'    => true,
 *           'allowedExt'=> array('jpg', 'jpeg', 'png', 'gif', 'bit')
 *       );
 *
 *   $uploader = Uploader::initialize('files', $config);
 *
 *   $uploaded = $uploader->upload();
 *
 *   // if upload multiple like <input type='files[]' multiple>
 *
 *   foreach ($uploaded as $file) {
 *
 *       if (isset($file['error'])) {
 *           dump($file['error']);
 *       } else {
 *           dump($file);
 *       }
 *
 *   }
 *
 * </code>
 *
 * @package Reborn\Fileupload
 * @author RebornCMS Development Team
 **/
class Uploader
{
    /**
     * File object
     *
     * @var Symfony\Component\HttpFoundation\File\UploadedFile
     **/
    public $files = null;

    /**
     * FileUpload objects
     *
     * @var Reborn\Fileupload\FileUpload
     **/
    public $fileUpload = null;

    /**
     * Variable for setting upload configration
     *
     * @var array
     **/
    protected $config = array(
            'encName'       => false,   // Encrypt file name with md5
            'path'          => UPLOAD,  // Directory the uploaded file save to
            'prefix'        => '',      // Add Prefix to filename
            'maxFileSize'   => 0,       // Maximum uploadable file size in byte, 0 means the maximum uploadable size is the same to php.ini
            'createDir'     => false,   // If set to true directory will be created if there is no specific directory
            'overwrite'     => false,   // If set to true the new uploaded file will overwrite to existing file with the same name
            'rename'        => false,   // If set to true the new uploaded file will be renamed if there is a file with the same name
            'fileChmod'     => 0775,    // Permission for uploaded file
            'dirChmod'      => 0777,    // Permission for directory
            'recursive'     => false,
            'allowedExt'    => array(), // Extensions, want to allow to be uploaded (eg . array('jpg', 'jpeg', 'zip', 'pdf'))
        );

    /**
     * Default file input name
     *
     * @var string
     **/
    protected $key = 'files';

    /**
     * Uploaded file information
     *
     * @var array
     **/
    protected $fileInfo = array();

    /**
     * Instance of Uploader class
     *
     * @var Reborn\Fileupload\Uploader
     **/
    protected static $instance;

    /**
     * Error messages
     *
     * @var array
     **/
    protected $errorMessage = array(
            1 => 'The uploaded file exceeds the upload_max_filesize directive in
                 php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload',
            9 => 'The file with this name has already existed',
            10 => "File size is larger than \$config['maxFileSize']",
            11 => 'This file type is not allowed to be uploaded'
        );

    /**
     * Constructer method for Uploader class
     *
     * @param String $key    The name of file tag
     * @param array  $config Configuration for upload
     *
     * @return Reborn\Fileupload\Uploader
     **/
    public function __construct($key = 'files', Array $config = null)
    {

        if (is_string($key)) {
            $this->key = $key;
        }

        if (is_array($key)) {
            $this->config = array_replace_recursive($this->config, $key);
        }

        if (! is_null($config)) {
            $this->config = array_replace_recursive($this->config, $config);
        }

        $this->files = Input::file($this->key);

        static::$instance = $this;

    }

    /**
     * Initializing files to be uploaded
     *
     * @param String $key    The name of file tag
     * @param array  $config Configuration for upload
     *
     * @return Reborn\Fileupload\Uploader
     **/
    public static function initialize($key = 'files', Array $config = null)
    {

        $ins = (is_null(static::$instance)) ? new static($key, $config)
                : static::$instance;

        if (is_array($ins->files)) {
            foreach ($ins->files as $file) {
                if (! is_null($file)) {
                    $ins->fileUpload[] = new FileUpload($file, $ins->config);   
                }
            }
        } else {
            $ins->fileUpload = new FileUpload($ins->files, $ins->config);
        }

        return $ins;
    }

    /**
     * Set configuration for file upload
     *
     * @param array $config Configuration array
     *
     * @return void
     **/
    public function setConfig($config)
    {

        if (! is_null($this->fileUpload)) {

            if (is_array($this->fileUpload)) {

                foreach ($this->fileUpload as $fileUpload) {
                    $fileUpload->setConfig($config);
                }

            } else {
                $this->fileUpload->setConfig($config);
            }

        }

        $this->config = array_replace_recursive($this->config, $config);

    }

    /**
     * This method will upload files
     *
     * @param String $key    The name of file tag
     * @param array  $config Configuration for upload
     *
     * @return mixed $uploadedFiles will return null or uploaded files' data.
     **/
    public static function upload($key = 'files', Array $config = null)
    {
        $ins = (is_null(static::$instance)) ? static::initialize($key, $config)
                    : static::$instance;

        Event::call('reborn.uploader.upload', array($ins));

        if ($ins->config['maxFileSize'] > $ins->maxUploadableFileSize()) {
            throw new RbException('Maximum uploadable file size is larger than maximum uploadable file size which is defined in php.ini!');
        }

        if (is_array($ins->fileUpload)) {
            foreach ($ins->fileUpload as $uploaded) {

                $ins->fileInfo[] = $ins->uploadProcess($uploaded);

            }
        } else {
            $ins->fileInfo = $ins->uploadProcess($ins->fileUpload);
        }

        return $ins->fileInfo;
    }

    /**
     * This method will return the maximum uploadable file size which is defined
     * in php.ini.
     *
     * @return String
     **/
    public function maxUploadableFileSize()
    {
        return \Symfony\Component\HttpFoundation\File\UploadedFile::getMaxFileSize();
    }

    /**
     * Set Custom error message
     *
     * @param mix    $key   Error key or array with error key and message pair
     * @param String $value Message for specific error
     *
     * @return void
     **/
    public function setErrorMessage($key, $value = null)
    {

        if (is_array($key)) {
            $this->errorMessage = array_replace_recursive($this->errorMessage, $key);
        }

        if (is_int((int) $key)) {
            $this->errorMessage[(int) $key] = $value;
        }

    }

    /**
     * Can be called method start with get
     *
     * @param string $name
     * @param array  $args
     *
     * @return mix
     **/
    public function __call($name, $args)
    {

        switch ($name) {
            case 'getConfig':
                return $this->config;
                break;

            case 'getFileInfo':
                return $this->fileInfo;
                break;

            case 'getKey':
                return $this->key;
                break;

            case 'getFileUpload':
                return $this->fileUpload;
                break;

            default:
                return false;
                break;
        }

    }

    /**
     * Real upload files
     *
     * @param Reborn\Fielupload\FileUpload $fileObject
     *
     * @return array
     **/
    protected function uploadProcess(FileUpload $fileObject)
    {

        $error = array();
        $result;

        try {
            $fileObject->initialize();

            if ($fileObject->isFail()) {
                $error = $fileObject->getError();
            } else {
                $fileObject->upload();
            }
        } catch (DirectoryNotCreatedException $e) {
            $error[] = 12;
        }

        $result = $fileObject->getFileInfo();

        foreach ($error as $err) {
            $result['error'][] = $this->errorMessage[$err];
        }

        return $result;

    }

} // END class Uploader
