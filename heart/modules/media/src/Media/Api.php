<?php

namespace Media;

use Media\Model\Files;
use Media\Model\Folders;

/**
 * Media File Api Class
 *
 * @package Media
 * @author MyanmarLinks Professional Web Development Team
 **/
class Api
{

	/**
	 * Make Media\Api instance with static method
	 *
	 * @return \Media\Api
	 **/
	public static function make()
	{
		return new static();
	}

	/**
	 * Get all folder lists.
	 *
	 * @return array
	 **/
	public function folders()
	{
		return Folders::all()->toArray();
	}

	/**
	 * Get folder's data by id.
	 *
	 * @param integer $id
	 * @return array
	 **/
	public function folder($id)
	{
		if (! is_null($folder = Folders::find($id)) ) {
			return $folder->toArray();
		}

		return array();
	}

	/**
	 * Get all folder lists with tree.
	 *
	 * @return array
	 **/
	public function treeFolders()
	{
		// Use tree_lists helper function from helpers.php
		return tree_lists($this->folders(), 'folder_id');
	}

	/**
	 * Get all folder lists with tree.
	 *
	 * @return array
	 **/
	public function treeListForSelect($empty = null)
	{
		$lists = array();

		if (! is_null($empty) ) {
			$lists[] = '-- '.$empty.' --';
		}

		foreach ($this->folders() as $folder) {
			$lists[$folder['id']] = str_repeat('&nbsp;&#187;&nbsp;', $folder['depth']) . $folder['name'];
		}

		return $lists;
	}

	/**
	 * Get folder lists and file lists from given folder ID
	 *
	 * @param integer $id Folder ID
	 * @return array
	 **/
	public function folderData($id = 0)
	{
		if ( is_null($folder = Folders::find($id)) ) {
			return array();
		}

		return $folder->toArray();
	}

	/**
	 * Get image files data.
	 *
	 * @param integer $folder_id
	 * @param integer $limit
	 * @param integer|null $offset
	 * @param string|null $type Image mime type
	 * @return array
	 **/
	public function images($folder_id = 0, $limit = 20, $offset = null, $type = null)
	{
		$type = $this->getImageType($type);

		$files = Files::whereIn('mime_type', $type)
						->where('folder_id', (int) $folder_id)
						->skip($offset)
						->orderBy('created_at', 'desc');

		if (! is_null($limit) ) {
			$files->take($limit);
		}

		$all = $files->get();

		if ($all->isEmpty()) {
			return array();
		}

		return $all->toArray();
	}

	/**
	 * Get Image Mine Type for Filter Query
	 *
	 * @param string|null $type
	 * @return array
	 **/
	protected function getImageType($type)
	{
		switch (strtolower($type)) {
			case 'jpg':
			case 'jpeg':
				return array('image/jpeg');
				break;

			case 'png':
				return array('image/png');
				break;

			case 'gif':
				return array('image/gif');
				break;

			case 'tiff':
				return array('image/tiff');
				break;

			default:
				return array('image/jpeg', 'image/png', 'image/gif', 'image/tiff');
				break;
		}
	}

	/**
	 * Get files by a specific folder
	 *
	 * @param mix $id Id or slug of folder
	 *
	 * @return array
	 **/
	public function getFilesByFolder($id)
	{

		$folder = (is_numeric($id)) ? Folders::find($id) : 
					Folders::where('slug', $id)->first();

		return (is_null($folder)) ? array() : $folder->files->toArray();

	}

	/**
	 * Get folders by a specific folder
	 *
	 * @param mix $id Id or slug of folder
	 *
	 * @return array
	 **/
	public function getFoldersByFolder($id)
	{

		$foler = (is_numeric($id)) ? Folders::find($id) : 
					Folders::where('slug', $id)->first();

		return (is_null($folder)) ? array() : $folder->children->toArray();

	}

} // END class Api
