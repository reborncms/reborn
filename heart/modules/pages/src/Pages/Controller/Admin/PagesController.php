<?php

namespace Pages\Controller\Admin;

use Pages\Model\Pages;
use Reborn\MVC\View\Theme as Theme;
use Pages\Lib\Helper;

class PagesController extends \AdminController
{
    public function before()
    {
        $this->menu->activeParent('content');

        $this->template->style('pages.css','pages');
        $this->template->script('pages.js','pages');

    }

    /**
     * Page Index
     *
     * @return void
     **/
    public function index()
    {
        $all = Pages::pageStructure();

        if (\Setting::get('default_module') == 'pages') {

            $home_setting = \Setting::get('home_page');

            $home_page = Pages::where('uri', $home_setting)->first(array('id', 'title', 'slug', 'uri'));

            $this->template->set('home_page', $home_page);

        }

        $this->template->title('Manage Your Pages')
                    ->set('pages', $all)
                    ->setPartial('admin/index')
                    ->script(array(
                            'plugins/jquery.ui.touch-punch.min.js',
                            'plugins/jquery.mjs.nestedSortable.js'
                    ));
    }

    /**
     * Page Create
     *
     * @return void
     **/
    public function create()
    {
        if (!user_has_access('pages.create')) {
                return $this->notFound();
        }

        $page = new Pages;

        if (\Input::isPost()) {

            if (\Input::get('id') != '') {
                $page = self::setValues('edit', \Input::get('id'));
            } else {
                $page = self::setValues('create');
            }

            if ($page->save()) {

                \Event::call('reborn.page.create');
                \Flash::success(t('pages::pages.messages.success.add'));

                return \Redirect::to(adminUrl('pages'));
            }
        }
        self::formElements();
        $this->template->title('Create Page')
                    ->set('method','create')
                    ->set('page', $page)
                    ->setPartial('admin/form');
    }

    /**
     * Page Edit
     *
     * @return void
     **/
    public function edit($id = null)
    {

        if (!user_has_access('pages.create')) {
                return $this->notFound();
        }

        $page = Pages::find($id);

        if (\Input::isPost()) {

            //get parent id
            $page = self::setValues('edit', \Input::get('id'));

            if ($page->save()) {

                \Flash::success(t('pages::pages.messages.success.edit'));

                return \Redirect::to(adminUrl('pages'));

            }
        }

        self::formElements();
        $this->template->title('Edit Page')
                    ->set('method','edit')
                    ->set('page', $page)
                    ->setPartial('admin/form');
    }

    /**
     * Page Duplicate
     *
     * @return void
     **/
    public function duplicate($id)
    {

        if (!user_has_access('pages.create')) {
            return $this->notFound();
        }

        $val_errors = new \Reborn\Form\ValidationError();

        $page = Pages::find($id);

        self::formElements();
        $this->template->title('Add new Page')
                    ->set('method','create')
                    ->set('page', $page)
                    ->set('errors', $val_errors)
                    ->setPartial('admin/form');
    }

    /**
     * Set Value for DB save
     *
     * @return object
     **/
    protected function setValues($method, $id = null)
    {
        if ($method == 'create') {

            $page = new Pages;

        } else {

            $page = Pages::find($id);

        }

        $parent_id = \Input::get('parent_id');

        if (!empty($parent_id)) {

            $page->parent_id = $parent_id;
            $parent_uri = Pages::getParentUri((int) $parent_id);
            $uri = $parent_uri.'/'.\Input::get('slug');

        } else {

            $uri = \Input::get('slug');

        }

        $slug = (\Input::get('slug') == '') ? 'untitled' : \Input::get('slug');

        $id = \Input::get('id');

        $slug_check = self::slugDuplicateCheck($slug, $id);

        if ($slug_check) {

            $n = 1;
            do {
                $match = preg_match('/(.+)_([0-9]+)$/', $slug, $matches);
                if ($match) {
                    $slug = $matches[1].'_'.$n;
                } else {
                    $slug = $slug.'_'.$n;
                }
                $check = self::slugDuplicateCheck($slug, $id);
                $n++;

            } while ($check);

        }

        $current_user = \Auth::getUser();
        $button_save = \Input::get('page_save');

        if ($button_save != null) {

            $status = ($button_save == t('global.save') || $button_save == t('global.publish')) ? 'live' : 'draft';
            $page->status = $status;

        }

        $page->title = (\Input::get('title') == '') ? 'Untitled' : \Input::get('title');
        $page->slug = $slug;
        $page->uri = $uri;
        $page->content = \Input::get('content');
        $page->page_layout = \Input::get('page_layout');
        $page->meta_title = \Input::get('meta_title');
        $page->meta_keyword = \Input::get('meta_keyword');
        $page->meta_description = \Input::get('meta_description');
        $page->css = \Input::get('css');
        $page->js = \Input::get('js');
        $page->comments_enable = \Input::get('comments_enable');
        $page->author_id = $current_user->id; //get author_id

        if (\Module::get('pages', 'db_version') >= 1.1) {
            $page->editor_type = \Input::get('editor_type');
        }

        return $page;
    }

    protected function slugDuplicateCheck($slug, $id)
    {
        $check = Pages::where('slug', $slug)
                        ->where('id', '!=', $id)
                        ->get();
        if (count($check)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Autosave Page
     *
     * @return json
     **/
    public function autosave()
    {
        $ajax = $this->request->isAjax();
        if ($ajax) {
            if (\Input::isPost()) {
                if ((\Input::get('title') == '') and (\Input::get('slug') == '') and (\Input::get('content') == '')) {
                    return $this->returnJson(array('status' => 'no_save'));
                } else {
                    if (\Input::get('name') == '') {

                    }
                    if (\Input::get('id') == '') {
                        $page = self::setValues('create');
                    } else {
                        $page = self::setValues('edit', \Input::get('id'));
                    }

                    if ($page->save()) {
                        return $this->returnJson(array('status' => 'save', 'post_id' => $page->id, 'time' => sprintf(t('pages::pages.messages.success.autosave_on'), date('d - M - Y H:i A', time()))));
                    }
                }
            }
        }

        return \Redirect::to(adminUrl('pages'));
    }

    /**
     * Delete Page
     *
     * @return void
     **/
    public function delete($id)
    {
        if (!user_has_access('pages.delete')) {
                return $this->notFound();
        }

        if ($id == 1) {
            \Flash::error(t('pages::pages.messages.error.delete_home_page'));

            return \Redirect::to(adminUrl('pages'));
        }

        $page = Pages::find($id);
        $parent_id = $page->parent_id;
        $parent_uri = Pages::getParentUri((int) $parent_id);
        $page_delete = $page->delete();

        if ($page_delete) {
            if (\Module::isEnabled('comment')) {
                $comment_delete = \Comment\Lib\Helper::commentDelete($id, 'pages');
            }
            $child_pages = Pages::where('parent_id', '=', $id)->get();
            foreach ($child_pages as $child) {
                $child_page = Pages::find((int) $child->id);
                $child_uri = $parent_uri.'/'.$child_page->slug;
                $child_page->parent_id = $parent_id;
                $child_page->uri = $child_uri;
                $child_page->save(array(), false);
                self::changeChildrenUri($child->id, $child_page->uri);
            }
        } else {
            //error
        }

        return \Redirect::to(adminUrl('pages'));
    }

    public function changeChildrenUri($id, $parent_uri)
    {
        $second_gen =  Pages::where('parent_id', '=', $id)->get();

        if (count($second_gen) > 0) {
            foreach ($second_gen as $child) {
                $cPage = Pages::find((int) $child->id);
                $cPage->uri = $parent_uri.'/'.$child->slug;
                $cPage->save(array(), false);
                self::changeChildrenUri($child->id, $cPage->uri);
            }
        }

        return;
    }

    /**
     * Change Page Status
     *
     * @return void
     **/
    public function status($id)
    {
        if (!user_has_access('pages.edit')) {
                return $this->notFound();
        }

        $page = Pages::find($id);

        if ($page->status == 'draft') {
            $page->status = 'live';
        } else {
            $page->status = 'draft';
        }

        $status_update = $page->save(array(), false);

        if ($status_update) {
            \Flash::success(t('pages::pages.messages.success.status_update'));
        } else {
            \Flash::error(t('pages::pages.messages.error.status_update'));
        }

        return \Redirect::to('admin/pages');
    }

    /**
     * Form Validation
     *
     * @return void
     **/
    protected function validate()
    {
        $rule = array(
            'title' => 'required|maxLength:225',
            'slug' => 'required|maxLength:225'
        );

        $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

        return $v;
    }

    protected function layoutList()
    {
        $current_theme = \Setting::get('public_theme');
        $theme = new Theme($this->app, $current_theme, THEMES);
        $layouts = $theme->layoutsFrom($current_theme);
        $list = array();
        foreach ($layouts as $key => $val) {
            $value = str_replace('.html', '', $val);
            $name = ucfirst($value);
            $list[$value] = $name;
        }

        return $list;
    }

    protected function formElements()
    {
        $layout_list = self::layoutList();
        $this->template->lang_list = array_merge(array('' => '-- Choose Language --'), \Config::get('langcodes'));
        $content_editor = (\Setting::get('content_editor')) ? \Setting::get('content_editor') : 'wysiwyg';
        $this->template->set('content_editor', $content_editor);
        $this->template->style(array(
                    'form.css',
                ))
                ->script(array(
                    'form.js',
                ))
                ->set('layoutList', $layout_list);
    }

    /**
     * Check slug
     *
     * @return void
     **/
    public function checkSlug()
    {
        $slug = \Input::get('slug');
        if ($slug == "") {
            return "*** This Field is required.";
        } else {
            $id = \Input::get('id');
            if ($id != '') {
                //page edit check slug
                $data = Pages::where('slug', '=', $slug)->where('id', '!=', $id)->get();
            } else {
                //page create check slug
                $data = Pages::where('slug', '=', $slug)->get();
            }
            if (count($data) > 0) {
                $error_msg = t('pages::pages.messages.error.slug_duplicate');

                return $error_msg;
            }
        }
        $this->template->partialOnly();
    }

    /**
     * Save Page Order after sorting
     *
     * @return void
     **/
    public function order()
    {
        $result = \Input::get('order');
        $order = 0;
        foreach ($result as $page_order) {
            $id = (int) $page_order['id'];
            $page = Pages::find($id);
            $page->page_order = $order;
            $page->parent_id = null;
            $page->uri = $page->slug;
            if (isset($page_order['children'])) {
                self::orderChild($page_order['children'],$id);
            }
            $order_save = $page->save(array(), false);
            $order++;
        }
        //dump($result, true);
        $get_pages = Pages::pageStructure();
        //dump($get_pages, true);
        $this->template->setPartial('admin/index')
                    ->set('pages', $get_pages)
                    ->partialOnly();
    }

    /**
     * Change Home Page
     *
     * @return string
     **/
    public function changeHomePage()
    {
        if ($new_home = \Input::get('home_page')) {

            \Setting::set('home_page', $new_home);
            \Flash::success("Successfully set home page !!!");
            return \Redirect::to('admin/pages');

        } else {

            return \Form::start('admin/pages/change-home-page', 'change_home_form').
                    \Form::select('home_page', Helper::parentPages(), \Setting::get('home_page')).
                    \Form::submit('submit', 'Set Home Page', array('class' => 'btn btn-green')).
                    \Form::end();

        }
    }

    /**
     * Save Page structure of children
     *
     * @return void
     * @author
     **/
    protected function orderChild($children,$parent_id)
    {
        $order = 0;
        foreach ($children as $child) {
            $id = (int) $child['id'];
            $page = Pages::find($id);
            $parent_uri = Pages::getParentUri($parent_id);
            $new_uri = $parent_uri.'/'.$page->slug;
            $page->page_order = $order;
            $page->parent_id = $parent_id;
            $page->uri = $new_uri;
            $save = $page->save(array(), false);
            //dump($save);
            if (isset($child['children'])) {
                self::orderChild($child['children'],$id);
            }
            $order++;
        }
    }

    public function after($response)
    {
        return parent::after($response);
    }
}
