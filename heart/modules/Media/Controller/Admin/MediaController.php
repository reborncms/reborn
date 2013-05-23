<?php

namespace Media\Controller\Admin;

use Media\Model\MediaFolders as MFolders;
use Media\Model\MediaFiles as MFiles;
use Reborn\Util\Uploader as Uploader;

/**
 * Admin Controller for Meda Module
 *
 * @package default
 * @author
 **/
class MediaController extends \AdminController
{

    /**
     * Before function for admin MediaController
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public function before()
    {
        $this->template->style('style.css', 'media');
        $this->template->script('media.js', 'media');
    }

    /**
     * Entry point for admin MediaController
     *
     * @return void
     * @author RebornCMS Development Team
     **/
    public function index()
    {
        $files = MFiles::where('module', '=', 'media')
                        ->where('folder_id', '=', 0)->get();
        $folders = MFolders::where('folder_id', '=', '0')->get();
        $this->template->title('Media Module')
                        ->set('files', $files)
                        ->set('folders', $folders)
                        ->setPartial('admin/index');
    }

# # # # # # # # # # Public Function for Folder # # # # # # # # # #

    /**
     * This method can be used to create folder.
     *
     * @author RebornCMS Development Team
     * @param int $folderId Id of current folder
     **/
    public function createFolder($folder_id = 0)
    {
        $allFolders = MFolders::all();

        if (\Input::isPost()) {

            $validate = $this->validation();

            if ($validate->valid()) {
                if (\Security::CSRFvalid()) {
                    $data = $this->setData();

                    $depth = ($data['folder_id'] != 0) ? $this->defineDepth(
                        $data['folder_id']) : 0;

                    if (! $this->duplication($data['name'], $data['slug'],
                        $data['folder_id'])) {

                        $toSave = new MFolders();

                        $toSave->name = $data['name'];
                        $toSave->slug = $data['slug'];
                        $toSave->description = $data['description'];
                        $toSave->folder_id = $data['folder_id'];
                        $toSave->user_id = $data['user_id'];
                        $toSave->depth = ((int)$depth) + 1;

                        if ($toSave->save()) {
                            if ($this->request->isAjax()) {
                                return $this->returnJson(array('success' => true));
                            }
                            \Flash::success(\Translate::get(
                                'media::folder.flash.createSuc'));

                            return \Redirect::to('admin/media/');
                        } else {
                            if ($this->request->isAjax()) {
                                $inform = array(
                                    'success'   => false,
                                    'error' => \Translate::get(
                                        'media::folder.flash.createFail'),
                                    );

                                return $this->returnJson($inform);
                            }
                            \Flash::error(\Translate::get(
                                'media::folder.flash.createFail'));
                        }
                    } else {
                        if ($this->request->isAjax()) {
                            $inform = array(
                                'success' => false,
                                'error' => \Translate::get(
                                    'media::folder.flash.folderExist'),
                                );

                            return $this->returnJson($inform);
                        }
                        \Flash::error(\Translate::get(
                            'media::folder.flash.folderExist'));
                    }

                } else {
                    if ($this->request->isAjax()) {
                        $inform = array(
                            'success' => false,
                            'error' => \Translate::get('media::media.error.csrf'),
                            );

                        return $this->returnJson($inform);
                    }
                    \Flash::error(\Translate::get('media::media.error.csrf'));
                }
            } else {
                if ($this->request->isAjax()) {
                    $inform = array(
                        'success'   => false,
                        'error'     => $validate->getErrors(),
                        );

                    return $this->returnJson($inform);
                }
                $this->template->errors = $validate->getErrors();
            }
        }

        // For ajax
        if ($this->request->isAjax()) { $this->template->partialOnly(); }

        $this->template->title('Media &#124; New Folder')
                        ->set('allFolders', $allFolders)
                        ->set('folder_id', $folder_id)
                        ->setPartial('admin/folder/folder');
    }

    /**
     * This method can be used to edit folders
     *
     * @return void
     * @author RebornCMS Development Team
     * @param int $id Id of the folder you want to edit
     **/
    public function editFolder($id)
    {
        $allFolders = MFolders::all();

        $fData = MFolders::where('id', '=', $id)->first();

        if (\Input::isPost()) {
            $validate = $this->validation();

            if ($validate->valid()) {
                if (\Security::CSRFvalid()) {
                    $data = $this->setData();

                    $depth = ($data['folder_id'] != 0) ? $this->defineDepth(
                        $data['folder_id']) : 0;

                    if (! $this->duplication($data['name'], $data['slug'],
                        $data['folder_id'], \Input::get('orName'), \Input::get('orSlug'))) {

                        $updateData = MFolders::find(\Input::get('id'));

                        $updateData->name = $data['name'];
                        $updateData->slug = $data['slug'];
                        $updateData->description = $data['description'];
                        $updateData->folder_id = $data['folder_id'];
                        $updateData->user_id = $data['user_id'];
                        $updateData->depth = $depth + 1;

                        if ($updateData->save()) {
                            if ($this->request->isAjax()) {
                                return $this->returnJson(array('success' => true));
                            }
                            \Flash::success(\Translate::get(
                                'media::folder.flash.createSuc'));

                            return \Redirect::to('admin/media/');
                        } else {
                            if ($this->request->isAjax()) {
                                $inform = array(
                                    'success' => false,
                                    'error' => \Translate::get(
                                        'media::folder.flash.createFail'),
                                    );

                                return $this->returnJson($inform);
                            }
                            \Flash::error(\Translate::get(
                                'media::folder.flash.createFail'));
                        }
                    } else {
                        if ($this->request->isAjax()) {
                            $inform = array(
                                'success' => false,
                                'error' => \Translate::get(
                                    'media::folder.flash.folderExist'),
                                );

                            return $this->returnJson($inform);
                        }
                        \Flash::error(\Translate::get(
                            'media::folder.flash.folderExist'));
                    }
                } else {
                    if ($this->request->isAjax()) {
                        $inform = array(
                            'success' => false,
                            'error' => \Translate::get('media::media.error.csrf'),
                            );

                        return $this->returnJson($inform);
                    }
                    \Flash::error(\Translate::get('media::media.error.csrf'));
                }
            } else {
                if ($this->request->isAjax()) {
                    $inform = array(
                        'success'   => false,
                        'error'     => $validate->getErrors(),
                        );

                    return $this->returnJson($inform);
                }
                $this->template->errors = $validate->getErrors();
            }
        }

        if ($this->request->isAjax()) { $this->template->partialOnly(); }

        $this->template->title('Media &#124; Edit Folder')
                        ->set('fData', $fData)
                        ->set('allFolders', $allFolders)
                        ->setPartial('admin/folder/folder');
    }

    /**
     * Folders can be deleted by using this method
     *
     * @param int $id The id of the folder you wish to delete
     * @return void
     * @author RebornCMS Development Team
     **/
    public function deleteFolder($id)
    {
        if ($this->hasChild($id)) {
            $files = MFiles::where('folder_id', '=', $id)->get();
            foreach ($files as $file) {
                $this->deleteFile($file->id);
            }

            $folders = MFolders::where('folder_id', '=', $id)->get();
            foreach ($folders as $folder) {
                $this->deleteFolder($folder->id);
            }
        }
        $delFolder = MFolders::find($id);
        $delFolder->delete();
        if ($this->request->isAjax()) {
            return $this->returnJson(array('success' => true));
        }
        \Flash::success('media::folder.flash.sucDel');
    }

    /**
     * This method is for drag and drop
     *
     * @param int $id Id of dragged file
     * @param int $folder_id Id of dropped folder
     * @author RebornCMS Development Team
     **/
    public function changeDir($id, $folderId)
    {
        $updateData = MFiles::find($id);

        $updateData->folder_id = $folderId;
        $updateData->user_id = \Sentry::getUser()->id;

        if ($updateData->save()) {
            return $this->returnJson(array('success' => true));
        }
    }

    /**
     * This method can be explore folders. (Method name spelling is wrong)
     *
     * @param int $id Id of the folder you want to explore
     * @author RebornCMS Development Team
     **/
    public function explode($id)
    {
        $folders = MFolders::where('folder_id', '=', $id)->get();
        $files = MFiles::where('folder_id', '=', $id)->get();
        $currentFolder = MFolders::where('id', '=', $id)->first();
        $folderPagi = array_reverse($this->folderPagi($id));

        $this->template->title('Media')
                        ->set('folders', $folders)
                        ->set('files', $files)
                        ->set('folder_id', $id)
                        ->set('currentFolder', $currentFolder)
                        ->set('folderPagi', $folderPagi)
                        ->setPartial('admin/index');
    }

    # # # # # # # # # # Public Function for Files # # # # # # # # # #

    /**
     * Set feature image (eg.blog featured image)
     *
     * @return void
     * @param int $folderId
     * @author RebornCMS Development Team
     **/
    public function featureImage($folderId = 0)
    {
        # @TODO - Do more flexible
        $images = MFiles::where('folder_id', '=', $folderId)
                            ->where('module', '=', 'media')
                            ->whereIn('mime_type', array(
                                'image/jpeg', 'image/gif', 'image/png',
                                'image/tiff', 'image/bmp'))
                            ->get();

        $allFolders = MFolders::all();

        if ($this->request->isAjax()) {
            $this->template->partialOnly();
            $this->template->set('ajax', true);
        }

        $this->template->title('Media &124; Set Featured Image')
                        ->set('images', $images)
                        ->set('allFolders', $allFolders)
                        ->setPartial('admin'.DS.'outside'.DS.'featuredImage');
    }

    /*public function mediaUpload($folderId = 0, $usrOptions = array())
    {
        $options = array(
            'dimension'     => false,
            'align'         => false,
            'altText'       => false,
            'onClick'       => true,
            'multiSelect'   => true,
            'btnName'       => 'Insert',
            'preview'       => false,
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
    }*/

    /**
     * This is the real upload function.
     * This function was created to solve the problem, parsing array parameter.
     *
     * @return void
     * @param String $key File input field name
     * @param id $folderId The folder, files to be uploaded to
     * @param array $config Configuration for upload
     * @param String $module
     * @author RebornCMS Development Team
     **/
    public function realUpload($key, $folderId, $config, $module)
    {
        Uploader::initialize($key, $config);

        if (Uploader::isSuccess()) {
            $uploaded = Uploader::upload('files');

            foreach ($uploaded as $save) {
                preg_match_all('/^(image)\/(\w*)$/', $save['fileType'], $match);

                $size = array('width' => 0, 'height' => 0);
                if (! empty($match[1])) {
                    $size = $this->getImageSize($config['savePath'] . $save['savedName']);
                }

                $data = new MFiles();

                $data->name = $save['baseName'];
                $data->alt_text = $save['baseName'];
                $data->folder_id = $folderId;
                $data->user_id = \Sentry::getUser()->id;
                $data->filename = $save['savedName'];
                $data->filesize = $save['fileSize'];
                $data->extension = $save['extension'];
                $data->mime_type = $save['fileType'];
                $data->width = $size['width'];
                $data->height = $size['height'];
                $data->module = $module;

                if ($data->save()) {
                    $folderName = MFolders::where('id', '=', $data->folder_id)
                                            ->first();
                    $inform = array(
                        'success'   => true,
                        'id'    => $data->id,
                        'name'  => $data->name,
                        'desctiption'   => $data->description,
                        'alt_text'  => $data->alt_text,
                        'folder_id' => $data->folder_id,
                        'folder_name'   => $folderName['name'],
                        'user_id'   => $data->user_id,
                        );

                    return $inform;
                } # @TODO - check db error
            }
        } else {
            $inform = array(
                'success'   => false,
                'error'     => Uploader::errors(),
                );

            return $inform;
        }

    }

    /**
     * Upload Function
     *
     * @return void
     * @param int $folderId Current folder id
     * @param String $key File input field name
     * @param mix $userConfig Upload configuration ('default', 'other', 'ck' or defined array)
     * @author RebornCMS Development Team
     **/
    public function upload($folderId = 0, $key = 'files', $userConfig = 'other',
        $module = 'media')
    {
        if (\Input::isPost()) {

            \Config::load('media');

            $config = array(
                'savePath'  => \Config::get('media::media.upload_path'),
                'rename'    => \Config::get('media::media.file_rename'),
                'createDir' => \Config::get('media::media.create_dir'),
                'encryptFileName'   => \Config::get('media::media.encrypt'),
                );

            if (!is_array($userConfig)) {
                if ($userConfig == 'default') {
                    array_unshift($config, \Config::get('media::media.allow.default'));
                } elseif ($userConfig == 'ck') {
                    array_unshift($config, \Config::get('media::media.allow.ck'));
                } else {
                    array_unshift($config, \Config::get('media::media.allow.other'));
                }
            } else {
                array_replace_recursive($config, $userConfig);
            }

            $uploaded = $this->realUpload($key, $folderId, $config, $module);

            if ($this->request->isAjax()) {
                return $this->returnJson($uploaded);
            }

            if ($uploaded['success']) {
                \Flash::success(\Translate::get('media::file.flash.sucUpload'));
            } else {
                foreach ($uploaded['error'] as $error) {
                    \Flash::error('The file ' . $error['errorAt'] .
                        ' cannot be uploaded!');
                }

                return \Redirect::toAdmin('media/upload/');
            }
        }

        $allFolders = MFolders::all();
        $maxUploadableSize = Uploader::getMaxFilesize();

        if ($userConfig == 'ck') {
            $header = $this->template->partialRender('admin/outside/header');
            $footer = $this->template->partialRender('admin/outside/footer');
            $btnsBar = $this->template->partialRender('admin/outside/btns');
            $this->template->title('Media &#124; Upload Files')
                            ->partialOnly()
                            ->set('folder_id', $folderId)
                            ->set('allFolders', $allFolders)
                            ->set('btnsBar', $btnsBar)
                            ->set('header', $header)
                            ->setPartial('admin/file/upload');
        }

        if ($this->request->isAjax()) { $this->template->partialOnly(); }

        $this->template->title('Media &#124; Upload Files')
                        ->set('folder_id', $folderId)
                        ->set('allFolders', $allFolders)
                        ->set('maxUploadableSize', $maxUploadableSize)
                        ->setPartial('admin/file/upload');

    }

    /**
     * Can edit file data by using this method
     *
     * @param int $id File id to be edited
     * @author RebornCMS Development Team
     **/
    public function editFile($id)
    {
        $allFolders = MFolders::all();

        $fileData = MFiles::where('id', '=', $id)->first();

        if (\Input::isPost()) {
            $rules = array(
                    'name'      => 'required',
                    'alt_text'  => 'required',
                    'folder_id' => 'required|numeric',
                );

            $postData = array(
                    'name'      => \Input::get('name'),
                    'alt_text'  => \Input::get('alt_text'),
                    'description'   => \Input::get('description'),
                    'folder_id' => (\Input::get('folder_id')) ? \Input::get('folder_id') : 0,
                    'user_id' => \Sentry::getUser()->id,
                );

            $val = new \Validation($postData, $rules);

            if ($val->valid()) {
                $updateData = MFiles::find(\Input::get('id'));

                $updateData->name = $postData['name'];
                $updateData->alt_text = $postData['alt_text'];
                $updateData->description = $postData['description'];
                $updateData->folder_id = $postData['folder_id'];
                $updateData->user_id = $postData['user_id'];

                if ($updateData->save()) {
                    if ($this->request->isAjax()) {
                        return $this->returnJson(array('success' => true));
                    }
                    \Flash::success('File is successfully updated');

                    return \Redirect::to('admin/media/');
                } else {
                    if ($this->request->isAjax()) {
                        $inform = array(
                            'success'   => false,
                            'error'     => 'File cannot be updated.',
                            );

                        return $this->returnJson($inform);
                    }
                    \Flash::error('File cannot be updated.');

                    return \Redirect::to('admin/media/');
                }
            } else {
                if ($this->request->isAjax()) {
                    $inform = array(
                        'success'   => false,
                        'error'     => $val->getErrors(),
                        );

                    return $this->returnJson($inform);
                }
                foreach ($val->getErrors() as $error) {
                    \Flash::error($error);
                }

                return \Response::to('admin/media/');
            }

        }

        if ($this->request->isAjax()) { $this->template->partialOnly(); }

        $this->template->title('Media &#124; File Edit')
                        ->set('allFolders', $allFolders)
                        ->set('fileData', $fileData)
                        ->setPartial('admin/file/edit');
    }

    /**
     * Used to delete file
     *
     * @return void
     * @param int $id File id to be deleted
     * @author RebornCMS Development Team
     **/
    public function deleteFile($id)
    {
        $deleteFile = MFiles::find($id);

        $fileDir = UPLOAD.date('Y', strtotime($deleteFile->created_at)).DS.date(
            'm', strtotime($deleteFile->created_at)).DS.$deleteFile->filename;

        \File::delete($fileDir);

        if ($deleteFile->delete()) {
            if ($this->request->isAjax()) {
                return $this->returnJson(array('success' => true));
            }
            \Flash::success('media::file.flash.sucDel');
        }
    }

    /**
     * This method serve to show file data
     *
     * @param int $id Id of the file you want to view
     * @author RebornCMS Development Team
     **/
    public function showFileData($id)
    {
        // @TODO - Show folder tree

        $fileData = MFiles::with(array('folder', 'user'))->where('id', '=', $id)->first();

        if ($this->request->isAjax()) { $this->template->partialOnly(); }

        $this->template->title('Media &#124; ' . $fileData->name)
                        ->set('fileData', $fileData)
                        ->setPartial('admin/file/show');
    }

    # # # # # # # # # # # Public function for WYSIWYG # # # # # # # # # #

    /**
     * This method is for WYSIWYG Editor
     *
     * @author RebornCMS Development Team
     **/
    public function rbCK()
    {
        $args = func_get_args();

        $folder_id = (empty($args)) ? 0 : $args[0];

        $images = MFiles::where('width', '!=', 0)
                            ->where('height', '!=', 0)
                            ->where('folder_id', '=', $folder_id)
                            ->get();

        $allFolders = MFolders::all();

        if (! empty($args)) {
            $this->template->set('current', $args[0]);
        }

        $header = $this->template->partialRender('admin/outside/header');
        $footer = $this->template->partialRender('admin/outside/footer');
        $btnsBar = $this->template->partialRender('admin/outside/btns');

        $this->template->title('Inserting Image')
                        ->partialOnly()
                        ->set('images', $images)
                        ->set('allFolders', $allFolders)
                        ->set('header', $header)
                        ->set('footer', $footer)
                        ->set('btnsBar', $btnsBar)
                        ->setPartial('admin/outside/ck');
    }

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

        $this->template->title('Media &#124; Upload Files')
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
     * Define folder depth
     *
     * @return int
     * @param int $folderId
     * @author RebornCMS Development Team
     **/
    private function defineDepth($folderId)
    {
        $result = MFolders::where('id', '=', $folderId)->first();

        return $result['depth'];
    }

    /**
     * Validation function
     *
     * @return Object $val
     * @param Strinf $fof File or folder ('file', 'folder')
     * @author RebornCMS Development Team
     **/
    private function validation($fof = 'folder')
    {
        $rule = array();

        if ($fof == 'folder') {
            $rule = array(
                    'name'      => 'required|maxLength:200',
                    'slug'      => 'required|alphaDashDot',
                    'folder_id' => 'required|integer',
                );
        }

        if ($fof == 'file') {
            $rule = array(
                    'name'      => 'required|maxLength:200',
                    'folder_id' => 'required|integer',
                );
        }

        $val = new \Validation(\Input::get('*'), $rule);

        return $val;
    }

    /**
     * This method will check folder name is exist or not
     *
     * @param String $name Folder name
     * @param int $folderId Parent folder's id
     * @return boolean
     * @author RebornCMS Development Team
     **/
    private function duplication($name, $slug, $folderId, $exceptName = '',
        $exceptSlug = '')
    {
        if ($exceptName == '' or $exceptSlug == '') {
            $slugExist = MFolders::where('slug', '=', $slug)->first();
            $nameExist = MFolders::where('name', '=', $name)
                            ->where('folder_id', '=', $folderId)
                            ->first();

            return (isset($slugExist->id) or isset($nameExist->id)) ? true : false;
        } else {
            $slugExist = MFolders::where('slug', '=', $slug)
                                    ->where('slug', '!=', $exceptSlug)
                                    ->first();
            $nameExist = MFolders::where('name', '=', $name)
                                    ->where('folder_id', '=', $folderId)
                                    ->where('name', '!=', $exceptName)
                                    ->first();

            return (isset($slugExist->id) or isset($nameExist->id)) ? true : false;
        }
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

    /**
     * Check parent folder
     *
     * @param int $id Current folder id
     * @author RebornCMS Development Team
     **/
    private function hasParent($id)
    {
        $exist = MFolders::where('id', '=', $id)->first();

        return ($exist->folder_id != 0) ? true : false;
    }

    /**
     * Check child folder
     *
     * @param int $id Folder id
     * @author RebornCMS Development Team
     **/
    private function hasChild($id)
    {
        $folderExist = MFolders::where('folder_id', '=', $id)->first();
        $fileExist = MFiles::where('folder_id', '=', $id)->first();

        return (isset($folderExist->id) OR isset($fileExist)) ? true : false;
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

} // END class MediaController
