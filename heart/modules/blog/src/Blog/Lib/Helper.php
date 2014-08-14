<?php

namespace Blog\Lib;

use Blog\Model\BlogCategory;

use Blog\Model\Blog;

class Helper
{
    protected static $level = 1;

    protected static $cat_list;

    protected static $inc_count = 0;

    public static function catStructure($category)
    {
        $bcs = '';
        $bcs .= '<div class="draggable_wrap">';
        $bcs .= 	$category['name'];

        $bcs .= '<div class="actions">';
        $bcs .= '<a href="'.\Uri::create(ADMIN_URL.'/blog/category/edit/'.$category['id']).'" title="'. t('global.edit') .'" class="tipsy-tip c-edit-box"><i class="icon-edit icon-black"></i></a>';
        if ($category['id'] != 1) {
            $bcs .= '<a href="'.\Uri::create(ADMIN_URL.'/blog/category/delete/'.$category['id']).'" title="'. t('global.delete') .'" class="confirm_delete tipsy-tip"><i class="icon-remove icon-black"></i></a>';
        }
        $bcs .= '</div>';
        $bcs .= '</div>';

        return $bcs;
    }

    public static function changeAuthor($author_id)
    {
        $blogs = Blog::where('author_id', $author_id)->update(array('author_id' => 1));
        if ($blogs) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateChildren($children)
    {
        $gc = '';
        $gc .= '<ol>';
        foreach ($children as $category) {
            $gc .= '<li id="cat_'.$category['id'].'">';
            $gc .= self::catStructure($category);
            if (isset($category['children'])) {
                $gc .= self::generateChildren($category['children']);
            }
            $gc .= '</li>';
        }
        $gc .= '</ol>';

        return $gc;
    }

    public static function langList($id, $frontend = false)
    {
        $langList = array();
        $main = Blog::find($id);

        if ($main->lang_ref != null) {

            $main = Blog::find($main->lang_ref);

        }

        $langList[$main->lang] = array(
            'id' => $main->id,
            'lang' => $main->lang,
            'title' => $main->title,
            'slug' => $main->slug,
            'status' => $main->status
        );

        if ($frontend) {
            $bLang = Blog::active()->where('lang_ref', $main->id)->get(array('id','lang','title', 'slug', 'status'));
        } else {
            $bLang = Blog::where('lang_ref', $main->id)->get(array('id','lang','title', 'slug', 'status'));
        }

        foreach ($bLang as $lang) {

            $langList[$lang->lang] = array(
                'id' => $lang->id,
                'lang' => $lang->lang,
                'title' => $lang->title,
                'slug' => $lang->slug,
                'status' => $lang->status
            );

        }

        return $langList;

    }

    public static function catList($from = null, $currentCat = null)
    {
        if ($from != null) {
            static::$cat_list[0] = '-- none --';
        }
        $categories = BlogCategory::cat_stucture();
        foreach ($categories as $category) {
            static::$cat_list[$category['id']] = $category['name'];
            if (isset($category['children'])) {
                static::$level = 1;
                self::childCats($category['children'], 1);
            }
        }
        if ($currentCat) {
            unset(static::$cat_list[$currentCat]);
        }

        return static::$cat_list;
    }

    protected static function childCats($children, $lvl = 0)
    {
        foreach ($children as $category) {
            static::$cat_list[$category['id']] = str_repeat('>',$lvl).' '.$category['name'];
            if (isset($category['children'])) {
                static::$level++;
                static::$inc_count++;
                self::childCats($category['children'], static::$level);
            } else {
                static::$level = static::$level - static::$inc_count;
                static::$inc_count = 0;
            }
        }
    }

    public static function dashboardWidget()
    {
        $widget = array();
        $widget['title'] = t('label.last_post');
        $widget['icon'] = 'icon-archive';
        $widget['id'] = 'blog';
        $widget['body'] = '';
        $posts = Blog::with('author')->take(5)->orderBy('created_at', 'desc')->get();
        $widget['body'] .= '<ul>';
        if (count($posts) > 0) {
            foreach ($posts as $post) {
                $widget['body'] .= '<li>
                                    <div class="widget-list-meta">
                                        <span class="date f-right">'.$post->post_date('d M Y').'</span>
                                        <span class="blog-author"><i class="icon-user"></i>
                                            <a href="'.url('user/profile/'.$post->author->id).'">'.$post->author_name.'</a>
                                        </span>
                                    </div>
                                    <div class="widget-list-content">
                                        <a href="'.url('blog/'.$post->slug).'" target="_black" class="no-overflow-txt" style="width: 65%;">'.$post->title.'</a>
                                        <span class="dashboard_widget_action">
                                            <a href="'.admin_url('blog/edit/'.$post->id) .'" title="'. t('global.edit') .'" class="tipsy-tip"><i class="icon-edit"></i></a>
                                        </span>
                                    </div>
                                    </li>';
            }
        } else {
            $widget['body'] .= '<li><span class="empty-list">'. t('label.last_post_empty') .'</span></li>';
        }
        $widget['body'] .= '</ul>';

        return $widget;
    }

    /**
     * Save Blog Tags
     *
     * @return boolean
     **/
    public static function tagSave($id, $tags)
    {
        $tag = \Tag\Lib\Helper::import($id, 'blog', $tags);

        if ($tag) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Slug Duplication
     *
     * @return boolean
     **/
    public static function slugDuplicateCheck($slug, $id)
    {
        $check = Blog::where('slug', $slug)
                    ->where('id', '!=', $id)
                    ->get();
        if (count($check)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check Language Duplicate
     *
     * @return void
     * @author
     **/
    public static function langDuplicate($lang, $lang_ref)
    {

        $check = Blog::where('lang', $lang)
                        ->where('lang_ref', $lang_ref)
                        ->count();

        $org_post = Blog::where('lang', $lang)
                        ->where('id', $lang_ref)
                        ->count();

        if ($check or $org_post) {
            return true;

        } else {
            return false;

        }
    }

    /**
     * Get Blog Category Level
     *
     * @return int
     * @author 
     **/
    public static function getCatLvl($categories, $category)
    {

        return self::getParentCat($categories, $category, 0);

    }

    /**
     * Check parent and get level
     *
     * @return void
     * @author 
     **/
    protected static function getParentCat($categories, $category, $level)
    {

        $parent_cat = array_values(array_filter($categories, function($cat) use($category) {

            return ($cat['id'] == $category['parent_id']);
            
        }));

        $parent_cat = $parent_cat[0];

        $level++;

        if ($parent_cat['parent_id'] != 0) {

            return self::getParentCat($categories, $parent_cat, $level);

        } 

        return $level;

    }

    /**
     * Check the post is trashed or not
     *
     * @return bool
     **/
    public static function isTrashed($id)
    {
        return Blog::withTrashed()->find($id)->trashed();
    }

    /**
     * Get count of Trashed Posts
     *
     * @return int
     **/
    public static function trashCount()
    {
        return Blog::onlyTrashed()->count();
    }

}
