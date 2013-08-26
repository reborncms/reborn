<?php

namespace Tag\Lib;

use Tag\Model\Tag;
use Tag\Model\TagsRelationship;

class Helper {

	/**
	 * Get Tag from other module
	 *
	 * @return void
	 **/
	public static function getTags($object_id, $object_name, $format = 'string')
	{
		$tags = TagsRelationship::with('tag')
							->where('object_id', '=', $object_id)
							->where('object_name', '=', $object_name)
							->get();
		if (count($tags) > 0) {
			foreach ($tags as $tag) {
				$obj_tag[] = $tag->tag->name;
			}
			if ($format == 'string') {
				$tags = implode(', ', $obj_tag);
			} else {
				$tags = $obj_tag;
			}
		} else {
			if ($format == 'string') {
				$tags = '';
			} else {
				$tags = array();
			}
		}

		return $tags;
	}

	public static function getObjectsCount($tag, $object_name)
	{
		$tag_id = Tag::where('name', $tag)->pluck('id');
		$obj_count = TagsRelationship::where('tag_id', '=', $tag_id)
							->where('object_name', '=', $object_name)
							->count();
		return $obj_count;
	}

	public static function getObjectIds($tag, $object_name)
	{
		$object_ids = array();
		$tag_id = Tag::where('name', $tag)->pluck('id');
		if ($tag_id == null) {
			return false;
		}
		$relations = TagsRelationship::where('tag_id', '=', $tag_id)
							->where('object_name', '=', $object_name)
							->get();
		foreach ($relations as $relation) {
			$object_ids[] = $relation->object_id;
		}
		return $object_ids;
	}

}
