<?php

namespace Media\Controller\Admin;

use Media\Model\Files;
use Media\Model\Folders;
use Reborn\Fileupload\Uploader as Uploader;
use Input, Flash, Redirect, Config, Validation;

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
     * @return void
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
     * @author RebornCMS Development Team
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

        $this->template->fileData = $model;

        $this->template->allFolders = Folders::all();

        $this->template->title(t('m.title.fileEdit'))
                        ->setPartial('admin/form/edit');
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
            $matching = preg_match_all('/^(\w*)_(\d\d?)\.(\w*)$/', $name, $match);

            if ($matching) {
                $counter = ((int) $match[2][0])+1;
                $name = $match[1][0] . '_' . $counter . '.' . $match[3][0];
            } else {
                $exploded = explode('.', $name);
                $room = count($exploded) - 2;
                $exploded[$room] .= '_1';
                $name = implode('.', $exploded);
            }
        }

        return $name;

    }

} // END class FileController extends \AdminController
