<?php 

namespace Reborn\FileUpload;

class FileUpload
{

	/**
     * Variable for setting upload configration
     *
     * @var array
     **/
	private $config = array(
            'encName'       => false,   // Encrypt file name with md5
            'path'          => UPLOAD,  // Directory the uploaded file save to
            'prefix'        => '',
            'maxFileSize'   => 0,
            'createDir'     => false,
            'overwrite'     => false,
            'rename'        => false,
            'fileChmod'     => 0777,
            'dirChmod'      => 0777,
            'recursive'		=> false,
            'allowedExt'    => array(),
        );

	/**
	 * Set method for config
	 *
	 * @param array $config Cusrom configuration to set
	 * @return void
	 **/
	public function setConfig($config)
	{
		$this->config = array_replace_recursive($this->config, $config);
	}

	/**
	 * Get method for config
	 *
	 * @return array $config 
	 **/
	public function getConfig()
	{
		return $this->config;
	}

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
	 * Get method for fileInfo
	 *
	 * @return array $fileInfo An array of file information
	 **/
	public function getFileInfo ()
	{
		return $this->fileInfo;
	}

	/**
	 * Error container
	 *
	 * @var array $errors
	 **/
	protected $errors = array();

	/**
	 * Get method for errors
	 *
	 * @return array $errors
	 **/
	public function getErrors ()
	{
		return $this->errors;
	}

	/**
	 * Possible error message
	 *
	 * @var array $errorMsg
	 **/
	protected $errorMsg = array(
			'notAllowExt'   => 'This file type is not allowed to be uploaded.',
            'largeFile'     => 'File size is too large to upload.',
            'fileExist'     => 'File is already exist.',
		);

	/**
	 * Set method for errorMsg
	 *
	 * @param array $errorMsg Custom error message to set
	 * @return void
	 **/
	public function setErrorMsg ($errorMsg)
	{
		$this->errorMsg = array_replace_recursive($this->errorMsg, $errorMsg);
	}

	/**
	 * Constructer method fo FileUpload calss
	 *
	 * @return void
	 * @author RebornCMS Development Team
	 **/
	function __construct($file)
	{
        $this->file = $file;

        $this->fileInfo = array(
        	'extension'		=> $file->getClientOriginalExtension(),
        	'originName'	=> $file->getClientOriginalName(),
        	'originBaseName'=> basename($file->getClientOriginalName(),
        			'.' . $file->getClientOriginalExtension()),
        	'savedName'		=> '',
        	'savedBaseName'	=> '',
        	'fileSize'		=> $file->getClientSize(),
        	'mimeType'		=> $file->getClientMimeType(),
        	);
	}

	/**
	 * Initializing the uplod config
	 *
	 * @return void
	 **/
	public function uploadInit ()
	{
		if (! \Dir::is($this->config['path'])) {
			
			if (! $this->config['createDir']) {
				throw new \RbException('Directory not found.');

				exit;
			} else {
				\Dir::make($this->config['path'], $this->config['dirChmod'],
					$this->config['recursive']);
			}
		}

		if (! is_writable($this->config['path'])) {
			throw new \RbException('Directory is not writable !');

			exit;
		}

		if ((! \Dir::is($this->config['path'])) and (! $this->config['createDir'])) {
			throw new \RbException('Directory not found.');
		}

		if (! $this->file->isValid()) {
			$this->errors = array($this->file->getError());
		} else {
			$i = 0;

			if (! $this->config['overwrite']) {
				if (! $this->config['rename']) {
					$f = $this->config['path'].$this->file->getClientOriginalName();
					if (\File::is($f)) {
	                    $this->errors[$i] = $this->errorMsg['fileExist'];
						$i++;
	                }
				}
			}

			if ($this->config['maxFileSize'] != 0 
	            and $this->file->getClientSize() > $this->config['maxFileSize']) {
	            $this->errors[$i] = $this->errorMsg['largeFile'];
	        }

	        $extension = strtolower($this->file->getClientOriginalExtension());

	        if ((! empty($this->config['allowedExt'])) 
	            and (! in_array($extension, $this->config['allowedExt']))) {
	            $this->errors[$i] = $this->errorMsg['notAllowExt'];
	        }	        
		}
	}

	/**
	 * This method will upload files to a specific path.
	 *
	 * @return void
	 * @author 
	 **/
	public function upload ()
	{
		if ($this->config['encName']) {
			$this->fileInfo['savedBaseName'] = hash('md5',
				$this->fileInfo['originBaseName']);

			$this->fileInfo['savedName'] = $this->fileInfo['savedBaseName']
				. '.' . $this->fileInfo['extension'];
		} else {
			$this->fileInfo['savedName'] = $this->fileInfo['originName'];
			$this->fileInfo['savedBaseName'] = $this->fileInfo['originBaseName'];
		}

		if (! empty($this->config['prefix'])) {
			$this->fileInfo['savedBaseName'] = $this->config['prefix'] . $this
				->fileInfo['savedBaseName'];

			$this->fileInfo['savedName'] = $this->config['prefix'] . $this
				->fileInfo['savedName'];
		}

		if (\File::is($this->config['path'] . $this->fileInfo['savedName'])) {
			if ($this->config['overwrite']) {
				$file->move($this->config['path'], $this->fileInfo['savedName']);
			} else {
				while (\File::is($this->config['path'] . $this->fileInfo['savedName'])) {
					$this->rename();
				}
				
				$this->file->move($this->config['path'], $this->fileInfo['savedName']);
			}

			chmod($this->config['path'] . $this->fileInfo['savedName'], 
				$this->config['fileChmod']);

			return true;
		} else {
			$this->file->move($this->config['path'], $this->fileInfo['savedName']);

			return true;
		}
	}

	/**
     * File renaming function. If filename is already exist,
     * this method will rename the uploaded filename with serial surfix.
     * (eg. file_1.ext, file_2.ext)
     *
     * @param String $fileDir the directory you want to upload
     * @param String $name the name of the file you uploaded
     * @return String Renamed filename will return
     * @author RebornCMS Development Team
     **/
    private function rename()
    {
    	$matching = preg_match_all('/^(\w*)_(\d\d?)\.(\w*)$/',
    		$this->fileInfo['savedName'], $match);

    	if ($matching) {
    		$count = ((int)$match[2][0])+1;

    		$this->fileInfo['savedName'] = $match[1][0] . '_' . $count . '.'
    			. $match[3][0];

    		$this->fileInfo['savedBaseName'] = $match[1][0] . '_' . $count;
    		
    	} else {
    		$this->fileInfo['savedName'] = $this->fileInfo['savedBaseName'] 
    			. '_1' . '.' . $this->fileInfo['extension'];

    		$this->fileInof['savedBaseName'] = $this->fileInfo['savedBaseName'] . '_1';
    	}
    }
}