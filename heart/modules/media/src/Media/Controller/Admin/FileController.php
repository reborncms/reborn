<?php

namespace Media\Controller\Admin;

use Media\Model\Files;
use Media\Model\Folders;
use Reborn\Fileupload\Uploader as Uploader;
use Input, Flash, Redirect, Config, Validation, File;

/**
 * Controller class for media files
 *
 * @package Media\Controller\Admin
 * @author RebrnCMS Development Team
 **/
class FileController extends \AdminController
{

    /**
     * File upload function
     *
     * @param int    $folderId
     * @param String $key      name if file input field
     *
     * @return String
     **/
    public function upload($folderId = 0, $key = 'files')
    {

        if (Input::isPost()) {
            $uploader = Uploader::initialize(
                    'files',
                    Config::get('media::media.upload_config')
                );

            $uploaded = $uploader->upload();

            $renamed = $this->solveDuplication($uploaded['savedName']);

            if ($uploaded['savedName'] != $renamed) {
                rename($uploaded['fullPath'], $uploaded['path'] . $renamed);
                $uploaded['savedName'] = $renamed;
            }

            if (isset($uploaded['error'])) {

                if ($this->request->isAjax()) {
                    return $this->json(array(
                            'status'    => 'fail',
                            'error'     => $uploaded['error']
                        ));
                }

                Flash::error(t('m.error.upload'));

            } else {

                $uploaded['folder_id'] = $folderId;

                $model = new Files;

                if ($saved = $model->saveFile($uploaded)) {

                    if ($this->request->isAjax()) {
                        return $this->json(array('status' => 'success'));
                    }

                    Flash::success(t('m.success.upload'));

                    return Redirect::module('media');

                }

                if ($this->requiest->isAjax()) {
                    return $this->json(array(
                            'status'    => 'fail',
                            'error'     => $uploaded['error']
                        ));
                }

                Flash::error(t('m.error.upload'));
            }
        }

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
        }

        $this->template->fileType = '.' . implode(',.', Config::get(
            'media::media.upload_config.allowedExt'));

        $this->template->title(t('media::media.title.upload'))
                        ->set('formName', 'upload')
                        ->set('folderId', $folderId)
                        ->setPartial('admin/form/upload');
    }

     /**
     * Can edit file data by using this method
     *
     * @param int $id File id to be edited
     *
     * @return String
     **/
    public function update($id = 0)
    {
        $model = Files::find($id);

        if (Input::isPost()) {

            if ($model->updateFile(Input::get('*'))) {

                Flash::success(t('m.success.fileUpdate'));

                return Redirect::module();
            } else {
                Flash::error(t('m.error.fileUpdate'));
            }

        }

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
        }

        $this->template->title(t('m.title.fileEdit'))
                        ->set('fileData', $model)
                        ->set('allFolders', Folders::all())
                        ->setPartial('admin/form/edit');
    }

    /**
     * Delete file or files
     *
     * @param mix $id Array or int
     *
     * @return String
     **/
    public function delete($id)
    {

        $ids = (array) $id;

        $files = Files::whereIn('id', $ids)->get();

        foreach ($files as $file) {

            $file->deleteFile();
        }

        Flash::success(t('media::media.success.fileDel'));

        return Redirect::module();

    }

    /**
     * Solve filename duplication problem
     *
     * @param String $filename
     *
     * @return String $name
     **/
    protected function solveDuplication($filename)
    {

        $name = $filename;

        while (Files::hasFile($name)) {

            $name = increasemental($name);
            
        }

        return $name;

    }

} // END class FileController extends \AdminController
