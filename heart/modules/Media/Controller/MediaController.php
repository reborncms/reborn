<?php

namespace Media\Controller;

use Media\Model\MediaFiles as MFiles;
use Media\Lib\ImageHandler as Image;
//
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

    /**
     * This method will show images.
     *
     * @param int $id File id
     * @param int $width Width in pixel (Optional)
     * @param int $height Height in pixel (Optional)
     * @author RebornCMS Development Team
     **/
    public function thumb ($id, $width = 0, $height = 0)
    {
        $file = MFiles::where('id', '=', $id)->first();

        $filePath = UPLOAD . date('Y', strtotime($file->created_at)).DS.date(
            'm', strtotime($file->created_at)) . DS  . $file->filename;

        $image = new Image($filePath);

        if ($width == 0) {
            $w = $file->width;
        } else {
            $w = ($width > $file->width) ? $file->width : $width;
        }

        if ($height == 0) {
            $h = $file->height;
        } else {
            $h = ($height > $file->height) ? $file->height : $height;
        }

        $cacheImg = static::$cachePath.'reborn-'.$w.'-'.$h.'-'.$file->filename;

        $expire = 3600;

        $response = new \Symfony\Component\HttpFoundation\Response();

        if ($expire) {
            $response->setPublic();
            $response->headers->add(array(
                'Pragma' => 'public',
                'Expires' => gmdate('D, d M Y H:i:s', time()+$expire) . 'GMT',
                ));
        }

        if (! \File::is($cacheImg) OR (filemtime($cacheImg) < filemtime($filePath))) {

            if ($width != 0 AND $height != 0) {
                $image->resize($w, $h, 'exact');
                $image->saveImage($cacheImg, '100');
            } else {
                $image->resize($w, $h, 'scale');
                $image->saveImage($cacheImg, '100');
            }
        } elseif(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            AND (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($cacheImg))
            AND $expire) {

            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s',
            filemtime($cacheImg) . ' GMT', true, 304));
        }
        $response->headers->add(array(
                'content-type' => $file->mime_type,
                'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($cacheImg)) . 'GMT',
                )
            );

        $response->send();

        ob_clean();
        flush();
        readfile($cacheImg);
        exit;
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
