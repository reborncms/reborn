<?php

namespace Media\Lib;

use Media\Model\Folders;
use Media\Model\Files;

/**
 * Plugins class for media module.
 * This class will interact with other module.
 *
 * @package Media\Lib
 * @author RebornCMS Development Team
 **/
class Plugins
{
    /**
     * Getting file
     *
     * @param mix $target Filename or id
     *
     * @return Object 
     **/
    public static function getFile($target, $json = false)
    {
        $file = (is_numeric($target)) ? Files::find($target) : 
                                Files::where('filename', '=', $target)->get();

        return ($json) ? $file->toJson() : $file;
    }

    /**
     * Getting files by folder id
     *
     * @param int $id folder id
     *
     * @return object
     **/
    public static function getFilesByFolderId($id, $json = false)
    {
        $files = Files::where('folder_id', '=', $id)->get();

        return ($json) ? json_encode($files->toJson()) : $file;
    }

    /**
     * Getting folders by folder id
     *
     * @param int $id folder id
     *
     * @return void
     **/
    public static function getFoldersByFolderId($id, $json = false)
    {
        $folders = Folders::where('folder_id', '=', $id)->get();

        return ($json) ? $folders->toJson() : $folders;
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