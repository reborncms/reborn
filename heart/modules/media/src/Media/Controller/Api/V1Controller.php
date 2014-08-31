<?php

namespace Media\Controller\Api;

use Media\Model\Files;
use Media\Model\Folders;
use Api\Controller\ApiController;

class V1Controller extends ApiController
{

    public function files($id = null)
    {
        $files = null;

        if (is_null($id)) {
            $files = Files::all()->toArray();
        } else {
            $files = Files::find($id)->toArray();
        }

        return $this->returnJson($files);
    }

    /**
     * Get files by specific folder id
     * @param  int $folderId Folder Id
     * @return json
     */
    public function filesByFolder($folderId)
    {
        $files = Files::where('folder_id', $folderId)->get()->toArray();

        return $this->returnJson($files);
    }

    /**
     * Get all images
     * @return json
     */
    public function images()
    {
        $images = Files::imageOnly()->get()->toArray();

        return $this->returnJson($images);
    }

    /**
     * Get images by specific folder id
     * @param  int $folderId Folder Id
     * @return json
     */
    public function imagesByFolder($folderId)
    {
        $images = Files::imageOnly()->where('folder_id', $folderId)->get()->toArray();

        return $this->returnJson($images);
    }

    public function folders($id = null)
    {
        $folders = null;

        if (is_null($id)) {
            $folders = Folders::all()->toArray();
        } else {
            $folders = Folders::find($id)->toArray();
        }

        return $this->returnJson($folders);
    }

    /**
     * Get child folders by specific folder id
     * @param  int $folderId Folder Id
     * @return json
     */
    public function foldersByFolder($folderId)
    {
        $folders = Folders::where('folder_id', $folderId)->get()->toArray();

        return $this->returnJson($folders);
    }

    /**
     * Get all files and folders
     * @return json
     */
    public function filesAndFolders()
    {
        $files = Files::all()->toArray();

        $folders = Folders::all()->toArray();

        $result = array(
                'files'     => $files,
                'folders'   => $folders
            );

        return $this->returnJson($result);
    }

    /**
     * Get files and folders by specific folder id
     * @param  int $folderId Folder Id
     * @return json
     */
    public function filesAndFolderByFolder($folderId)
    {
        $files = Files::where('folder_id', $folderId)->get()->toArray();

        $folders = Folders::where('folder_id', $folderId)->get()->toArray();

        $result = array(
                'files'     => $files,
                'folders'   => $folders
            );

        return $this->returnJson($result);
    }

}
