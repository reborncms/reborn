<?php

namespace Pages\Model;

class Pages extends \Eloquent
{
    protected $table = 'pages';

    protected $multisite = true;

    protected $rules = array(
        'title' => 'required|maxLength:225',
        'slug' => 'required|maxLength:225'
    );

    public static function pageStructure($no_draft = false)
    {
        $ins = new static();

        if ($no_draft == true) {
            $all = $ins->where('status', 'live')->orderBy('page_order')->get();
        } else {
            $all = $ins->orderBy('page_order')->get(); // add order_by
        }

        $page_structure = $pages = array();
        
        foreach ($all as $row) {
            $pages[$row->id] = $row->toArray();
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
    public static function getParentUri($id)
    {
        $ins = new static();

        $query = $ins->where('id', '=', $id)->pluck('uri');

        return $query;
    }

    public function getPageBodyAttribute() 
    {
        return (isset($this->attributes['content'])) ? $this->attributes['content'] : '';
    }


}
