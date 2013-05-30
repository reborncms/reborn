<?php

namespace Media\Lib;

/**
 * This Class will handle images.
 *
 * @package Media\Lib
 * @author RebornCMS Development Team
 **/
class ImageHandler
{

    /**
     * Container for file with path
     *
     * @var string
     **/
    private $file = '';

    private $width = 0;

    private $height = 0;

    private $newImage = null;

    /**
     * Construct method for ImageHandler
     *
     * @param String $file Filename with path
     * @author RebornCMS Development Team
     **/
    public function __construct($file)
    {
        if (! \File::is($file)) {
            throw new \RbException('File not found.');
            exit;
        }

        $this->file = $file;

        $size = @getimagesize($file);

        if ($size[0] == 0 OR $size[1] == 0) {
            throw new \RbException('The given file is not image.');
            exit;
        }

        $this->width = $size[0];
        $this->height = $size[1];
    }

    /**
     * This method will resize the given image
     *
     * @param String $target filename with file path
     * @param int $width the width you want to resize
     * @param int $height the height you want ot resize
     * @param String $option option for resing image
     *          avalible options - 'scale', 'exact'
     *             @COMING SOON - fit
     * @return
     * @author RebornCMS Development Team
     **/
    public function resize($width = 0, $height = 0, $option = 'scale')
    {
        switch ($option) {
            case 'scale':
                $size = $this->calRatio($width, $height);
                break;
            case 'exact':
                $size['width'] = ($width !=0 ) ? $width : $this->width;
                $size['height'] = ($height != 0) ? $height : $this->height;
                break;
            default:
                break;
        }

        $this->newImage = @imagecreatetruecolor($size['width'], $size['height']);

        $createdImg = $this->createImg($size['width'], $size['height']);

        @imagecopyresampled($this->newImage, $createdImg, 0, 0, 0, 0,
            $size['width'], $size['height'], $this->width, $this->height);
    }

    /**
     * This method will save image to expected directory
     *
     * @param String $path Directory you want to move to.
     * @param int $quality Image compression quality (0 - 100)
     * @author RebornCMS Development Team
     **/
    public function saveImage($path, $quality = 72)
    {
        $ext = strtolower(substr(strrchr($this->file, '.'), 1));
        ob_start();

        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->newImage, $path, $quality);
                }
                break;
            case 'png':
                $scaling = 9 - (round(($quality*9)/100));
                if (imagetypes() & IMG_PNG) {
                    imagepng($this->newImage, $path, $scaling);
                }
                break;
            case 'gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->newImage, $path);
                }
                break;
            default:
                break;
        }

        @imagedestroy($this->newImage);
    }

    public function crop($width = 0, $height = 0, $option = 'scale')
    {

    }

    private function createImg($w = null, $h = null)
    {
        $ext = strtolower(substr(strrchr($this->file, '.'), 1));

        switch ($ext) {
            case 'jpg':
            case 'jpeg';
                $img = @imagecreatefromjpeg($this->file);
                break;
            case 'png':
                @imagealphablending($this->newImage, false);
                @imagesavealpha($this->newImage, true);
                $transparent = imagecolorallocatealpha($this->newImage, 255, 255, 255, 0);
                imagefilledrectangle($this->newImage, 0, 0,$w, $h, $transparent);
                $img = imagecreatefrompng($this->file);
                break;
            case 'gif':
                @imagecolortransparent($this->newImage, @imagecolorallocate(
                    $this->newImage, 0, 0, 0));
                $img = @imagecreatefromgif($this->file);
                break;
            default:
                $img = false;
                break;
        }

        return $img;
    }

    /**
     * This method can calculate for scaling widht height
     *
     * @param int $width
     * @param int $height
     * @return int $size Calculated value
     * @author RebornCMS Development Team
     **/
    private function calRatio($width = 0, $height = 0) {
        $size = array();

        if ($width != 0) {
            $size['width'] = $width;
            $size['height'] = round(($this->height*$width)/$this->width);

            return $size;
        }

        if ($height != 0) {
            $size['width'] = round(($this->width*$height)/$this->height);
            $size['height'] = $height;

            return $size;
        }
    }
} // END class ImageHandler
