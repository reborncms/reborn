<?php

namespace Theme\Controller\Admin;

use \Theme\Model\Theme as Theme;
use Reborn\Util\Uploader as Upload;

class ThemeController extends \AdminController
{
    /**
     * Before function for ThemeController
     *
     * @return void
     **/
    public function before()
    {
        $this->menu->activeParent('appearance');
        $this->template->style('theme.css','theme');
    }

    /**
     * Display all usable themes along with currently activated theme
     *
     * @return void
     **/
    public function index()
    {
        $themes = Theme::all();
        $active = \Setting::get('public_theme');

        $this->template->title(\Translate::get('theme::theme.title'))
                    ->breadcrumb(\Translate::get('theme::theme.title'))
                    ->set('themes', $themes)
                    ->set('active', $active)
                    ->setPartial('admin/index');
    }

    /**
     * Activate a theme from usable themes
     *
     * @param  string $name
     * @return void
     **/
    public function activate($name)
    {
        if (!user_has_access('theme.activate')) return $this->notFound();

        $themes = Theme::all();

        if (array_key_exists($name, $themes)) {
            \Setting::set('public_theme', $name);
            \Flash::success(sprintf(\Translate::get('theme::theme.activate.success'), $themes[$name]['name']));
        } else {
            \Flash::error(\Translate::get('theme::theme.activate.error'));
        }

        return \Redirect::to(ADMIN_URL.'/theme');
    }

    /**
     * Delete a theme
     * You cannot delete currently activated theme
     *
     * @param  string $name
     * @return void
     **/
    public function delete($name)
    {
        if (!user_has_access('theme.delete')) return $this->notFound();

        $themes = Theme::all();

        if (array_key_exists($name,$themes)) {
            if (is_dir(THEMES.$name)) {
                $delete = \Dir::delete(THEMES.$name);

                if ($delete) {
                    \Flash::success(\Translate::get('theme::theme.delete.success'));
                } else {
                    \Flash::error(\Translate::get('theme::theme.delete.error'));
                }
            } else {
                \Flash::error(\Translate::get('theme::theme.delete.error'));
            }
        } else {
            \Flash::error(\Translate::get('theme::theme.delete.error'));
        }

        return \Redirect::to(ADMIN_URL.'/theme');
    }

    /**
     * Upload new theme with .zip format
     * Saved on temporary folder and then extracted to specific theme folder
     * Please change upload_max_filesize in php.ini for larger theme zip files
     *
     * @return void
     **/
    public function upload()
    {
        if (!user_has_access('theme.upload')) return $this->notFound();

        if (\Input::isPost()) {

            $tmp_path = STORAGES.'tmp'.DS;

            $uploadPath = \Input::get('upload_path');

            if ($uploadPath == 'main') {
                $extract_path = THEMES;
            } else {
                $extract_path = SHARED.'themes'.DS;
            }

            $config = array(
                'savePath' => $tmp_path,
                'createDir' => true,
                'allowedExt' => array('zip')
            );

            $v = \Validation::create(
                array('file' => \Input::file('file')),
                array('file' => 'required')
            );

            $e = new \Reborn\Form\ValidationError();

            if ($v->valid()) {

                if (Upload::isSuccess()) {
                    Upload::initialize('file', $config);

                    $data = Upload::upload('file');
                    $data[0]['status'] = 'success';
                } else {
                    $v = Upload::errors();
                    $data[0]['status'] = 'fail';
                    $error = '';
                    foreach ($v[0] as $k => $s) {
                        if (is_int($k)) {
                            $error .= $s.' ';
                        }
                    }
                    $data[0]['errors'] = $error;

                    \Flash::error($error);

                    return \Redirect::toAdmin('theme/upload');
                }

                try {
                    $zip_file = $tmp_path.$data[0]['savedName'];

                    $filename = str_replace('.zip', '', $data[0]['savedName']);

                    // create object
                    $zip = new \ZipArchive() ;

                    // open archive
                    if ($zip->open($zip_file) !== TRUE) {
                        \Flash::error(sprintf(t('theme::theme.unzip_error'),$filename));
                        \File::delete($zip_file);

                        return \Redirect::toAdmin('theme/upload');
                    }

                    // extract contents to destination directory
                    $zip->extractTo($extract_path);

                    // close archive
                    $zip->close();

                    \File::delete($zip_file);
                    \Flash::success(sprintf(t('theme::theme.upload.success'),$filename));

                    return \Redirect::toAdmin('theme');
                } catch (\Exception $e) {
                    \Flash::error($e);

                    return \Redirect::toAdmin('theme/upload');
                }
            } else {
                $e = $v->getErrors();
                \Flash::error(implode("\n\r", $e));
            }
        }

        $this->template->title(\Translate::get('theme::theme.title'))
                    ->breadcrumb(\Translate::get('theme::theme.title'))
                    ->setPartial('admin/upload');
    }
}
