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
     * @author
     **/
    public function before()
    {
        if(! \Dir::is(STORAGES . 'cache/media/'))
        {
            \Dir::make(STORAGES . 'cache/media/', 0777, TRUE);
        }

        static::$cachePath = STORAGES . 'cache/media/';
    }

    public function image($target, $width = 0, $height = 0, $crop = false,
        $x = 0, $y = 0)
    {
        $file = (is_int($target)) ? Files::find($target) : Files::where('filename',
            '=', $target)->first();

        if (empty($file)) {
            return $this->notFound();
        }

        $width = ($width > $file->width) ? $file->width : $width;

        $height = ($height > $file->height) ? $file->height : $height;

        if (0 === $width and 0 === $height) {

            $width = $file->width;
            $height = $file->height;

        } elseif (0 === (int)$height) {

            $height = doScale($file->width, $file->height, $width, 'height');

        } elseif (0 === (int)$width) {

            $width = doScale($file->width, $file->height, $height, 'width');

        }

        $this->cacheImage = static::$cachePath.'reborn-'.$width.'-'.$height.'-'
            .$file->filename;

        $filePath = UPLOAD . date('Y', strtotime($file->created_at)).DS.date(
                'm', strtotime($file->created_at)) . DS  . $file->filename;

        if (!\File::is($this->cacheImage) or (filemtime($filePath) > 
            filemtime($this->cacheImage))) {

            $image = new Image($filePath);

            $image->resize($width, $height);

            if($image->saveImage($this->cacheImage))
            {
                return $this->showImage($file);
            }
        }

        return $this->showImage($file, true);

    }

    protected function showImage($file, $notModify = false)
    {
        $expire = 6 * 60 * 60; // 6 hours

        $headers['Pragma'] = 'public';
        $headers['Expires'] = gmdate('D, d M Y H:i:s', time()+$expire) . ' GMT';
        $headers['Cache-Control'] = 'public';
        $headers['Content-Type'] = $file->mime_type;

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            AND (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == 
                filemtime($this->cacheImage)) AND $expire) {

            header('Last-Modified : '.gmdate('D, d M Y H:i:s', 
                filemtime($this->cacheImage)) . ' GMT', true, 304);

        }

        $cacheImg = $this->cacheImage;

        return StreamedResponse::create(function() use($cacheImg){
                ob_flush();
                flush();
                readfile($cacheImg);
            }, 200, $headers)->send();
    }

    public function download ($id)
    {
        $file = MFiles::where('id', '=', $id)->first();

        $path = UPLOAD . date('Y', strtotime($file->created_at)).DS.date(
            'm', strtotime($file->created_at)) . DS  . $file->filename;

        if (\File::is($path)) {
            $response = new \Symfony\Component\HttpFoundation\Response();

            $response->setPrivate();

            $disposition = $response->headers->makeDisposition('attachment',
                $file->name.'.'.$file->extension, 'rb-'.time().'.'.$file->extension);

            $response->headers->add(array(
                'Content-Description'       => 'File Transfer',
                'Content-Disposition'       => $disposition,
                'Content-Type'              => $file->mime_type,
                'Content-Transfer-Encoding' => 'binary',
                'Expires'                   => 0,
                'Pragma'                    => 'public',
                ));

            $response->send();

            ob_clean();
            flush();
            readfile($path);
            exit;
        }

    }
} // END class MediaController
