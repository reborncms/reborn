<?php

namespace Module\Controller\Admin;

use Flash;
use Config;
use Module;
use Redirect;
use Reborn\Util\Uploader as Upload;

/**
 * Module Manager Admin Controller
 *
 * @package Reborn\Modules
 * @author Nyan Lynn Htut
 **/

class ModuleController extends \AdminController
{
    public function before()
    {
        $this->menu->activeParent('utilities');

        $this->template->style('modules.css', 'module');
    }

    /**
     * Default index action
     */
    public function index()
    {
        $modules = $this->modulePrepare();

        $this->template->title(t('module::module.title'))
                    ->set('system', $modules['system'])
                    ->set('plugged', $modules['plugged'])
                    ->set('news', $modules['news']);

        $drag = $this->template->partialRender('module::plugged');
        $news = $this->template->partialRender('module::news');
        $system = $this->template->partialRender('module::system');

        $this->template->set('drag_view', $drag);
        $this->template->set('news_view', $news);
        $this->template->set('system_view', $system);

        $this->template->view('lists');
    }

    /**
     * Install action for Module Manager
     *
     * @param string $name
     */
    public function install($name)
    {
        if (! $this->checkAction($name) ) {
            return $this->notFound();
        }

        if (Module::install($name)) {
            $msg = sprintf(t('module::module.install_success'), $name);
            Flash::success($msg);
        } else {
            $msg = sprintf(t('module::module.install_error'), $name);
            Flash::error($msg);
        }

        return Redirect::toAdmin('module');
    }

    /**
     * Uninstall action for Module Manager
     *
     * @param string $name
     */
    public function uninstall($name)
    {
        if (! $this->checkAction($name) ) {
            return $this->notFound();
        }

        if (Module::uninstall($name)) {
            $msg = sprintf(t('module::module.uninstall_success'), $name);
            Flash::success($msg);
        } else {
            $msg = sprintf(t('module::module.uninstall_error'), $name);
            Flash::error($msg);
        }

        return Redirect::toAdmin('module');
    }

    /**
     * Module Upgrade action for Module Manager
     *
     * @param string $name
     */
    public function upgrade($name)
    {
        if (Module::upgrade($name)) {
            $msg = sprintf(t('module::module.upgrade_success'), $name);
            Flash::success($msg);
        } else {
            $msg = sprintf(t('module::module.upgrade_error'), $name);
            Flash::error($msg);
        }

        return Redirect::toAdmin('module');
    }

    /**
     * Module Enable action for Module Manager
     *
     * @param string $name
     */
    public function enable($name)
    {
        if (! $this->checkAction($name) ) {
            return $this->notFound();
        }

        if (Module::enable($name)) {
            $msg = sprintf(t('module::module.enable_success'), $name);
            Flash::success($msg);
        } else {
            $msg = sprintf(t('module::module.enable_error'), $name);
            Flash::error($msg);
        }

        return Redirect::toAdmin('module');
    }

    /**
     * Module Disable action for Module Manager
     *
     * @param string $name
     */
    public function disable($name)
    {
        if (! $this->checkAction($name) ) {
            return $this->notFound();
        }

        if (Module::disable($name)) {
            $msg = sprintf(t('module::module.disable_success'), $name);
            Flash::success($msg);
        } else {
            $msg = sprintf(t('module::module.disable_error'), $name);
            Flash::error($msg);
        }

        return Redirect::toAdmin('module');
    }

    /**
     * Module Delete action for Module Manager
     *
     * @param string $name
     */
    public function delete($name)
    {
        if (! $this->checkAction($name) ) {
            return $this->notFound();
        }

        if (Module::has($name)) {
            $mod = Module::get($name);

            if ($mod->isInstalled()) {
                Module::uninstall($name);
            }

            if (\Dir::is($mod->path)) {
                if (\Dir::delete($mod->path)) {
                    $msg = sprintf(t('module::module.delete_success'), $name);
                    Flash::success($msg);
                } else {
                    $msg = sprintf(t('module::module.manually_remove'), $name);
                    Flash::error($msg);
                }
            }
        }

        return Redirect::toAdmin('module');
    }

    /**
     * Module Upload action for Module Manager
     *
     */
    public function upload()
    {
        $tmp_path = STORAGES.'tmp'.DS;

        $extract_path = MODULES;

        if (\Input::isPost()) {
            $config = array(
                'savePath' => $tmp_path,
                'createDir' => true,
                'allowedExt' => array('zip')
            );

            $v = \Validation::create(array('file' => \Input::file('fileselect')),
                                        array('file' => 'required'));

            if ($v->valid()) {
                // Start the Upload File
                Upload::initialize('fileselect', $config);

                if (Upload::isSuccess()) {
                    $data = Upload::upload('fileselect');
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

                    Flash::error($error);

                    return Redirect::toAdmin('module/upload');
                }

                try {
                    $zip_file = $tmp_path.$data[0]['savedName'];

                    $filename = str_replace('.zip', '', $data[0]['savedName']);

                    // create object
                    $zip = new \ZipArchive() ;

                    // open archive
                    if ($zip->open($zip_file) !== TRUE) {
                        Flash::error(sprintf(t('module::module.unzip_error'),$filename));
                        \File::delete($zip_file);

                        return Redirect::toAdmin('module/upload');
                    }

                    // extract contents to destination directory
                    $zip->extractTo($extract_path);

                    // close archive
                    $zip->close();

                    \File::delete($zip_file);
                    Flash::success(sprintf(t('module::module.upload_success'),$filename));

                    return Redirect::toAdmin('module/upload');
                } catch (\Exception $e) {
                    Flash::error($e);

                    return Redirect::toAdmin('module/upload');
                }
            } else {
                Flash::error(implode("\n\r", $v->getErrors()));
            }
        }

        $this->template->title(t('module::module.upload_title'))
                        ->view('upload');
    }

    /**
     * Update module 1.0 to 1.1.
     * Remove description column
     *
     * @return void
     **/
    public function update()
    {
        try {
            \Schema::table('modules', function ($table) {
                $table->dropColumn('description');
            });
        } catch (\Illuminate\Database\QueryException $e) {
        }
        \DB::table('modules')->where('name', 'module')->update(array('version' => '1.1'));

        return Redirect::module();
    }

    /**
     * Check Module is system module or not.
     *
     * @param  string  $name
     * @return boolean
     */
    protected function checkAction($name)
    {
        $systems = Config::get('app.module.system');

        if (in_array(strtolower($name), $systems)) {
            return false;
        }

        return true;
    }

    /**
     * Prepare module lists with type.
     *
     * @return array
     */
    protected function modulePrepare()
    {
        $m = array();
        $m['plugged'] = $m['news'] = $m['system'] = array();

        $sys = Config::get('app.module.system');

        $m['news'] = Module::findNews();

        $all = Module::getAll();

        $manager = $this->get('site_manager');

        // Remove site_manager if cms is single site
        if (!$manager->isMulti()) {
            unset($all['site_manager']);
        }

        foreach ($all as $k => $mod) {

            if (in_array($k, $sys)) {
                $mod->module_class = 'is-core-block';
                $m['system'][$k] = $mod;
            } else {
                $mod->module_class = 'is-plugged-block';
                $m['plugged'][$k] = $mod;
            }
        }

        return $m;
    }
}
