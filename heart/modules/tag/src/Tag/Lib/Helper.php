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

	/**
	 * Import Tags
	 *
	 * @return void
	 **/
	public static function import($object_id, $object_name, $tags = '')
	{
		if (!empty($tags)) {
			$prev_tags = TagsRelationship::with('tag')
							->where('object_id', '=', $object_id)
							->where('object_name', '=', $object_name)
							->get();
			$i = 0;
			foreach ($prev_tags as $tag) {
				$pre_tag_name[] = $tag->tag->name;
				$i++;
			}

			$tags = explode(',', $tags);

			if (isset($pre_tag_name)) {
				$deleted = array_diff($pre_tag_name, $tags);

				if (!empty($deleted)) {
					foreach ($prev_tags as $tag) {
						if (in_array($tag->tag->name, $deleted)) {
							TagsRelationship::where('tag_id', '=', $tag->tag->id)
												->where('object_id', '=', $object_id)
												->where('object_name', '=', $object_name)
												->delete();
						}
					}
				}
			}

			foreach ($tags as $tag) {

				if (strlen($tag) == strlen(utf8_decode($tag))) {		
					$tag = strtolower(preg_replace("/[^a-zA-Z 0-9-]/", "", $tag));
				}

				$tag = str_replace(" ", "-", $tag);

				$result = Tag::where('name', '=', $tag)->first();

				if(count($result) > 0) {
					$tag_id = $result->id;
				} else {
					$tag_insert = Tag::create(array('name' => $tag));
					$tag_id = $tag_insert->id;
				}
				$has_tag = TagsRelationship::where('tag_id', '=', $tag_id)
												->where('object_id', '=', $object_id)
												->where('object_name', '=', $object_name)
												->count();
				
				if ($has_tag == 0) {
					TagsRelationship::create(array(
							'tag_id' => $tag_id,
							'object_id' => $object_id,
							'object_name' => $object_name
					));
				}
			}
		} else {
			return '';
		}

	}

}
