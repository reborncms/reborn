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

use Blog\Lib\DataProvider as Provider;

class BlogController extends \AdminController
{
    /**
     * Data Provider
     *
     **/
    protected $provider;

    public function before()
    {
        $this->provider = new Provider;

        $this->menu->activeParent('content');

        $this->template->style('blog.css','blog');
        $this->template->script('blog.js','blog');

        $ajax = $this->request->isAjax();

        if ($ajax) {

            $this->template->partialOnly();

        }
    }

    /*|-------------------------------------------------------------------------------------
      | Retrive Data Session
      | --------------------
      | * index 
      | * search
      | * trash 
      |  
      |-------------------------------------------------------------------------------------
     */

    /**
     * Blog Index
     *
     * @return void
     **/
    public function index($id = null)
    {
        //Pagination
        $options = array(
            'total_items'       => $this->provider->countBy(array('lang_ref' => null)),
            'items_per_page'    => Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = $this->provider->getAllParentLangPosts(Pagination::limit(), Pagination::offset());

        $trash_count = $this->provider->trashCount();

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
     * Blog List by Category
     *
     * @return void
     * @author 
     **/
    public function category($category_id)
    {
        $options = array(
            'total_items'       => $this->provider->countBy(array('lang_ref' => null, 'category_id' => $category_id)),
            'items_per_page'    => Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = $this->provider->getPostsBy(array(
            'instances' => array('notOtherLang', 'order'),
            'wheres'    => array('category_id' => $category_id)
        ));

        if (count($blogs) > 0) {
            $this->template->category_name = $blogs[0]->category->name;
        }

        $trash_count = $this->provider->trashCount();

        $this->template->title(t('blog::blog.title_main'))
                        ->setPartial('admin/index')
                        ->set('pagination', $pagination)
                        ->set('blogs',$blogs)
                        ->set('trash_count', $trash_count)
                        ->set('list_type', 'category');

        $data_table = $this->template->partialRender('admin/table');
        $this->template->set('data_table', $data_table);
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

            $result = $this->provider->searchPostBy($term, 'title');

            $this->template->set('list_type', 'search');

        } else {

            $options = array(
                'total_items'       => $this->provider->countBy(array('lang_ref' => null)),
                'items_per_page'    => Setting::get('admin_item_per_page'),
            );

            $pagination = Pagination::create($options);

            $result = $this->provider->getAllParentLangPosts(Pagination::limit(), Pagination::offset());

            $this->template->set('pagination', $pagination)
                             ->set('list_type', 'index');

        }

        $this->template->partialOnly()
             ->set('blogs', $result)
             ->setPartial('admin/table');
    }

    /**
     * View Trash
     *
     * @return void
     * @author
     **/
    public function trash()
    {

        $trash_count = $this->provider->trashCount();

        $options = array(
            'total_items'       => $trash_count,
            'items_per_page'    => Setting::get('admin_item_per_page'),
        );

        $pagination = Pagination::create($options);

        $blogs = $this->provider->getTrashedPosts(Pagination::limit(), Pagination::offset());

        $this->template->title(t('blog::blog.title_main'))
                        ->setPartial('admin/index')
                        ->set('pagination', $pagination)
                        ->set('blogs',$blogs)
                        ->set('trash_count', $trash_count)
                        ->set('list_type', 'trash');

        $data_table = $this->template->partialRender('admin/table');
        $this->template->set('data_table', $data_table);

    }


    /*|-------------------------------------------------------------------------------------
      | Data Session (Full Data Session)
      | --------------------
      | * create
      | * edit
      | * multilang
      | * autosave
      | * delete
      |
      |-------------------------------------------------------------------------------------
     */

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

        $blog = $this->provider->newBlogInstance();

        if (Input::isPost()) {

            if (Input::get('id')) {

              $blog = $this->provider->update(Input::get('id'), Input::get('*'));

            } else {

              $blog = $this->provider->create(Input::get('*'));

            }

            if ($blog) {

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

                $blog = $this->provider->update(Input::get('id'), Input::get('*'));

                if ($blog) {

                    Flash::success(t('blog::blog.edit_success'));

                    return Redirect::to(adminUrl('blog'));

                } else {

                    Flash::error(t('blog::blog.edit_error'));

                }

        } else {

            if ($id == null) {
                return Redirect::to(adminUrl('blog'));

            } else {

                $blog = $this->provider->post($id, false);

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

            $blog = $this->provider->post($id, false);

            if (empty($blog)) {
                
                return $this->notFound();

            }

        }

        if (Input::isPost()) {

            $langDup = $this->provider->langDuplicate(\Input::get('lang'), \Input::get('lang_ref'));

            if (!$langDup) {

                $blog = $this->provider->create(Input::get('*'));

                if ($blog) {

                    Flash::success(t('blog::blog.create_success'));

                    return Redirect::to(adminUrl('blog'));

                } else {

                    $blog = Input::get('*');
                    Flash::error(t('blog::blog.create_error'));

                }

            } else {

                $blog = Input::get('*');
                $lang = Config::get('langcodes.'.Input::get('lang'));
                Flash::error($lang.' article for this post is already exist.');

            }

        }

        self::formEle($blog);

        $this->template->title('Another Language')
                        ->set('method', 'multilang');

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

                } elseif ($this->provider->langDuplicate(\Input::get('lang'), \Input::get('lang_ref'))) {
                    return $this->returnJson(array(
                            'status'    => 'no_save',
                            'msg'       => 'Language already exist.'
                        ));

                } else {

                    if (Input::get('id') == '') {

                        $blog = $this->provider->create(Input::get('*'));

                    } else {

                        // update
                        $blog = $this->provider->update(Input::get('id'), Input::get('*'));

                    }

                    if ($blog) {

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
     * Delete Blog
     *
     * @return void
     **/
    public function delete($id = 0)
    {

        $ids = ($id) ? array($id) : \Input::get('action_to');

        $blogs = array();

        foreach ($ids as $id) {

            if ($blog = $this->provider->getPostWithTrashed(array('id' => $id))) {

                if ($blog->trashed()) {

                    // only delete when force Delete
                    $this->provider->delete($blog);

                    $redirect_url = 'blog/trash';

                } else {

                    $this->provider->moveToTrash($blog);

                    $redirect_url = 'blog';

                }

                $blogs[] = "success";

            } else {

              return $this->notFound();

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

    /*|-------------------------------------------------------------------------------------
      | Partial Data Update Session
      | ---------------------------
      | * changeStatus
      | * publish
      | * restore
      |
      |-------------------------------------------------------------------------------------
     */

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

        $blog = $this->provider->post($id, false);

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

        $blog = $this->provider->post($id, false);

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

        $restore = $this->provider->restore($id);

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

    /*|-------------------------------------------------------------------------------------
      | Editor Plugin Methods
      | ---------------------------
      | * postLinks
      | * searchLists
      | 
      |
      |-------------------------------------------------------------------------------------
     */

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

    /*|-------------------------------------------------------------------------------------
      | Extra sidekick Methods
      | ---------------------------
      | * checkSlug
      | 
      |-------------------------------------------------------------------------------------
     */

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

                return t('validation.slug_duplicate');

            }

        }

        $this->template->partialOnly();

    }

    /*|-------------------------------------------------------------------------------------
      | Internal Helper functions
      | ---------------------------
      | * formEle
      | 
      |-------------------------------------------------------------------------------------
     */

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

        $lang_list = array_merge(array('' => '-- Choose Language --'), Config::get('langcodes'));

        $content_editor = (\Setting::get('content_editor')) ? \Setting::get('content_editor') : 'wysiwyg';

        $this->template->setPartial('admin/form')
                        ->set('authors', $authors)
                        ->set('blog', $blog)
                        ->set('custom_field', $fields)
                        ->set('lang_list', $lang_list)
                        ->set('post_types', Config::get('blog::blog.post_types'))
                        ->set('content_editor', $content_editor)
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

}
