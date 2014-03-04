<?php

namespace Blog\Controller\Admin;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

use Blog\Lib\Helper;

class CategoryController extends \AdminController
{
    /**
     * Before Method
     *
     * @return void
     **/
    public function before()
    {
        $this->menu->activeParent('content');

        \Translate::load('blog::blog');
        $this->template->style('blog.css','blog');
        $this->template->script('blog.js','blog');
    }

    /**
     * Category Index
     *
     * @return void
     **/
    public function index()
    {
        $categories = BlogCategory::cat_stucture();
        $category_form = $this->template->partialRender('admin/category/form');
        $this->template->title('Categories')
                        ->setPartial('admin/category/index')
                        ->set('categories', $categories)
                        ->set('category_form', $category_form)
                        ->style('form.css')
                        ->script(array(
                            'plugins/jquery.ui.touch-punch.min.js',
                            'plugins/jquery.mjs.nestedSortable.js',
                            'form.js'
                        ));
    }

    /**
     * Add new Category
     *
     * @return void
     **/
    public function create()
    {
        $ajax = $this->request->isAjax();

        $data = '';

        if (\Input::isPost()) {
            $val = self::validate();
            if ($val->valid()) {
                $blogCat = self::setVals('create');
                $save_cat = $blogCat->save();
                $save_id = $blogCat->id;
                if ($save_cat) {
                    $msg = t('blog::blog.cat_create_success');
                    if ($ajax) {
                        return	json_encode(array('status' => 'ok', 'success' => $msg, 'saveID' => $save_id));
                    } else {
                        \Flash::success($msg);

                        return \Redirect::to(adminUrl('blog/category'));
                    }
                } else {
                    $msg = t('blog::blog.cat_create_error');
                    if ($ajax) {
                        return	json_encode(array('status' => 'fail' ,'error' => $msg));
                    } else {
                        \Flash::error($msg);

                        return \Redirect::to(adminUrl('blog/category'));
                    }
                }
            } else {
                $val_errors = $val->getErrors();
                if ($ajax) {
                    $status = array('status' => 'invalid');
                    $errors = array_merge($status, $val_errors);

                    return json_encode($errors);
                } else {
                    $this->template->set('val_errors', $val_errors);
                }
            }
            $data = \Input::get('*');
        }

        if ($ajax) {
            $this->template->partialOnly();
        }

        $this->template->setPartial('admin/category/form')
                       ->set('method','create')
                       ->set('cat',$data)
                       ->set('ajax', $ajax);

    }

    /**
     * Edit Category
     *
     * @return void
     **/
    public function edit($id = null)
    {
        $ajax = $this->request->isAjax();

        if (\Input::isPost()) {
            $val = self::validate();
            if ($val->valid()) {

                $blogCat = self::setVals('edit', \Input::get('cat_id'));
                $catUpdate = $blogCat->save();

                if ($catUpdate) {
                    $msg = t('blog::blog.cat_edit_success');
                    if ($ajax) {
                        return	json_encode(array('status' => 'ok', 'success' => $msg));
                    } else {
                        \Flash::success($msg);

                        return \Redirect::to(adminUrl('blog/category'));
                    }
                } else {
                    $err_msg = t('blog::blog.cat_edit_error');
                    if ($ajax) {
                        return	json_encode(array('status' => 'fail' ,'error' => $err_msg));
                    } else {
                        \Flash::error($err_msg);
                    }
                }
            } else {
                $val_errors = $val->getErrors();
                if ($ajax) {
                    $status = array('status' => 'invalid');
                    $errors = array_merge($status, $val_errors);

                    return json_encode($errors);
                } else {
                    $this->template->set('val_errors', $val_errors);
                }
            }
            $data = (object) \Input::get('*');
        } else {
            $data = BlogCategory::find($id);
        }

        if ($ajax) {
            $this->template->partialOnly();
        }

        $this->template->setPartial('admin/category/form')
                       ->set('method','edit')
                       ->set('cat',$data)
                       ->set('ajax', $ajax);
    }

    /**
     * Delete Category
     *
     * @return void
     **/
    public function delete($id)
    {
        if ($id == 1) {
            \Flash::warning(t('blog::blog.not_allow_delete'));

            return \Redirect::to(adminUrl('blog/category'));
        }
        $item = BlogCategory::find($id);

        //Update Parent_id of Children
        $getChildren = BlogCategory::where('parent_id', '=' , $id)->count();

        if ($getChildren > 0) {

            //Have Children No Parent
            if ($item->parent_id == 0) {

                $updateParent = BlogCategory::where('parent_id', '=', $id)->update(array('parent_id' => 0));

            } else { //Have Children Have Parent

                $updateParent = BlogCategory::where('parent_id', '=', $id)->update(array('parent_id' => $item->parent_id));

            }

        }

        $item->delete();

        //update blog category of the posts to default category
        Blog::where('category_id', '=', $id)->update(array('category_id' => 1));

        \Flash::success(t('blog::blog.cat_delete_success'));

        return \Redirect::to(adminUrl('blog/category'));
    }

    /**
     * Set Values to save
     *
     * @return void
     **/
    protected function setVals($method, $id=null)
    {
        if ($method == 'create') {
            $blogCat = new BlogCategory;
        } else {
            $blogCat = BlogCategory::find($id);
        }
        $blogCat->name = \Input::get('name');
        $blogCat->slug = \Input::get('slug');
        $blogCat->description = \Input::get('description');
        $blogCat->parent_id = \Input::get('cat_selected');

        return $blogCat;
    }

    /**
     * Sort Category
     *
     * @return void
     **/
    public function order()
    {
        $result = \Input::get('order');
        $order = 0;
        foreach ($result as $cat_order) {
            $id = (int) $cat_order['id'];
            $category = BlogCategory::find($id);
            $category->order = $order;
            $category->parent_id = 0;
            //uri
            if (isset($cat_order['children'])) {
                self::order_child($cat_order['children'], $id);
            }
            $order_save = $category->save();
            $order++;
        }
        $this->template->partialOnly();
    }

    /**
     * Sort children
     *
     * @return void
     **/
    protected static function order_child($children, $parent_id)
    {
        $order = 0;
        foreach ($children as $child) {
            $id = (int) $child['id'];
            $category = BlogCategory::find($id);
            //uri
            $category->order = $order;
            $category->parent_id = $parent_id;
            $category->save();
            if (isset($child['children'])) {
                self::order_child($child['children'], $id);
            }
            $order++;
        }
    }

    /**
     * Validation
     *
     * @return void
     **/
    public function validate()
    {
        $rule = array(
            'name' => 'required|maxLength:100',
            'slug' => 'required|maxLength:100'
        );

        $v = new \Reborn\Form\Validation(\Input::get('*'), $rule);

        return $v;
    }

    /**
     * get Category Select
     *
     * @return void
     **/
    public function getCategory($selected = null)
    {
        if ($selected != null) {
            return \Form::select('category_id', Helper::catList(), $selected, array('class' => 'xx-large'));
        } else {
            return \Form::select('category_id', Helper::catList(), '', array('class' => 'xx-large'));
        }
    }

    /**
     * After Method
     *
     * @return void
     **/
    public function after($response)
    {
        return parent::after($response);
    }

}
