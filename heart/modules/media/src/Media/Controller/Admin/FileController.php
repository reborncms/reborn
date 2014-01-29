<?php 

namespace Media\Controller\Admin;

use Media\Model\Files;
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
     * @param int $folderId
     * @param String $key name if file input field
     *
     * @return void
     **/
    public function upload ($folderId = 0, $key = 'files')
    {

        if (Input::isPost()) {

            $fileObj = Uploader::fileUpload();

            $fileObj->setConfig(Config::get('media::media.upload_config'));
            $fileObj->uploadInit();
            $errors = $fileObj->getErrors();

            if (empty($errors)) {

                if ($fileObj->upload()) {
                    $fileInfo = $fileObj->getFileInfo();

                    $fileInfo['folder_id'] = $folderId;

                    $path = Config::get('media::media.upload_config.path');

                    $dimension = getImgDimension($path.$fileInfo['savedName'],
                        $fileInfo['mimeType']);

                    $fileInfo['width'] = $dimension['width'];
                    $fileInfo['height'] = $dimension['height'];

                    $model = new Files;

                    if ($saved = $model->saveFile($fileInfo)) {
                        if ($this->request->isAjax()) {
                            return $this->json(array('status' => 'success'));
                        }

                        Flash::success(t('m.success.upload'));

                        return Redirect::media();
                    } else {
                        if ($this->request->isAjax()) {
                            return $this->json(array('status' => 'fail'));
                        }

                        Flash::error(t('m.error.upload'));

                        return Redirect::module();
                    }
                }
            } else {
                $errors = $fileObj->getErrors();
            }

            return \Redirect::module();
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
            $validate = $this->validation();

            if ($validate->valid()) {

                if ($model->updateFile(Input::get('*'))) {

                    Flash::success(t('m.success.fileUpdate'));

                    return Redirect::module();
                } else {
                    Flash::error(t('m.error.fileUpdate'));
                }

            } else {
                $this->template->error = $validate->getErrors();
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
     * This method will check validation for forms
     *
     * @param String $validationFor which for ('folder', 'file')
     *
     * @return Object $validate Validation Object
     **/
    protected function validation ()
    {

        return new Validation(Input::get('*'), array(
                'name'      => 'required|maxLangth:200',
                'folder_id' => 'required|integer',
            ));
    }

} // END class FileController extends \AdminController