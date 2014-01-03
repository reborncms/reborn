<?php 

use Flash, Input, Translate, Redirect;

/**
 * Folder controller
 *
 * @package Media\Controller\Admin
 * @author RebornCMS Development Team
 **/
class FolderController extends \AdminController
{

	/**
	 * Before function for Folder controller class
	 *
	 * @return void
	 **/
	public function before()
	{



	}

	/**
     * This method will create folders
     *
     * @param int $folderId
     * @return void
     **/
    public function createFolder($folderId = 0)
    {
        if (Input::isPost()) {

            $validate = $this->validation();

            if ($validate->valid()) {
                if ($this->saveData()) {
                    if ($this->request->isAjax()) {
                        return $this->returnJson(array('status' => 'success'));
                    }

                    Flash::success(t('media::success.create'));

                    return Redirect::toAdmin('media');
                } else {
                    Flash::error(Translate::get('m.error.create'));
                }
            } else {
                $this->template->error = $validate->getErrors();
                Flash::error(Translate::get('m.error.create'));
            }
        }

        $this->checkAjax();

        $this->template->title(Translate::get('m.title.create'))
                        ->set('parentId', $folderId)
                        ->setPartial('admin'.DS.'form'.DS.'folder');
    }

} // END class FolderController extends \AdminController
