<?php

namespace Media\Lib;

use Media\Model\MediaFolders as MFolders;
use Media\Model\MediaFiles as MFiles;

/**
 * Plugins class for media module.
 * This class will interact with other module.
 *
 * @package Media\Lib
 * @author RebornCMS Development Team
 **/
class Plugins
{

    public static function featured()
    {
        dump($this->request, true);
    }

    public static function viewPdf($id, $type = 'link', $width = 600, $height = 780)
    {
        $file = MFiles::find($id);

        $filePath = rbUrl() . date('Y', strtotime($file->created_at)) . DS
            . date('m', strtotime($file->created_at)) . DS  . $file->filename;

        $filePath = 'https://docs.google.com.viewer?url=' . rawurlencode($filePath);

        $iframe = "<iframe src=".$filePath." width = '".$width."' height = '"
            .$height."' style = 'border: none;'></iframe>";

        return ($type == 'link') ? $filePath : $iframe;
    }

    /**
     * By using this method, can get files by name.
     * **! Warnging !**
     * Using this method is not good.
     *
     * @param String $name File name
     * @return Object 
     * @author RebornCMS Development Team
     **/
    public static function getFileByName ($name)
    {
        $result = MFiles::where('name', '=', $name)->get();

        return $result;
    }

    /**
     * Get all files or folers or files and folders by folder slug
     *
     * @param String $slug Slug of folder (none = media home page)
     * @param String $pointer What you want to get. (file, folder, all)
     * @return array
     * @author RebornCMS Development Team
     **/
    public static function getBySlug ($slug = 'none', $pointer = 'all')
    {
        if ($pointer == 'file') {
            if ($slug != 'none') {
                $folderId = MFolders::where('slug', '=', $slug)->first();
                $files = MFiles::where('folder_id', '=', $folderId->id)->get();

                return $files;
            }
            $files = MFiles::where('folder_id', '=', 0)->get();

            return $files;

        } elseif ($pointer == 'folder') {
            if ($slug != 'none') {
                $folderId = MFolders::where('slug', '=', $slug)->first();
                $folders = MFolders::where('folder_id', '=', $folderId['id'])->get();

                return $folders;
            }
            $folders = MFolders::where('folder_id', '=', 0)->get();

            return $fodlers;
        } else {
            if ($slug != 'none') {
                $folderId = MFolders::where('slug', '=', $slug)->first();
                $result['folder'] = MFolders::where('folder_id', '=', $folderId['id'])->get();
                $result['file'] = MFiles::where('folder_id', '=', $folderId['id'])->get();

                return $result;
            }
        }
    }

} // END classPlugins