<?php

namespace Reborn\Fileupload;

use RbException;
use Dir;
use File;
use Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Reborn\Fileupload\Exception\DirectoryNotCreatedException;
use Reborn\Fileupload\Exception\DirectoryNotWritableException;
use Reborn\Fileupload\Exception\LargeFileSizeException;
use Reborn\Fileupload\Exception\IOException;

/**
 * File uploader class for RebornCMS
 *
 * @package Reborn\Fileupload
 * @author RebornCMS Development Team <reborncms@gmail.com>
 * @link http://reborncms.com Official Website of RebornCMS
 */
class Uploader
{

    /**
     * File object
     * @var Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $file;

    /**
     * Uploaded file information
     *
     * @var array
     **/
    protected $fileInfo = array();

    /**
     * Variable for setting upload configration
     *
     * @var array
     **/
    protected $config = array(
            'encName'       => false,   // Encrypt file name with md5
            'path'          => UPLOAD,  // Directory the uploaded file save to
            'prefix'        => null,      // Add Prefix to filename
            'maxFileSize'   => 0,       // Maximum uploadable file size in byte, 0 means the maximum uploadable size is the same to php.ini
            'overwrite'     => false,   // If set to true the new uploaded file will overwrite to existing file with the same name
            'allowedExt'    => array(), // Extensions, want to allow to be uploaded (eg . array('jpg', 'jpeg', 'zip', 'pdf'))
        );

    /**
     * Initializing the uploader
     * 
     * @param  Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param  array $config 
     * @return Reborn\FileUpload\Uploader
     */
    public static function init(UploadedFile $file = null, Array $config = null)
    {
        $that = new static;

        if (! is_null($file)) {
            $that->fileObject($file);
        }

        if (! is_null($config)) {
            $that->configure($config);
        }

        return $that;
    }

    /**
     * Accessor and mutator for file object
     * 
     * @param  Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return mix
     */
    public function fileObject(UploadedFile $file = null)
    {
        if (is_null($file)) {
            return $this->file;
        } else {
            $this->file = $file;

            $this->makeInfo();

            return $this;
        }
    }

    /**
     * Getting inforamtion of the file
     * @return array
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * Accessor and mutator for config
     * 
     * @param  array  $config 
     * @return mix
     */
    public function configure(Array $config = null)
    {
        if (is_null($config)) {
            return $this->config;
        } else {
            $this->config = array_replace_recursive($this->config, $config);

            $this->fileInfo['canonicalPath'] = $this->config['path']
                . $this->fileInfo['savedName'];

            return $this;
        }
    }

    /**
     * Upload file
     * @return array
     */
    public function upload()
    {
        if (is_null($this->file)) {
            throw new RbException('No file object has been setted.');
            exit;
        }

        $this->process();

        $file = $this->file;
        $path = $this->config['path'];
        $name = $this->fileInfo['savedName'];

        try {
            $file->move($path, $name);

            chmod($this->fileInfo['canonicalPath'], 755);

            return $this->fileInfo;
        } catch (FileException $e) {
            throw new IOException($e->getMessage());
            exit;
        }

    }

    /**
     * Preprocess for file upload
     * @return void
     */
    protected function process()
    {
        $this->makeAllowedExtension();

        $this->makeSize();

        $this->makePath();

        $this->makeEncName();

        $this->makePrefix();

        $this->makeOverwrite();
    }

    /**
     * Handle about file size
     * @return void
     */
    protected function makeSize()
    {
        $uploadable = (0 == $this->config['maxFileSize'])
                        ? UploadedFile::getMaxFileSize()
                        : $this->config['maxFileSize'];

        if ($this->fileInfo['fileSize'] > $uploadable) {
            throw new LargeFileSizeException();
            exit;
        }
    }

    /**
     * Handle about allowed files
     * @return void
     */
    protected function makeAllowedExtension()
    {
        if (! is_null($this->config['allowedExt']) or ! empty($this->config['allowedExt'])) {
            if (is_string($this->config['allowedExt'])) {
                $this->config['allowedExt'] = explode('|', $this->config['allowedExt']);
            }

            if (! in_array($this->fileInfo['extension'], $this->config['allowedExt'])) {
                throw new RbException('The file type is not allowed to be uploaded.');
                exit;
            }   
        }
    }

    /**
     * Prepare about information for uploaded file
     * @return void
     */
    protected function makeInfo()
    {
        $file = $this->file;

        $this->fileInfo = array(
                'originalName'  => $file->getClientOriginalName(),
                'extension'     => strtolower($file->getClientOriginalExtension()),
                'fileSize'      => $file->getClientSize(),
                'mimeType'      => $file->getClientMimeType(),
                'savedName'     => $file->getClientOriginalName(),
                'canonicalPath' => $this->config['path'] . $file->getClientOriginalName(),
                'savedBaseName' => basename($file->getClientOriginalName()
                    . '.' . $file->getClientOriginalExtension()),
                'originalBaseName'  => basename($file->getClientOriginalName()
                    . '.' . $file->getClientOriginalExtension()),
            );
    }

    /**
     * Handle about name encryption
     * @return void
     */
    protected function makeEncName()
    {
        if ($this->config['encName']) {
            $hashed = hash('md5', $this->fileInfo['savedBaseName']);

            $this->fileInfo['savedBaseName'] = $hashed;
            $this->fileInfo['savedName'] = $hashed . '.' . $this->fileInfo['extension'];
            $this->fileInfo['canonicalPath'] = $this->config['path'] 
                                                . $this->fileInfo['savedName'];
        }
    }

    /**
     * Handle about file renaming with sequencial number
     * @return void
     */
    protected function makeOverwrite()
    {
        if (File::is($this->fileInfo['canonicalPath'])) {
            if (! $this->config['overwrite']) {

                $baseName = $this->fileInfo['savedBaseName'];
                $ext = $this->fileInfo['extension'];
                $path = $this->config['path'];

                $count = 0;

                $canonical;
                $savedBaseName;
                $savedName;

                do {
                    $count++;
                    $canonical = $path . $baseName . '_' . $count . '.' . $ext;
                    $savedBaseName = $baseName . '_' . $count;
                    $savedName = $savedBaseName . '.' . $ext;
                } while (File::is($canonical));

                $this->fileInfo['canonicalPath'] = $canonical;
                $this->fileInfo['savedBaseName'] = $savedBaseName;
                $this->fileInfo['savedName'] = $savedName;
            }
        }
    }

    /**
     * Handle about upload path
     * @return void
     */
    protected function makePath()
    {
        if (! Dir::is($this->config['path'])) {

            $old = umask(0); 

            $created = mkdir($this->config['path'], 0777, true);

            umask($old);

            if (! $created) {
                throw new DirectoryNotCreatedException('Directory is not created');
                exit;
            }
        } else {
            if (! is_writable($this->config['path'])) {
                throw new DirectoryNotWritableException('Directory is not writable');
                exit;
            }
        }

        $this->fileInfo['canonicalPath'] = $this->config['path'] . 
                                            $this->fileInfo['savedName'];
    }

    /**
     * Adding prefix to file name
     * @return void
     */
    protected function makePrefix()
    {
        if (! is_null($this->config['prefix'])) {
            $this->fileInfo['savedBaseName'] = $this->config['prefix']
                    . $this->fileInfo['savedBaseName'];

            $this->fileInfo['savedName'] = $this->config['prefix']
                    . $this->fileInfo['savedName'];

            $this->fileInfo['canonicalPath'] = $this->config['path'] 
                    . $this->fileInfo['savedName'];
        }
    }

} // END class Uploader
