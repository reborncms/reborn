<?php

namespace Media\Controller\Admin;

use Media\Model\Folders as Folders;
use Media\Model\Files as Files;
use Reborn\Fileupload\Uploader as Uploader;

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
        $this->template->style('plugins.css', 'media');
        $this->template->style('media.css', 'media');
        $this->template->script('plugins.js', 'media');
        $this->template->script('media.js', 'media');

        $this->allFolders = Folders::all();
        $this->allFiles = Files::all();
        $this->user = \Sentry::getUser();

        $this->template->set('allFolders', $this->allFolders);
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

# # # # # # # # # # Folders # # # # # # # # # #

    /**
     * This method will create folders
     *
     * @param int $folderId
     * @return void
     **/
    public function createFolder($folderId = 0)
    {

        if (\Input::isPost()) {

            $validate = $this->validation();

            if ($validate->valid()) {
                if ($this->saveData()) {
                    return \Redirect::toAdmin('media');
                } else {
                    \Flash::error(\Translate::get('m.error.create'));
                }
            } else {
                $this->template->error = $validate->getErrors();
                \Flash::error(\Translate::get('m.error.create'));
            }
        }

        $this->checkAjax();

        $this->template->title(\Translate::get('m.title.create'))
                        ->set('parentId', $folderId)
                        ->setPartial('admin'.DS.'form'.DS.'folder');
    }

    /**
     * Folders can be edited by using this method.
     *
     * @param int $id Id of the folder you want to edit
     *
     * @return void
     **/
    public function editFolder ($id)
    {
        if (\Input::isPost()) {

            $validate = $this->validation();

            if ($validate->valid()) {

                if ($this->saveData(false, $id)) {
                    \Flash::success(\Translate::get('m.success.folderUpdate'));
                    return \Redirect::toAdmin('media');
                } else {
                    \Flash::error(\Translate::get('m.error.folderUpdate'));
                }

            } else {
                $this->template->error = $validate->getErrors();
                \Flash::error(\Translate::get('m.error.folderUpdate'));
            }
        }

        $folderData = Folders::where('id', '=', $id)->first();

        $this->checkAjax();

        $this->template->title(\Translate::get('files.editFolder'))
                        ->set('folderData', $folderData)
                        ->setPartial('admin'.DS.'form'.DS.'folder');
    }

    /**
     * Deleting folder or folders
     *
     * @param int $id Id of folder to be delete
     *
     * @return void
     **/
    public function deleteFolder($id)
    {
        $result = with(new Folders)->folderTreeIds($id);

        $files = Files::whereIn('folder_id', $result)->get(array('id'))->toArray();

        if (! empty($files)) {
            $this->deleteFile(array_pluck($files, 'id'), false);
        }

        Folders::destroy($result);

        if ($this->request->isAjax()) {
            return $this->json(array('status' => 'success'));
        }

        \Flash::success(t('media::media.success.folderDel'));

        return \Redirect::toAdmin('media');
    }

# # # # # # # # # # Files # # # # # # # # # #

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

        if (\Input::isPost()) {

            $config = array(
                'encName'   => true,
                'path'      => UPLOAD . date('Y') . DS . date('m') . DS,
                'prefix'    => 'rb_',
                'maxFileSize'   => Uploader::maxUploadableFileSize(),
                'createDir' => true,
                'rename'    => true,
                'dirChmod'      => 0777,
                'recursive'     => true,
                'allowedExt'    => array(
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'txt', 'rtf', 'doc', 'docx',
                    'xls', 'xlsx', 'pdf', 'zip', 'tar', 'rar', 'mp3', 'wav', 'wma',
                    ),
                );

            $fileObj = Uploader::fileUpload();

            $fileObj->setConfig($config);
            $fileObj->uploadInit();
            $errors = $fileObj->getErrors();

            if (empty($errors)) {

                if ($fileObj->upload()) {
                    $fileInfo = $fileObj->getFileInfo();

                    $data = new Files();

                    $returnData['name'] = $data->name = $fileInfo['originBaseName'];
                    $data->alt_text = $fileInfo['originBaseName'];
                    $data->description = null;
                    $data->folder_id = $folderId;
                    $data->user_id = $this->user->id;
                    $data->filename = $fileInfo['savedName'];
                    $data->filesize = $fileInfo['fileSize'];
                    $data->extension = $fileInfo['extension'];
                    $data->mime_type = $fileInfo['mimeType'];

                    $dimension = getImgDimension($config['path']
                        .$data->filename, $data->mime_type);

                    $data->width = $dimension['width'];
                    $data->height = $dimension['height'];

                    if ($data->save()) {
                        if ($this->request->isAjax()) {
                            return $this->json(array('status' => 'success'));
                        }

                        \Flash::success(\Translate::get('m.success.upload'));

                        return \Redirect::toAdmin('media');
                    } else {
                        if ($this->request->isAjax()) {
                            return $this->json(array('status' => 'fail'));
                        }

                        \Flash::error(\Translate::get('m.error.upload'));

                        return \Redirect::toAdmin('media');
                    }
                }
            } else {
                $errors = $fileObj->getErrors();
            }

            return \Redirect::toAdmin('media');
        }

        $this->checkAjax();

        $this->template->title(t('media::media.title.upload'))
                        ->set('folderId', $folderId)
                        ->setPartial('admin'.DS.'form'.DS.'upload');
    }

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
     * @param int $id
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
    public function explore($id)
    {
        $pagination = with(new Folders)->pagination($id);

        $files = Files::with(array('folder', 'user'))->where('folder_id', '=', $id)->get();

        $folders = Folders::with(array('folder', 'user'))->where('folder_id', '=', $id)->get();

        $current = Folders::find($id);

        if (is_null($current)) {
            $current = new \stdClass();

            $current->id = 0;
            $current->name = t('media;:media.lbl.none');
            $current->desc = 'Default folder of media module.';
            $current->user = \User\Model\User::find(1);
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
                        ->setPartial('admin/index');
    }

    /**
     * This method will save data.
     *
     * @param String $saveFor Which for
     * @param int $id id of the file or folder to be edit
     *
     * @return boolean
     **/
    protected function saveData ($file = false, $id = 0)
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
    protected function validation ($validateFor = 'folder')
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
    public function thumbnail($folderId = 0, $target = null)
    {
         $images = Files::where('folder_id', '=', $folderId)
                            ->whereIn('mime_type', array(
                                'image/jpeg', 'image/gif', 'image/png',
                                'image/tiff', 'image/bmp'))
                            ->get();

        if ($this->request->isAjax()) {

            $this->template->partialOnly();
            $thumbMeta = $this->template
                            ->partialRender('admin'.DS.'plugin'.DS.'thumbmeta');
            $this->template->set('thumbMeta', $thumbMeta);

        } elseif ('wysiwyg' == $target) {

            $this->template->partialOnly();
            $wysiwyMeta = $this->template
                            ->partialRender('admin'.DS.'plugin'.DS.'wysiwygmeta');
            $this->template->set('wysiwygMeta', $wysiwyMeta);

        } else {
            $this->template->script('setthumbnail.js', 'media', 'footer');
            $this->template->set('ajax', false);
        }
        
        $upload = $this->template->partialRender('admin'.DS.'form'.DS.'upload');

        //$option = $this->template->partialRender('admin'.DS.'plugin'.DS.'option');

        $this->template->title(t('media::media.ext.thumbnail'))
                        ->set('images', $images)
                        ->set('upload', $upload)
                        ->setPartial('admin'.DS.'plugin'.DS.'thumbnail');
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


    # # # # # # # # # # # Public function for WYSIWYG # # # # # # # # # #

    /**
     * Another function for WYSIWYG editor
     *
     * @author RebornCMS Development Team
     **/
    public function ckLink()
    {
        $header = $this->template->partialRender('admin/outside/header');
        $footer = $this->template->partialRender('admin/outside/footer');
        $btnsBar = $this->template->partialRender('admin/outside/btns');

        $this->template->title(\Translate::get('m.title.upload'))
                        ->partialOnly()
                        ->set('btnsBar', $btnsBar)
                        ->set('header', $header)
                        ->set('footer', $footer)
                        ->setPartial('admin/outside/cklink');
    }

    # # # # # # # # # # Private Functions for folder # # # # # # # # # #

    /**
     * Prepare input data
     *
     * @return array $data
     * @author RebornCMS Development Team
     **/
    private function setData()
    {
        $data = array(
            'name'  => \Input::get('name'),
            'slug'  => \Input::get('slug'),
            'description'   => \Input::get('description'),
            'folder_id' => (\Input::get('folder_id')) ? \Input::get('folder_id') : 0,
            'user_id'   => \Sentry::getUser()->id,
            );

        return $data;
    }

    /**
     * Breadcrumb for media module
     *
     * @param int $id Current folder id
     * @author RebornCMS Development Team
     **/
    private function folderPagi($id)
    {
        $theId = $id;
        $i = 0;
        $folders = array();

        while ($this->hasParent($theId)) {
            $folder = MFolders::where('id', '=', $theId)->first();
            $folders[$i] = MFolders::where('id', '=', $folder->folder_id)->first();
            $theId = $folders[$i]->id;
            $i++;
        }

        return $folders;
    }


    # # # # # # # # # # Private functions for files # # # # # # # # # #

    /**
     * Getting image width and height
     *
     * @param String $file File name with directory
     * @return array $size Width and height of expected img
     * @author RebornCMS Development Team
     **/
    private function getImageSize($file = null)
    {
        if(file_exists($file))
        {
            $data = @getimagesize($file);
            $size = array('width' => $data[0], 'height' => $data[1]);

            return $size;
        }
    }

    public function mediaManager($folderId = 0, $usrOptions = array())
    {
        $options = array(
            'dimension'     => true,
            'align'         => true,
            'altText'       => true,
            'onClick'       => true,
            'btnName'       => 'Insert',
            'preview'       => true,
            'moduleName'    => 'media',
            );

        if (! empty($usrOptions)) { array_replace_recursive($options, $usrOptions); }

        $files = MFiles::where('folder_id', '=', $folderId)
                        ->where('width', '!=', 0)
                        ->get();
        $allFolders = MFolders::all();

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
            $this->template->set('ajax', true);
        }

        $this->template->title('Media &#124; Edit Folder')
                        ->set('options', $options)
                        ->set('files', $files)
                        ->set('allFolders', $allFolders)
                        ->setPartial('admin/outside/upload');
    }

    public function testing()
    {
        dump(Files::active(), true);
    }

} // END class MediaController
