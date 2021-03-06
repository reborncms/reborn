<?php

namespace Media\Controller;

use Media\Model\Files;
use Reborn\Util\ImageHandler as Image;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * This is front controller for Media Module.
 *
 * @package Media\Controller
 * @author RebornCMS Development Team
 **/
class MediaController extends \PublicController
{
    /**
     * This variable is a container for cache path.
     *
     * @var string
     **/
    private static $cachePath = '';

    /**
     * Filename with directory
     *
     * @var String
     **/
    private $cacheImage = null;

    /**
     * Before function for Media Controller
     *
     * @return void
     **/
    public function before()
    {
        if (! \Dir::is(STORAGES . 'cache/media/')) {
            \Dir::make(STORAGES . 'cache/media/', 0777, TRUE);
        }

        static::$cachePath = STORAGES . 'cache/media/';
    }

    public function image($target = null, $width = 0, $height = 0, $crop = false,
        $x = 0, $y = 0)
    {

        $file = (is_numeric($target)) ? Files::find($target) : Files::where('filename',
            '=', $target)->first();

        if (empty($file)) {
            return $this->notFound();
        }

        $width = ($width > $file->width) ? $file->width : $width;

        $height = ($height > $file->height) ? $file->height : $height;

        if (0 === $width and 0 === $height) {

            $width = $file->width;
            $height = $file->height;

        } elseif (0 === (int) $height) {

            $height = doScale($file->width, $file->height, $width, 'height');

        } elseif (0 === (int) $width) {

            $width = doScale($file->width, $file->height, $height, 'width');

        }

        $filePath = UPLOAD . date('Y', strtotime($file->created_at)).DS.date(
                'm', strtotime($file->created_at)) . DS  . $file->filename;
        $this->cacheImage = static::$cachePath.'r-'.$width.'-'.$height.'-'
            .$file->filename;

        if (!\File::is($this->cacheImage) or (filemtime($filePath) >
            filemtime($this->cacheImage))) {
            $image = new \Zebra_Image();
            $image->source_path = $filePath;
            $image->target_path = $this->cacheImage;
            $image->resize($width, $height, ZEBRA_IMAGE_CROP_CENTER, -1);
        }

        return $this->showImage($file);
    }

    protected function showImage($file, $notModify = false)
    {
        $status = 200;

        $expire = 6 * 60 * 60; // 6 hours

        $headers['Pragma'] = 'public';
        $headers['Expires'] = gmdate('D, d M Y H:i:s', time()+$expire) . ' GMT';
        $headers['Cache-Control'] = 'public';
        $headers['Content-Type'] = $file->mime_type;

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            AND (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) ==
                filemtime($this->cacheImage)) AND $expire) {
            $status = 304;
        }

        $cacheImg = $this->cacheImage;

        $headers['Last-Modified'] = gmdate('D, d M Y H:i:s', filemtime($cacheImg)) . ' GMT';

        return StreamedResponse::create(function () use ($cacheImg) {
                ob_end_clean();
                readfile($cacheImg);
            }, $status, $headers)->send();
    }

    public function download($id)
    {
        $file = Files::find($id);

        if (! is_null($file) ) {
             $path = UPLOAD .
                    date('Y', strtotime($file->created_at)) . DS .
                    date('m', strtotime($file->created_at)) . DS .
                    $file->filename;

            if (\File::is($path)) {
                return \Response::binary($path, $file->name.'.'.$file->extension);
            }
        }

        return \Response::clueless();
    }

    public function test()
    {

    }

} // END class MediaController
