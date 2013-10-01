<?php

namespace Pages\Lib;

use Pages\Model\Pages;

class Helper
{
    protected static $level = 1;

    protected static $inc_count = 0;

    protected static $page_list;

    public static function page_structure($page)
    {
        $ps = '';
        /*if (isset($page['children'])) {
            $ps .= '<i class="icon-circleplus icon-grey page_expand"></i>';
        } else {
            $ps .= '<i class="page_expand"></i>';
        }*/
        $ps .= '<div class="draggable_wrap">
                    <div class="index_page_title">';
        $ps .= 	$page['title'];

        if ($page['status'] == 'draft') {
            $ps .= '<a href="'.adminUrl('pages/status/'.$page['id']).'" style="cursor:pointer;">';
            $ps .= '<span class="label label-info">'.t('global.draft_label').'</span>';
        }
        $ps .= '<div class="page_actions">';
        $ps .= '<a href="'.rbUrl('pages/preview/'.$page['uri']).'" title="'.t('global.view').'" class="tipsy-tip" target="_blank"><i class="icon-view icon-black"></i></a>';

        if (user_has_access('pages.create')) {
             $ps .= '<a href="'.adminUrl('pages/duplicate/'.$page['id']).'" title="'.t('pages::pages.labels.page_duplicate').'" class="tipsy-tip"><i class="icon-copy icon-black"></i></a>';   
        }

        if (user_has_access('pages.edit')) {
            $ps .= '<a href="'.adminUrl('pages/edit/'.$page['id']).'" title="'.t('global.edit').'" class="tipsy-tip"><i class="icon-edit icon-black"></i></a>';
        }

        if (user_has_access('pages.delete') && $page['id'] != 1) {
            $ps .= '<a href="'.adminUrl('pages/delete/'.$page['id']).'" title="'.t('global.delete').'" class="confirm_delete tipsy-tip"><i class="icon-remove icon-black"></i></a>'; 
        }
        
        $ps .= '</div>';
        $ps .= '</div>
                    </div>';

        return $ps;
    }

    public static function changeAuthor($author_id)
    {
        $page_update = Pages::where('author_id', $author_id)->update(array('author_id' => 1));

        if ($page_update) {
            return true;
        } else {
            return false;
        }
    }

    public static function generate_children($children)
    {
        $gc = '';
        $gc .= '<ol>';
        foreach ($children as $page) {
            $gc .= '<li id="page_'.$page['id'].'">';
            $gc .= self::page_structure($page);
            if (isset($page['children'])) {
                $gc .= self::generate_children($page['children']);
            }
            $gc .= '</li>';
        }
        $gc .= '</ol>';

        return $gc;
    }

    public static function pageList()
    {
        $all_page = Pages::page_structure(true);
        foreach ($all_page as $page) {
            static::$page_list[$page['uri']] = $page['title'];
            if (isset($page['children'])) {
                static::$level = 1;
                self::childPage($page['children'], 1);
            }
        }
        return static::$page_list;
    }

    protected static function childPage($children, $lvl = 0)
    {
        foreach ($children as $page) {
            static::$page_list[$page['uri']] = str_repeat('>', $lvl). ' ' . $page['title'];
            if(isset($page['children'])) {
                static::$level++;
                static::$inc_count++;
                self::childPage($page['children'], static::$level);
            } else {
                static::$level = static::$level - static::$inc_count;
                static::$inc_count = 0;
            }
        }
    }

}
