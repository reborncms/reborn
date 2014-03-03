<?php

namespace Navigation\Model;

class NavigationLinks extends \Eloquent
{
    protected $table = 'navigation_links';

    public $timestamps = false;

    protected $multisite = true;

    public function updateLink($current, $groupChange = false)
    {
        if ($groupChange) {
            $this->updateParent($current, $groupChange);
        }

        return $this->save();
    }

    public function updateParent($current = null, $group_change = false)
    {
        $current_group = is_null($current)
                        ? $this->getAttribute('navigation_id')
                        : $current;

        $last = static::getlinkOrder($current);

        $instance = new static;

        $instance->newQuery()
                ->where('parent_id', '=', $this->getAttribute('id'))
                ->update(array('link_order' => $last, 'parent_id' => 0));
    }

    public static function getLinkOrder($group)
    {
        $instance = new static;
        $last = $instance->newQuery()->where('navigation_id', '=', $group)
                ->orderBy('link_order', 'desc')->take(1)->get();

        if (isset($last[0])) {
            return $last[0]->attributes['link_order'] + 1;
        }

        return 1;
    }

    public static function setTheChild($link)
    {
        $instance = new static;

        if (isset($link['children'])) {
            foreach ($link['children'] as $k => $child) {
                $instance->newQuery()
                        ->where('id', '=', $child['id'])
                        ->update(array('link_order' => $k, 'parent_id' => $link['id']));

                if (isset($child['children'])) {
                    self::setTheChild($child);
                }
            }
        }
    }

}
