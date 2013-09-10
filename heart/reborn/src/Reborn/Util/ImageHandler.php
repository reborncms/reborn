<?php

namespace Reborn\Util;

/**
 * Image manipulation class for RebornCMS
 *
 * @package Reborn\Util
 * @author Myanmar Links Professional Web Development Team
 **/
class ImageHandler
{

    /**
     * Container for file
     *
     * @var String
     **/
    public $file = null;

    /**
     * Path to save the new image
     *
     * @var String
     **/
    public $path = null;

    /**
     * Image original width
     *
     * @var int
     **/
    public $originWidth = 0;

    /**
     * Image original height
     *
     * @var string
     **/
    public $originHeight = 0;

    /**
     * Expected width
     *
     * @var String
     **/
    public $expectedWidth = 0;

    /**
     * Expected height
     *
     * @var string
     **/
    public $expectedHeight = 0;

    /**
     * Image resource identifier
     *
     * @var
     **/
    protected $rawImage = null;

    /**
     * Symfony file object
     *
     * @var object
     **/
    protected $fileObj = null;

    /**
     * Constructor method for ImageHandler class
     *
     * @param String $file File name with extension
     *
     * @return void
     **/
    function __construct($file)
    {
        if (! extension_loaded('gd')) {
            throw new \RbException('PHP gd library is disable or not installed.');
        }

        if (! \File::is($file)) {
            throw new \RbException("File not found! [\"$file\"]");
        }

        if (! is_readable($file)) {
            throw new \RbException("File is not readable! [\"$file\"]");
        }

        $this->file = $file;

        $this->checkImage();
    }

    /**
     * Resizing image
     *
     * @param int $width Expected width to resize
     * @param int $height Expected width to resize
     *
     * @return void
     **/
    public function resize($width = 0, $height = 0)
    {
        $this->sizeAdjustment($width, $height);

        $this->rawImage = @imagecreatetruecolor($this->expectedWidth,
            $this->expectedHeight) or new \RbException(
            'Cannot initialize new GD image stream.');

        $createdImg = $this->createImage();

        @imagecopyresampled($this->rawImage, $createdImg, 0, 0, 0, 0,
            $width, $height, $this->originWidth, $this->originHeight);
    }

    /**
     * Cropping image
     *
     * @param int $width Expected width to crop
     * @param int $height Expected height to crop
     * @param int $x
     * @param int $y
     *
     * @return void
     **/
    public function crop($width = 0, $height = 0, $x = 0, $y = 0)
    {
        $this->sizeAdjustment($width, $height);

        $this->rawImage = @imagecreatetruecolor($this->expectedWidth,
            $this->expectedHeight) or new \RbException(
            'Cannot initialize new GD image stream.');

        $createdImg = $this->createImage();

        imagecopy($this->rawImage, $createdImg, 0, 0, $x, $y, $width, $height);
    }

    /**
     * Save image to a specific directory
     *
     * @param String $path A directory
     *
     * @return void
     **/
    public function saveImage($path, $quality = 100)
    {
        $path = (preg_match('/(.*)(.)(jpg|jpeg|png|gif)$/', $path)) ? $path
                                        : $path . $this->fileObj->getFilename();
        ob_start();

        switch (strtolower($this->fileObj->getExtension())) {
            case 'jpg':
            case 'jpeg':
                if (imagetypes() & IMG_JPG) {

                    imagejpeg($this->rawImage, $path, $quality);
                }

                break;
            case 'png':
                $scaling = 9 - (round(($quality*9)/100));
                if (imagetypes() & IMG_PNG) {
                    imagepng($this->rawImage, $path, $scaling);
                }

                break;
            case 'gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->rawImage, $path);
                }

                break;
            default:

                break;
        }

        //$data = ob_get_contents();

        //ob_end_clean();

        return imagedestroy($this->rawImage);
    }

    /**
     * Creating new image
     *
     * @return resource $img
     **/
    protected function createImage()
    {
        switch (strtolower($this->fileObj->getExtension())) {
            case 'jpg':
            case 'jpeg':
                $img = @imagecreatefromjpeg($this->file);

                break;

            case 'png':
                @imagealphablending($this->rawImage, false);
                @imagesavealpha($this->rawImage, true);
                $transparent = imagecolorallocatealpha($this->newImage, 255, 255,
                    255, 0);
                imagefilledrectangle($raw->newImage, 0, 0, 0, 0, $transparent);
                $img = imagecreatefrompng($this->file);

                break;

            case 'gif':
                @imagecolortransparent($this->newImage, @imagecolorallocate(
                    $this->rawImage, 0, 0, 0));
                $img = @imagecreatefromgif($this->file);

                break;

            default:

                break;
        }

        return $img;
    }

    /**
     * Adjusting image width height
     *
     * @return void
     **/
    protected function sizeAdjustment($width, $height)
    {
        if (0 === $width and 0 === $height) {

            $this->expectedWidth = $this->originWidth;
            $this->expectedHeight = $this->originHeight;

        } elseif (0 === $height) {

            $this->expectedHeight = $this->doScale($width, 'height');

        } elseif (0 === $width) {

            $this->expectedWidth = $this->doScale($height, 'width');

        } else {
            $this->expectedWidth = $width;
            $this->expectedHeight = $height;
        }
    }

    /**
     * Scaling
     *
     * @param int $expected Expected width or height
     *
     * @return int $result Scaled result
     **/
    protected function doScale($expected, $scaleFor = 'width')
    {
        switch ($scaleFor) {
            case 'width':
                $result = ($expected / $this->originHeight) * $this->originWidth;
                break;

            case 'height':
                $result = ($expected / $this->originWidth) * $this->originHeight;
                break;

            default:
                trigger_error("Something's wrong in scaling.", E_USER_ERROR);
                break;
        }

        return $result;
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    protected function checkImage()
    {
        $size = @getimagesize($this->file);

        $matching = preg_match('/^(image)\/(\w*)$/', $size['mime'], $matches);

        if (! $matching) {
            throw new \RbException("Wrong file type ! [\"$this->file\"]");
        }

        $this->originWidth = $size[0];
        $this->originHeight = $size[1];

        $this->fileObj = new \Symfony\Component\HttpFoundation\File\File($this->file);
    }

} // END class ImageHandler
