<?php

namespace Media\Controller\Admin;

use Media\Model\Folders as Folders;
use Media\Model\Files as Files;
use Cache, Input, Flash;

/**
 * Admin Controller for Meda Module
 *
 * @package default
 * @author
 **/
class MediaController extends \AdminController
{

    /**
     * All folders queried object
     *
     * @var object
     **/
    public $allFoldes = null;

    /**
     * All Files queried object
     *
     * @var object
     **/
    public $allFiles = null;

    /**
     * Logged in user
     *
     * @var object
     **/
    public $user = null;

    /**
     * Before function for admin MediaController
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public function before()
    {
        $this->template->style(array('plugins.css', 'media.css'), 'media');
        $this->template->script(array('plugins.min.js', 'media.js'), 'media');

        $this->allFolders = Folders::all();
        $this->allFiles = Files::all();
        $this->user = \Auth::getUser();

        $this->template->jsValue(array(
                'hasFolder' => (0 == Folders::count()) ? false : true,
                'adminMedia'=> admin_url('media'),
            ));
    }

    /**
     * Entry point for admin MediaController
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public function index()
    {
        $this->explore(0);
    }

# # # # # # # # # # Files # # # # # # # # # #

     /**
     * Can edit file data by using this method
     *
     * @param int $id File id to be edited
     * @author RebornCMS Development Team
     **/
    public function editFile($id)
    {
        if (\Input::isPost()) {
            $validate = $this->validation();

            if ($validate->valid()) {

                if ($this->saveData(true, $id)) {

                    \Flash::success(\Translate::get('m.success.fileUpdate'));

                    return \Redirect::toAdmin('media');
                } else {
                    \Flash::error(\Translate::get('m.error.fileUpdate'));
                }

            } else {
                $this->template->error = $validate->getErrors();
                \Flash::error(\Translate::get('m.error.fileUpdate'));
            }
        }

        $this->checkAjax();

        $this->template->fileData = Files::find($id);

        $this->template->allFolders = Folders::all();

        $this->template->title(\Translate::get('m.title.fileEdit'))
                        ->setPartial('admin' . DS . 'form' . DS . 'edit');
    }

    /**
     * Deleting files
     *
     * @param int     $id
     * @param boolean $redirect
     *
     * @return void
     **/
    public function deleteFile($id, $redirect = true)
    {
        $ids = (array) $id;

        $files = Files::whereIn('id', $ids)->get();

        foreach ($files as $file) {
            $path = UPLOAD . date('Y', strtotime($file->created_at)).DS
                .date('m', strtotime($file->created_at)) . DS  . $file->filename;

            if (\File::is($path)) \File::delete($path);

            $file->delete();
        }

        if ($redirect) {
            if ($this->request->isAjax()) {
                return $this->json(array('status' => 'success'));
            }

            \Flash::success(t('media::media.success.fileDel'));

            return \Redirect::toAdmin('media');
        }

        return true;

    }

    /**
     * This method will explore folders
     *
     * @param int $id Folder it to be explore
     *
     * @return void
     **/
    public function explore($id = 0)
    {
        $pagination = with(new Folders)->pagination($id);

        $files = Files::with(array('folder', 'user'))->where('folder_id', '=', $id)->get();

        $folders = Folders::with(array('children', 'user'))->where('folder_id', '=', $id)->get();

        $current = Folders::find($id);

        if (is_null($current)) {
            $current = new \stdClass();

            $current->id = 0;
            $current->name = t('media;:media.lbl.none');
            $current->desc = 'Default folder of media module.';
            $current->user = \Reborn\Auth\Sentry\Eloquent\User::find(1);
        }

        if ($files->isEmpty() and $folders->isEmpty()) {
            $this->template->set('isEmpty', true);
        }

        $statusBar = $this->template
                        ->set('current', $current)
                        ->set('files', $files)
                        ->set('folders', $folders)
                        ->partialRender('admin/statusbar');

        $actionBar = $this->template->set('currentFolder', 0)
                        ->set('pagination', array_reverse($pagination))
                        ->set('current', $current)
                        ->set('selected', $id)
                        ->partialRender('admin/actionbar');

        $this->template->title(t('media::media.title.title'))
                        ->set('statusBar', $statusBar)
                        ->set('current', $current)
                        ->set('files', $files)
                        ->set('folders', $folders)
                        ->set('actionBar', $actionBar)
                        ->jsValue('currentFolder', (0 == $id) ? '' : $id)
                        ->setPartial('admin/index');
    }

    /**
     * This method will save data.
     *
     * @param String $saveFor Which for
     * @param int    $id      id of the file or folder to be edit
     *
     * @return boolean
     **/
    protected function saveData($file = false, $id = 0)
    {
        if ($file) {
            $data = Files::find($id);

            $data->name = \Input::get('name');
            $data->alt_text = \Input::get('alt_text');
            $data->description = \Input::get('description');
            $data->folder_id = \Input::get('folder_id');
            $data->user_id = $this->user->id;

            $data->name = duplication($data->name, $data->folder_id, true,
                \Input::get('originName'));

            return $data->save();

        } else {
            $data = (0 === $id) ? new Folders() : Folders::find($id);

            $data->name = \Input::get('name');
            $data->slug = \Input::get('name');
            $data->description = \Input::get('description');
            $data->folder_id = \Input::get('folder_id');
            $data->user_id = $this->user->id;
            $data->depth = 1;

            if (0 === $id) {
                $data->name = duplication($data->name, $data->folder_id);
            } else {
                $data->name = duplication($data->name, $data->folder_id, $file,
                    \Input::get('originName'));
            }

            $data->slug = strtolower(str_replace(' ', '-', $data->name));

            $data->depth = defineDepth($data->folder_id);

            return $data->save();

        }
    }

    /**
     * This method will check validation for forms
     *
     * @param String $validationFor which for ('folder', 'file')
     *
     * @return Object $validate Validation Object
     **/
    protected function validation($validateFor = 'folder')
    {
        switch ($validateFor) {
            case 'folder':
                $rule = array(
                    'name'      => 'required|maxLength:200',
                    'folder_id' => 'required|integer',
                    );
                break;

            case 'file':
                $rule = array(
                    'name'      => 'required|maxLength:200',
                    'folder_id' => 'required|integer',
                    );
                break;
        }

        $validate = new \Validation(\Input::get('*'), $rule);

        return $validate;
    }

    /**
     * Lazy method
     *
     * @return void
     **/
    private function checkAjax()
    {
        if ($this->request->isAjax()) {
            $this->template->partialOnly();
        }
    }

    /**
     * Inserting thumbnail images
     *
     * @param int $folderId
     *
     * @return void
     **/
    public function thumbnail($folderId = 0)
    {
        $images = Files::imageOnly()->where('folder_id', '=', $folderId)->get();
        $this->template->allFolders = Folders::all();

        $this->checkAjax();

        $this->template->title(t('meida::media.ext.thumbnail'))
                        ->style('plugins.css', 'media')
                        ->style('thumbnail.css', 'media')
                        ->script('plugins.js', 'media')
                        ->script('thumbnail.js', 'media')
                        ->set('images', $images)
                        ->setPartial('admin/plugin/thumbnail');

    }

    /**
     * Called by  wysiwyg editor
     *
     * @param int $folderId Parent Folder Id
     *
     * @return void
     **/
    public function wysiwyg($folderId = 0)
    {
        $images = Files::imageOnly()->where('folder_id', '=', $folderId)->get();

        $this->template->title(t('media::media.ext.thumbnail'))
                        ->set('images', $images)
                        ->partialOnly()
                        ->setPartial('wysiwyg'.DS.'wysiwyg');
    }

    public function search()
    {
        if (!\Input::isPost()) {
            return \Redirect::toAdmin('media');
        }

        $keyword = \Input::get('keyword');
        $files = Files::make()->contain('name', $keyword)->get();

        if ($files->isEmpty()) {
            $this->template->set('isEmpty', true);
        }

        $this->template->title(t('media::media.title.title'))
                        ->set('files', $files)
                        ->setPartial('admin/index');
    }

} // END class MediaController
