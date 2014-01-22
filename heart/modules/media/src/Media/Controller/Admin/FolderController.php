<?php 

namespace Media\Controller\Admin;

use Media\Model\Folders;
use Media\Model\Files;
use Flash, Input, Event, Redirect, Validation;

/**
 * Folder controller
 *
 * @package Media\Controller\Admin
 * @author RebornCMS Development Team
 **/
class FolderController extends \AdminController
{

	/**
     * This method will create folders
     *
     * @param int $folderId
     * @return String
     **/
    public function create($folderId = 0)
    {
        if (Input::isPost()) {

            $validate = $this->validation();

            if ($validate->valid()) {
                $folder = new Folders;

                if ($saved = $folder->createFolder(Input::get('*'))) {
                    Event::call('media.folder.create', array($saved));

                    if ($this->request->isAjax()) {
                        return $this->returnJson(array('status' => 'success'));
                    }

                    Flash::success(t('media::success.create'));

                    return Redirect::toAdmin('media');
                } else {
                    Flash::error(t('m.error.create'));
                }
            } else {
                $this->template->error = $validate->getErrors();
                Flash::error(t('m.error.create'));
            }
        }

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
        }

        $this->template->title(t('m.title.create'))
                        ->set('parentId', $folderId)
                        ->set('folders', Folders::all())
                        ->view('admin/form/folder');
    }

    /**
     * Folders can be edited by using this method.
     *
     * @param int $id Id of the folder you want to edit
     *
     * @return String
     **/
    public function update ($id)
    {
        $folder = Folders::find($id);

        if (Input::isPost()) {

            $validate = $this->validation();

            if ($validate->valid()) {

                if ($saved = $folder->updateFolder(Input::get('*'))) {
                    Event::call('omi.session.update', array($saved));

                    if ($this->request->isAjax()) {
                        return $this->returnJson(array('status' => 'success'));
                    }

                    Flash::success(t('m.success.folderUpdate'));

                    return Redirect::module();
                } else {
                    Flash::error(t('m.error.folderUpdate'));
                }

            } else {
                $this->template->error = $validate->getErrors();
                Flash::error(t('m.error.folderUpdate'));
            }
        }

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
        }

        $this->template->title(t('files.editFolder'))
                        ->set('folderData', $folder)
                        ->set('folders', Folders::all())
                        ->view('admin/form/folder');
    }

    /**
     * Deleting folder or folders
     *
     * @param int $id Id of folder to be delete
     *
     * @return void
     **/
    public function delete ($id)
    {
        $result = with(new Folders)->folderTreeIds($id);

        $files = Files::whereIn('folder_id', $result)->get(array('id'))->lists('id');

        if (! empty($files)) {
            Files::destroy($files);
        }

        Folders::destroy($result);

        if ($this->request->isAjax()) {
            return $this->json(array('status' => 'success'));
        }

        Flash::success(t('media::media.success.folderDel'));

        return Redirect::module();
    }

    /**
     * This method will check validation for forms
     *
     * @return Reborn\Form\Validation
     **/
    protected function validation ()
    {
        return new Validation(Input::get('*'), array(
                'name'      => 'required|maxLength:200',
                'folder_id' => 'required|integer',
            ));
    }

} // END class FolderController extends \AdminController
