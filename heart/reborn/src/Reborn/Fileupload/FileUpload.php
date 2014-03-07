<?php

namespace Reborn\Fileupload;

use Dir, Event, RbException, File;
use \Symfony\Component\HttpFoundation\File\UploadedFile;
use Reborn\Fileupload\Exception\DirectoryNotFoundException;
use Reborn\Fileupload\Exception\DirectoryNotCreatedException;
use Reborn\Fileupload\Exception\DirectoryNotWritabledException;
use Reborn\Fileupload\Exception\LargeFileSizeException;

class FileUpload
{

    /**
     * Upload configuration
     *
     * @var array
     **/
    protected $config = array(
            'encName'       => false,   // Encrypt file name with md5
            'path'          => UPLOAD,  // Directory the uploaded file save to
            'prefix'        => '',
            'maxFileSize'   => 0,
            'createDir'     => false,
            'overwrite'     => false,
            'rename'        => false,
            'fileChmod'     => 0775,
            'dirChmod'      => 0777,
            'recursive'     => false,
            'allowedExt'    => array(),
        );

    /**
     * File is uploaded or not
     *
     * @var boolean
     **/
    protected $fail = false;

    /**
     * File object
     *
     * @var Object $file
     **/
    protected $file = null;

    /**
     * File information
     *
     * @var array $fileInfo
     **/
    protected $fileInfo = array();

    /**
     *  Uploaded file errors
     *
     *  Error codes
     *
     * 0 - UPLOAD_ERR_OK
     * 1 - UPLOAD_ERR_INI_SIZE
     * 2 - UPLOAD_ERR_FORM_SIZE
     * 3 - UPLOAD_ERR_PARTIAL
     * 4 - UPLOAD_ERR_NO_FILE
     * 6 - UPLOAD_ERR_NO_TMP_DIR
     * 7 - UPLOAD_ERR_CANT_WRITE
     * 8 - UPLOAD_ERR_EXTENSION
     * 9 - File duplication
     * 10 - File size is larger than $config['maxFileSize']
     * 11 - File type is not allowed to be uploaded
     *
     *  @var array
     */
    protected $error;

    /**
     * Instance of FileUpload Class
     *
     * @var Reborn\Fileupload\FileUpload
     **/
    protected static $instance;

    /**
     * Constructer method fo FileUpload calss
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public function __construct($file, Array $config = null)
    {
        $this->file = $file;

        $this->setConfig($config);

        $this->fileInfo = array(
            'extension'     => strtolower($file->getClientOriginalExtension()),
            'originName'    => $file->getClientOriginalName(),
            'originBaseName'=> basename($file->getClientOriginalName(),
                    '.' . $file->getClientOriginalExtension()),
            'savedName'     => '',
            'savedBaseName' => '',
            'fileSize'      => $file->getClientSize(),
            'mimeType'      => $file->getClientMimeType(),
            'width'         => 0,
            'height'        => 0,
            );

        static::$instance = $this;

        Event::call('reborn.fileupload.start', array($this));
    }

    /**
     * Initializing upload config and files
     *
     * @return
     **/
    public static function initialize($file = null, Array $config = null)
    {

        $ins = (is_null(static::$instance)) ? new static($file, $config)
                     : static::$instance;

        Event::call('reborn.fileupload.initialize', array($ins));

        if (! $ins->createDir()) {
            throw new DirectoryNotCreatedException();

            exit;
        }

        if (! Dir::is($ins->config['path'])) {
            throw new DirectoryNotFoundException();

            exit;
        }

        if (! is_writable($ins->config['path'])) {
            throw new DirectoryNotWritabledException();

            exit;
        }

        if ($ins->config['maxFileSize'] > UploadedFile::getMaxFilesize()) {
            throw new LargeFileSizeException('Maximum uploadable file size is
             larger than maximum uploadable file size which is defined in php.ini!');

            exit;
        }

        $ins->encryptFilename();

        $ins->addPrefix();

        if (! $ins->rename()) {
            $ins->error[] = 9;     // File Duplication
            $ins->fail = true;
        }

        $ins->config['maxFileSize'] = (0 == $ins->config['maxFileSize'])
            ? UploadedFile::getMaxFilesize() : $ins->config['maxFileSize'];

        if ($ins->fileInfo['fileSize'] > $ins->config['maxFileSize']) {
            $ins->error[] = 4;     // File size is larger than $config['maxFileSize']
            $ins->fail = true;
        }

        if (! $ins->checkExtension()) {
            $ins->error[] = 5;     // File type is not allowed to be uploaded
            $ins->fail = true;
        }

        if (! $ins->file->isValid()) {
            $ins->error[] = $ins->file->getError();
            $ins->fail = true;
        }

    }

    /**
     * Set method for config
     *
     * @param  array $config Cusrom configuration to set
     * @return void
     **/
    public function setConfig($config)
    {
        if (! is_null($config)) {
            $this->config = array_replace_recursive($this->config, $config);
        }
    }

    /**
     * This method will upload files to a specific path.
     *
     * @return void
     * @author
     **/
    public function upload()
    {

        Event::call('reborn.fielupload.upload', array($this));

        if (! $this->isFail()) {

            $path = $this->config['path'];
            $file = $this->fileInfo['savedName'];
            $chmod = $this->config['fileChmod'];

            $this->file->move($path, $file);

            chmod($path . $file, $chmod);

            $this->addExtraData();

        }

    }

    /**
     * Check the extension is allowed to be uploaded or not
     *
     * @return boolean
     **/
    protected function checkExtension()
    {

        if (empty($this->config['allowedExt'])) {
            return true;
        }

        return (in_array($this->fileInfo['extension'], $this->config['allowedExt']))
                ? true : false;

    }

    /**
     * Add extra data to file info
     *
     * @return void
     **/
    protected function addExtraData()
    {

        $this->fileInfo['path'] = $this->config['path'];
        $this->fileInfo['fullPath'] = $this->config['path']
                                            . $this->fileInfo['savedName'];

        list($group, $type) = explode('/', $this->fileInfo['mimeType']);

        if ('image' == $group) {
            list($width, $height, $type, $attr) = getimagesize(
                                                    $this->fileInfo['fullPath']
                                                );

            $this->fileInfo['width'] = $width;
            $this->fileInfo['height'] = $height;

        }

    }

    /**
     * Add prefix to file name
     *
     * @return void
     **/
    protected function addPrefix()
    {

        if (! empty($this->config['prefix'])) {

            $this->fileInfo['savedBaseName'] = $this->config['prefix']
                    . $this->fileInfo['savedBaseName'];

            $this->fileInfo['savedName'] = $this->config['prefix']
                    . $this->fileInfo['savedName'];
        }

    }

    /**
     * Create directory if $config['createDir'] is set to true
     *
     * @return boolean
     **/
    protected function createDir()
    {

        if ($this->config['createDir']) {
            return (Dir::is($this->config['path'])) ? true : Dir::make(
                    $this->config['path'],
                    $this->config['dirChmod'],
                    $this->config['recursive']
                );
        }

        return true;

    }

    /**
     * Hash filename with by ushing md5
     *
     * @return void
     **/
    protected function encryptFilename()
    {

        if ($this->config['encName']) {
            $hashed = hash('md5', $this->fileInfo['originBaseName']);

            $this->fileInfo['savedBaseName'] = $hashed;

            $this->fileInfo['savedName'] = $hashed . '.' . $this->fileInfo['extension'];
        } else {
            $this->fileInfo['savedBaseName'] = $this->fileInfo['originBaseName'];

            $this->fileInfo['savedName'] = $this->fileInfo['originName'];
        }

    }

    /**
     * File renaming function. If filename is already exist,
     * this method will rename the uploaded filename with serial surfix.
     * (eg. file_1.ext, file_2.ext)
     *
     * @return String Renamed filename will return
     **/
    protected function rename()
    {

        if ($this->config['overwrite']) {
            return true;
        }

        $savedName = $this->fileInfo['savedName'];
        $savedBaseName = $this->fileInfo['savedBaseName'];
        $file = $this->config['path'] . $savedName;

        if (File::is($file)) {

            if ($this->config['rename']) {

                while (File::is($file)) {

                    $matching = preg_match_all('/^(\w*)_(\d\d?)\.(\w*)$/',
                        $savedName, $match);

                    if ($matching) {

                        $counter = ((int) $match[2][0])+1;

                        $savedBaseName = $match[1][0] . '_' . $counter;

                        $savedName = $savedBaseName . '.' . $match[3][0];

                    } else {

                        $savedBaseName = $savedBaseName. '_1';

                        $savedName = $savedBaseName . '.'
                                    . $this->fileInfo['extension'];
                    }

                }

                $this->fileInfo['savedName'] = $savedName;
                $this->fileInfo['savedBaseName'] = $saveBasedName;

            } else {
                return false;

            }
        }

        return true;
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

            case 'getFileUpload':
                return $this->fileUpload;
                break;

            case 'isFail':
                return $this->fail;
                break;

            case 'getError':
                return $this->error;
                break;

            default:
                return false;
                break;
        }

    }
}
