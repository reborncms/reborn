<?php

namespace Pages\Model;

class Pages extends \Eloquent
{
    protected $table = 'pages';

    public static function page_structure($no_draft = false)
    {
        if ($no_draft == true) {
            $all = \DB::table('pages')->where('status', 'live')->orderBy('page_order')->get();
        } else {
            $all = \DB::table('pages')->orderBy('page_order')->get(); // add order_by
        }

        foreach ($all as $row) {
            $pages[$row->id] = (array) $row;
        }

        unset($all);

        foreach ($pages as $row) {
            if (array_key_exists($row['parent_id'], $pages)) {
                $pages[$row['parent_id']]['children'][] =& $pages[$row['id']];
            }
            if ($row['parent_id'] == null) {
                $page_structure[] =& $pages[$row['id']];
            }
        }

        return $page_structure;
    }

    /**
     * Get Parent Uri
     *
     * @return void
     **/
    public static function get_parent_uri($id)
    {
        $query = \DB::table('pages')->where('id', '=', $id)->pluck('uri');

        return $query;
    }
}
