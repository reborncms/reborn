<?php

namespace Blog\Model;

class BlogCategory extends \Eloquent
{
    protected $table = 'blog_categories';

    public $timestamps = false;

    protected $multisite = true;

    protected static $cat = array();

    public static function cat_stucture()
    {
        $all = \DB::table('blog_categories')->orderBy('order')->get();

        foreach ($all as $row) {
            $cat[$row->id] = (array) $row;
        }

        unset($all);

        foreach ($cat as $row) {
            if (array_key_exists($row['parent_id'], $cat)) {
                $cat[$row['parent_id']]['children'][] =& $cat[$row['id']];
            }
            if ($row['parent_id'] == 0) {
                $cat_structure[] =& $cat[$row['id']];
            }
        }

        return $cat_structure;
    }

    public static function getCatIds($slug)
    {
        $cat = \DB::table('blog_categories')->where('slug', $slug)->pluck('id');

        static::$cat[] = $cat;

        if (!$cat) {
            return false;
        }

        $child_cats = \DB::table('blog_categories')->select('id')->where('parent_id', $cat)->get();

        if (!empty($child_cats)) {
            self::getChildren($child_cats);
        }

        return static::$cat;
    }

    protected static function getChildren($child_cats)
    {
        foreach ($child_cats as $cat) {

            static::$cat[] = $cat->id;
            $s_child = \DB::table('blog_categories')->select('id')->where('parent_id', $cat->id)->get();
            if (!empty($s_child)) {
                self::getChildren($s_child);
            }
        }
    }

}
