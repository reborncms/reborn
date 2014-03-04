<?php

namespace Blog\Controller\Admin;

use Blog\Model\Blog;

use Blog\Lib\Helper;

use Auth,
    Config,
    Event,
    Field,
    Flash,
    Input,
    Module,
    Pagination,
    Redirect,
    Setting,
    Str;

class BlogController extends \AdminController
{
    public function before()
    {
        $this->menu->activeParent('content');

        $this->template->style('blog.css','blog');
        $this->template->script('blog.js','blog');

        $ajax = $this->request->isAjax();

        if ($ajax) {

            $this->template->partialOnly();

        }
    }

    /**
     * Blog Index
     *
     * @return void
     **/
    public function index($id = null)
    {
        //Pagination
        $options = array(
            'total_items'       => Blog::where('lang_ref', null)->count(),
            'items_per_page'    => Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = Blog::with(array('category','author'))
                            ->where('lang_ref', null)
                            ->orderBy('created_at', 'desc')
                            ->skip(Pagination::offset())
                            ->take(Pagination::limit())
                            ->get();

        $trash_count = Helper::trashCount();

        $this->template->title(t('blog::blog.title_main'))
                        ->setPartial('admin/index')
                        ->set('pagination', $pagination)
                        ->set('blogs',$blogs)
                        ->set('trash_count', $trash_count)
                        ->set('list_type', 'index');

        $data_table = $this->template->partialRender('admin/table');
        $this->template->set('data_table', $data_table);
    }

    /**
     * Blog Create
     *
     * @return void
     **/
    public function create()
    {
        if (!user_has_access('blog.create')) {
                return $this->notFound();

        }

        $blog = new Blog();

        if (Input::isPost()) {

            if (Input::get('id') != '') {

                $blog = self::setValues('edit', Input::get('id'));

            } else {

                $blog = self::setValues('create');

            }

            if ($blog->save()) {

                Helper::tagSave($blog->id, Input::get('blog_tag'));

                if (Module::isEnabled('field')) {

                    Field::save('blog', $blog);

                }

                Event::call('reborn.blog.create');
                Flash::success(t('blog::blog.create_success'));

                return Redirect::to(adminUrl('blog'));

            } else {

                Flash::error(t('blog::blog.create_error'));

            }

        }

        self::formEle($blog);

        $this->template->title(t('blog::blog.title_create'))
                        ->set('method', 'create');
    }

    /**
     * Edit Blog
     *
     * @return void
     **/
    public function edit($id = null)
    {

        if (Input::isPost()) {

                $blog = self::setValues('edit', Input::get('id'));

                if ($blog->save()) {

                    Helper::tagSave($blog->id, Input::get('blog_tag'));

                    if (Module::isEnabled('field')) {

                        Field::update('blog', $blog);

                    }

                    Flash::success(t('blog::blog.edit_success'));

                    return Redirect::to(adminUrl('blog'));

                } else {

                    Flash::error(t('blog::blog.edit_error'));

                }

        } else {

            if ($id == null) {
                return Redirect::to(adminUrl('blog'));

            } else {

                $blog = Blog::find($id);

                if (empty($blog)) {
                    return $this->notFound();

                }

            }

        }

        self::formEle($blog);

        $this->template->title('Edit Blog')
                        ->set('method', 'edit');
    }

    // ======== Multi-Language ====== //

    /**
     * Add another language for blog content
     *
     * @return void
     * @author
     **/
    public function multilang($id = null)
    {
        if ($id != null) {

            $blog = Blog::find($id);

            if (empty($blog)) {
                return $this->notFound();

            }

        }

        if (Input::isPost()) {

            $blog = self::setValues('create');

            $langDup = Helper::langDuplicate(\Input::get('lang'), \Input::get('lang_ref'));

            if (!$langDup) {

                if ($blog->save()) {

                    Helper::tagSave($blog->id, \Input::get('blog_tag'));

                    if (Module::isEnabled('field')) {

                        Field::save('blog', $blog);

                    }

                    Flash::success(t('blog::blog.create_success'));

                    return Redirect::to(adminUrl('blog'));

                } else {

                    Flash::error(t('blog::blog.create_error'));

                }

            } else {

                $lang = Config::get('langcodes.'.Input::get('lang'));
                Flash::error($lang.' article for this post is already exist.');

            }

        }

        self::formEle($blog);
        $this->template->title('Another Language')
                        ->set('method', 'multilang');

    }

    /**
     * Change Blog Status
     *
     * @return void
     **/
    public function changeStatus($id = null)
    {
        if (!$id) {
            return $this->notFound();

        }

        $blog = Blog::find($id);

        if ($blog->status == 'draft') {

            $blog->status = 'live';

        } else {

            $blog->status = 'draft';

        }

        $save = $blog->save(array(), false);

        if ($save) {

            Flash::success(t('blog::blog.change_status_success'));

        } else {

            Flash::error(t('blog::blog.change_status_error'));

        }

        return Redirect::to(adminUrl('blog'));

    }

    /**
     * Publish the scheduled post
     *
     * @return void
     * @author
     **/
    public function publish($id)
    {
        if (!$id) {
            return $this->notFound();

        }

        $blog = Blog::find($id);

        $blog->created_at = new \DateTime();
        $blog->updated_at = new \DateTime();

        //dump($blog, true);

        if ($blog->save(array(), false)) {
            \Flash::success(t('blog::blog.publish_success'));
        } else {
            \Flash::error(t('blog::blog.publish_error'));
        }

        return Redirect::to(adminUrl('blog'));

    }

    /**
     * Restore from Trash
     *
     **/
    public function restore($id = null)
    {

        if (!$id) {
            return $this->notFound();

        }

        $restore = Blog::withTrashed()->where('id', $id)->restore();

        if (Module::isEnabled('comment')) {

            $comment_delete = \Comment\Lib\Helper::commentRestore($id, 'blog');

        }

        if ($restore) {

            Flash::success("Successfully Restored");

        } else {

            Flash::error("Error Restored");

        }

        return Redirect::to(adminUrl('blog/trash'));

    }

    /**
     * Delete Blog
     *
     * @return void
     **/
    public function delete($id = 0)
    {

        $ids = ($id) ? array($id) : \Input::get('action_to');

        $blogs = array();

        foreach ($ids as $id) {

            if ($blog = Blog::withTrashed()->where('id', $id)->first()) {

                if ($blog->trashed()) {

                    // only delete when force Delete
                    if (Module::isEnabled('field')) {

                        Field::delete('blog', $blog);
                    }

                    $blog->forceDelete();

                    //Also soft deleted to comment and tag
                    if (Module::isEnabled('tag')) {

                        $tag = \Tag\Model\TagsRelationship::where('object_id', $id)
                                                        ->where('object_name', 'blog')
                                                        ->delete();

                    }

                    if (Module::isEnabled('comment')) {

                        $comment_delete = \Comment\Lib\Helper::commentDelete($id, 'blog', true);

                    }

                    $redirect_url = 'blog/trash';

                } else {

                    $blog->delete();

                    if (Module::isEnabled('comment')) {

                        $comment_delete = \Comment\Lib\Helper::commentDelete($id, 'blog');

                    }

                    $redirect_url = 'blog';

                    //solft delete tag and comments

                }

                $blogs[] = "success";

            }

        }

        if (!empty($blogs)) {

            if (count($blogs) == 1) {

                Flash::success(t('blog::blog.delete_success'));

            } else {

                Flash::success(t('blog::blog.delete_success_many'));

            }

            Event::call('reborn.blog.delete');

        } else {

            Flash::error(t('blog::blog.delete_error'));

        }

        return Redirect::to(adminUrl($redirect_url));

    }

    /**
     * Get Post links for Editor Plugin
     *
     * @param  int|null $id Post ID for Skipping
     * @return void
     **/
    public function postLinks($id = null)
    {

        if ( is_null($id) ) {

            $total = Blog::active()->where('lang_ref', null)->count();
            $links = Blog::active();

        } else {

            $total = Blog::active()->where('id', '!=', $id)
                            ->where('lang_ref', null)->count();
            $links = Blog::active()->where('id', '!=', $id);
            $this->template->id = $id;

        }

        $options = array(
            'total_items'       => $total,
            'items_per_page'    => \Setting::get('admin_item_per_page'),
        );

        $pagination = \Pagination::create($options);

        $links = $links->where('lang_ref', null)
                        ->orderBy('created_at', 'desc')
                        ->skip(\Pagination::offset())
                        ->take(\Pagination::limit())
                        ->get(array('title', 'slug', 'lang'));

        $this->template->partialOnly();
        $this->template->setPartial('admin/editor/index', compact('links', 'pagination'));

    }

    /**
     * Ajax Filter Search lists for Editor
     *
     * @return void
     **/
    public function searchLists()
    {

        $term = Input::get('term');
        $id = Input::get('edit_mode');

        if ( is_null($id) ) {

            $total = Blog::active()->where('lang_ref', null)
                            ->where('title', 'like', '%'.$term.'%')->count();
            $links = Blog::active();

        } else {

            $total = Blog::active()->where('id', '!=', $id)
                            ->where('lang_ref', null)
                            ->where('title', 'like', '%'.$term.'%')
                            ->count();
            $links = Blog::active()->where('id', '!=', $id);
            $this->template->id = $id;

        }

        $options = array(
            'total_items'       => $total,
            'items_per_page'    => \Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        $links = $links->where('lang_ref', null)
                        ->orderBy('created_at', 'desc')
                        ->skip(Pagination::offset())
                        ->take(Pagination::limit())
                        ->where('title', 'like', '%'.$term.'%')
                        ->get(array('title', 'slug', 'lang'));

        $this->template->partialOnly();
        $this->template->term = $term;
        $this->template->setPartial('admin/editor/index', compact('links', 'pagination'));

    }

    /**
     * Set JS and Style to Template
     *
     * @return void
     **/
    protected function formEle($blog)
    {

        $fields = array();

        if (Module::isEnabled('field')) {

            $fields = Field::getForm('blog', $blog);

        }

        $authors[0] = '-- '. t('blog::blog.auto_detect') .' -- ';

        $users = Auth::getUserProvider()->findAllWithAccess('admin');

        foreach ($users as $user) {

            $authors[$user->id] = $user->first_name . ' ' . $user->last_name;

        }

        $lang_list = array_merge(array('none' => '-- Choose Language --'), Config::get('langcodes'));

        $this->template->setPartial('admin/form')
                        ->set('authors', $authors)
                        ->set('blog', $blog)
                        ->set('custom_field', $fields)
                        ->set('lang_list', $lang_list)
                        ->set('post_types', Config::get('blog::blog.post_types'))
                        ->jsValue('post_id', $blog->id)
                        ->style(array(
                            'plugins/jquery.tagsinput_custom.css',
                            'form.css'))
                        ->script('chosen.jquery.min.js', 'blog')
                        ->style('chosen.min.css', 'blog')
                        ->script(array(
                            'plugins/jquery-ui-timepicker-addon.js',
                            'plugins/jquery.tagsinput.min.js',
                            'form.js'));

    }

    /**
     * Set Form Values of Create and Edit Blog
     *
     * @return boolean
     **/
    protected function setValues($method, $id = null)
    {

        if ($method == 'create') {

            $blog = new Blog;

        } else {

            $blog = Blog::find($id);

        }

        if (Input::get('author_id') == 0) {

            $author = Auth::getUser()->id;

        } else {

            $author = Input::get('author_id');

        }

        $button_save = Input::get('blog_save');

        if ($button_save !== null) {

            $status = ($button_save == t('global.save') || $button_save == t('global.publish')) ? 'live' : 'draft';
            $blog->status = $status;

        }

        //if excerpt is empty get some part from body
        if (Input::get('excerpt') == '') {

            $excerpt = Str::words(strip_tags(html_entity_decode(Input::get('body'))), Setting::get('excerpt_length'));

        } else {

            $excerpt = Input::get('excerpt');

        }

        $slug = (Input::get('slug') == '') ? 'untitled' : \Input::get('slug');

        $id = Input::get('id');

        $slug_check = Helper::slugDuplicateCheck($slug, $id);

        if ($slug_check) {

            do {

                $slug = Str::increment($slug);
                $check = Helper::slugDuplicateCheck($slug, $id);

            } while ($check);

        }

        $blog->title = (Input::get('title') == '') ? 'Untitled' : \Input::get('title');
        $blog->slug = $slug;
        $blog->category_id = Input::get('category_id');
        $blog->excerpt = $excerpt;
        $blog->post_type = Input::get('post_type');
        $blog->body = Input::get('body');
        $blog->author_id = $author;

        if (Module::get('blog', 'db_version') >= 1.1) {

            //Check if this lang is already exist

            $lang = Input::get('lang');

            $lang_ref = Input::get('lang_ref');

            $blog->lang = $lang;

            if ($lang_ref) {

                $blog->lang_ref = $lang_ref;

            }

        }

        $blog->comment_status = Input::get('comment_status');

        if (Input::get('sch_type') != null) {

            if (Input::get('sch_type') == 'manual') {

                $blog->created_at = new \DateTime(Input::get('date'));

            } else {

                if ($method == 'create') {

                    $blog->created_at = new \DateTime();

                }

            }

        }

        if ($method == 'edit') {

            $blog->updated_at = new \DateTime();

        }

        // Remove Base Url from Attachment
        $blog->attachment = remove_base_url(Input::get('attachment'));
        //type
        return $blog;

    }

    /**
     * Ajax Check slug
     *
     * @return void
     **/
    public function checkSlug()
    {
        $slug = Input::get('slug');

        if ($slug == "") {
            return "*** This Field is required.";

        } else {

            $id = (int) Input::get('id');

            if ($id != '') {

                //page edit check slug
                $data = Blog::where('slug', '=', $slug)->where('id', '!=', $id)->get();

            } else {

                //page create check slug
                $data = Blog::where('slug', '=', $slug)->get();

            }

            if (count($data) > 0) {

                $error_msg = t('validation.slug_duplicate');

                return $error_msg;

            }

        }

        $this->template->partialOnly();

    }

    /**
     * Ajax Filter Search
     *
     * @return void
     **/
    public function search()
    {

        $term = Input::get('term');

        if ($term) {

            $result = Blog::with(array('category','author'))
                        ->where('title', 'like', '%'.$term.'%')
                        ->get();

        } else {

            $options = array(
                'total_items'       => Blog::count(),
                'items_per_page'    => Setting::get('admin_item_per_page'),
            );

            $pagination = Pagination::create($options);

            $result = Blog::with(array('category','author'))
                                ->where('lang_ref', null)
                                ->skip(\Pagination::offset())
                                ->take(\Pagination::limit())
                                ->orderBy('created_at', 'desc')
                                ->get();

            $this->template->set('pagination', $pagination);

        }

        $this->template->partialOnly()
             ->set('blogs', $result)
             ->setPartial('admin/table')
             ->set('list_type', 'search');
    }

    /**
     * Autosave Posts
     *
     * @return json
     **/
    public function autosave()
    {
        $ajax = $this->request->isAjax();

        if ($ajax) {

            if (Input::isPost()) {

                if ((Input::get('title') == '' ) and
                    (Input::get('slug') == '') and
                    (Input::get('body') == ''))  {
                    return $this->returnJson(array('status' => 'no_save'));

                } elseif (Helper::langDuplicate(Input::get('lang'), Input::get('lang_ref'))) {
                    return $this->returnJson(array(
                            'status'	=> 'no_save',
                            'msg'		=> 'Language already exist.'
                        ));

                } else {

                    if (Input::get('id') == '') {

                        $blog = self::setValues('create');

                    } else {

                        // update
                        $blog = self::setValues('edit', Input::get('id'));

                    }

                    if ($blog->save()) {
                        return $this->returnJson(array(
                            'status' => 'save',
                            'post_id' => $blog->id,
                            'time' => sprintf(t('blog::blog.autosave_on'), date('d - M - Y H:i A', time()))));

                    }

                }

            }

        }

        return Redirect::to(adminUrl('blog'));
    }

    /**
     * View Trash
     *
     * @return void
     * @author
     **/
    public function trash()
    {

        $trash_count = Helper::trashCount();

        $options = array(
            'total_items'       => $trash_count,
            'items_per_page'    => Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = Blog::with(array('category','author'))
                            ->onlyTrashed()
                            ->orderBy('deleted_at', 'desc')
                            ->skip(Pagination::offset())
                            ->take(Pagination::limit())
                            ->get();

        $this->template->title(t('blog::blog.title_main'))
                        ->setPartial('admin/index')
                        ->set('pagination', $pagination)
                        ->set('blogs',$blogs)
                        ->set('trash_count', $trash_count)
                        ->set('list_type', 'trash');

        $data_table = $this->template->partialRender('admin/table');
        $this->template->set('data_table', $data_table);

    }

}
