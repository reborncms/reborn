<?php

namespace Reborn\Util;

use Reborn\Exception\RbException as RbException;

/**
 * Uploader Class for Reborn.
 * This class will take actions for 'Uploading' and 'Downloading'
 * Can be accept usser config like :
 *
 * $config = array(
 *      'encryptFileName'   => true,
 *      'savePath'          => "UPLOAD"."myupload",
 *      'prefix'            => 'myPrefix_',
 *      'maxFileSize'       => 0,
 *      'createDir'         => true,
 *      'overwrite'         => true,
 *      'rename'            => true,
 *      'allowedExt'        => array('jpg', 'jpeg', 'png', 'gif', 'tif')
 *  );
 * \Uploader::initialize('files', $config);
 * if (\Uploader::isSuccess()) {
 *      \Uploader::upload('files');
 * } else {
 *      foreach(\Uploader::errors() as $error) {
 *           echo $error;
 *      }
 * }
 *
 * @package Reborn\Util
 * @author RebornCMS Development Team
 **/
class Uploader
{
    /**
     * Variable for setting upload configration
     *
     * @var array
     **/

    private static $config = array(
            'encryptFileName'   => false,
            'savePath'          => '',
            'prefix'            => '',
            'maxFileSize'       => 0,
            'createDir'         => false,
            'overwrite'         => false,
            'rename'            => false,
            //'fileChmod'           => 777,
            //'dirChmod'            => 777,
            'allowedExt'        => array(),
        );

    /**
     * Error codes
     *
     * @var array
     **/
    private static $errorCodes = array(
            'notAllowExt'   => 'This file type is not allowed to be uploaded.',
            'largeFile'     => 'File is larger than ',
            'fileExist'     => 'File is already exist.',
        );

    /**
     * This is can for errors
     *
     * @var array $errors
     **/
    private static $errors = array();

    /**
     * undocumented class variable
     *
     * @var string
     **/
    private static $success = true;

    /**
     * This method is to set custom error code
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public static function setErrorCode($errCode) {
        array_replace_recursive(static::$errorCodes, $errCode);
    }

    /**
     * This method will check success or not and return boolean value
     *
     * @return boolean $success
     * @author RebornCMS Development Team
     **/
    public static function isSuccess() {
        return static::$success;
    }

    /**
     * This method will return errors concern with file
     *
     * @return array $errors
     * @author RebornCMS Development Team
     **/
    public static function errors() {
        return static::$errors;
    }

    /**
     * Initializing the files
     *
     * @param String $key Name of input field. The default is 'files'.
     * @param array $config The array of upload Configration.
     * @return void
     * @author RebornCMS Development Team
     **/
    public static function initialize($key = 'files', $config = array())
    {
        $files = \Input::file($key);

        if (empty($files)) {
            throw new RbException("There is no file to upload.
                                Please check 'enctype' of your 'form' tag.");
            exit;
        }

        // Bind default config with user definded config
        if (! empty($config)) {
            static::$config = array_replace_recursive(static::$config, $config);
        }

        // Checking the directory is existed or not
        if (! static::$config['createDir']) {
           if (! is_dir(static::$config['savePath'])) {
                throw new RbException('Directory not found.');
                exit;
           }
        }

        // Convert one dimation array to multi-dimational array
        if (! is_array($files)) { $files = array($files); }

        $i = 0;
        $j = 0;

        foreach ($files as $file) {
            if ($file->isValid()) {
                // Check File exist or not
                if (! static::$config['encryptFileName']) {
                    $thePath = static::$config['savePath'] .
                                        $file->getClientOriginalName();
                    if (! static::$config['overwrite']) {
                        if (! static::$config['rename']) {
                            if (file_exists($thePath)) {
                                static::$success = false;
                                static::$errors[$i][$j] = self::$errorCodes['fileExist'];
                                $j++;
                            }
                        }
                    }
                }

                // Check maximum file size to upload
                if (static::$config['maxFileSize'] != 0) {
                    if ($file->getClientSize() > static::$config['maxFileSize']) {
                        static::$success = false;
                        static::$errors[$i][$j] = self::$errorCodes['largeFile'] .
                                            static::$config['maxFileSize'] . '.';
                        $j++;
                    }
                }

                // Check extension
                if (! empty(static::$config['allowedExt'])) {
                    $fileExt = strtolower($file->getClientOriginalExtension());
                    if (! in_array($fileExt, static::$config['allowedExt'])) {
                        static::$success = false;
                        static::$errors[$i][$j] = static::$errorCodes['notAllowExt'];
                        $j++;
                    }
                }

                // Show which file cause the errors.
                if (! static::isSuccess()) {
                    static::$errors[$i]['errorAt'] = $file->getClientOriginalName();
                    $j++;
                }
            } else {
                dump($file->getError);
            }
            $i++;
        }
    }

    /**
     * Uplod function for RebornCMS. This function will be called by other class to upload file/files.
     *
     * @param String $key Name of input field. The default is 'files'.
     * @param array $config The array of upload Configration.
     * @return array $uploadedData Uploaded data
     * @author RebornCMS Development Team
     **/
    public static function upload($key = 'files')
    {
        $files = \Input::file($key);

        if (empty($files)) {
            throw new RbException("There is no file to upload.");
        }

        // Convert one dimation array to multi-dimational array
        if (! is_array($files)) { $files = array($files); }

        $uploadedData = array();

        $i = 0;

        foreach ($files as $file) {
            if ($file->isValid()) {
                $uploadedData[$i]['savedName'] = $file->getClientOriginalName();
                $uploadedData[$i]['originalName'] = $file->getClientOriginalName();
                $uploadedData[$i]['fileSize'] =
                    static::sizeConverter($file->getClientSize());
                $uploadedData[$i]['fileType'] = $file->getClientMimeType();
                $baseName = explode('.', $uploadedData[$i]['savedName']);
                $ext = array_pop($baseName);
                $uploadedData[$i]['baseName'] = implode('.', $baseName);
                $uploadedData[$i]['extension'] = $ext;

                if (static::$config['encryptFileName']) {
                    $exploded = explode('.',$file->getClientOriginalName());
                    $ext = array_pop($exploded);

                    $uploadedData[$i]['savedName'] =
                                hash('md5', $file->getBasename($ext)) . '.' . $ext;
                }

                if (! empty(static::$config['prefix'])) {
                    $uploadedData[$i]['savedName'] = static::$config['prefix'] .
                                                    $uploadedData[$i]['savedName'];
                }

                if (file_exists(static::$config['savePath'] .DS.
                    $uploadedData[$i]['savedName'])) {
                    if (static::$config['rename']) {
                        while (file_exists(static::$config['savePath'] .DS.
                                            $uploadedData[$i]['savedName'])) {
                           $uploadedData[$i]['savedName'] =
                                static::rename($uploadedData[$i]['savedName']);
                        }
                    }
                }
                $file->move(static::$config['savePath'],
                    $uploadedData[$i]['savedName']);
            } else {
                dump($file->getError());
            }
        }

        return $uploadedData;
    }

    /**
     * This method will remove unnecessary dots, control character and so on.
     *
     * @return
     * @author
     **/
    private function trimFileName($fileName)
    {

    }

    /**
     * This method will convert the given filesize in 'b' to 'k', 'm', 'g'.
     *
     * @param int $size
     * @return string Converted filesize with 'k', 'm' or 'g'
     * @author RebornCMS Development Team
     **/
    private static function sizeConverter($size)
    {
        // @TODO - Let's flexible

        $tmpSize = $size;

        if ($tmpSize >= 1073741824) {
            return $tmpSize = ceil((float)($tmpSize/1073741824)) . 'g';
        }

        if ($tmpSize >= 1048576) {
            return $tmpSize = ceil((float)($tmpSize/1048576)) . 'm';
        }

        if ($tmpSize >= 1024) {
            return $tmpSize = ceil((float)($tmpSize/1024)) . 'k';
        }

        return $tmpSize;
    }

    /**
     * File renaming function. If filename is already exist,
     * this method will rename the uploaded filename with serial surfix.
     * (eg. file_1.ext, file_2.ext)
     *
     * @param String $fileDir the directory you want to upload
     * @param String $fileName the name of the file you uploaded
     * @return String Renamed filename will return
     * @author RebornCMS Development Team
     **/
    private static function rename($fileName)
    {
        $matching = preg_match_all('/^(\w*)_(\d)(\.\w*)$/', $fileName, $match);

        $noExt = explode('.', $fileName);
        $ext = array_pop($noExt);
        $noExt = implode('.', $noExt);

        if (! empty($match[0])) {
            $noEnderscore = explode('_', $noExt);
            $number = array_pop($noEnderscore);
            $noEnderscore = implode('_', $noEnderscore) . '_' . ++$number . '.' . $ext;

            return $noEnderscore;
        } else {
            return /*$tmpFileName = */$noExt . '_1.' . $ext;
        }
    }

    /**
     * This method will return the maximum uploadable file size.
     *
     * @return String
     * @author RebornCMS Development Team
     **/
    public static function getMaxFilesize()
    {
        return \Symfony\Component\HttpFoundation\File\UploadedFile::getMaxFileSize();
    }

} // END class Uploader
