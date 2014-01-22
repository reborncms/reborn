<?php

namespace Media\Model;

use Auth;

/**
 * Model for Media Module which served CURD with media_folders table.
 *
 * @package Media\Model
 * @author RebornCMS Development Team
 **/
class Folders extends \Eloquent
{
	/**
	 * Table name
	 *
	 * @access protected
	 * @var string
	 **/
    protected $table = 'media_folders';

    protected $multisite = true;

    /**
     * Save new folder data to database
     *
     * @return Media\Model\Folders
     **/
    public function createFolder($data)
    {
    
        $name = duplicate($data['name']);

        $this->name = $name;
        $this->slug = slug($name);
        $this->description = $data['description'];
        $this->folder_id = $data['folder_id'];
        $this->user_id = Auth::getUser()->id;
        $this->depth = defineDepth($data['folder_id']);


        if ($this->save()) {
            return $this;
        }

        return false;

    }

    /**
     * Update folder data
     *
     * @return Media\Model\Folders
     **/
    public function updateFolder($data)
    {

        $name = duplicate($data['name'], 'folder', $this->name);

        $this->name = $name;
        $this->slug = slug($name);
        $this->description = $data['description'];
        $this->folder_id = $data['folder_id'];
        $this->user_id = Auth::getUser()->id;
        $this->depth = defineDepth($data['folder_id']);


        if ($this->save()) {
            return $this;
        }

        return false;

    }

    /**
     * Child files' data
     *
     * @return Media\Model\Files
     **/
    public function files()
    {

        return $this->hasMany('Media\Model\Files', 'folder_id');

    }

    /**
     * Folder creater's data
     *
     * @return object User\Model\User
     **/
    public function user()
    {
        return $this->belongsTo('Reborn\Auth\Sentry\Eloquent\User');
    }

    /**
     * Find parent folder
     *
     * @return object Media\Model\Folders
     **/
    public function parent()
    {
        return $this->belongsTo('Media\Model\Folders', 'folder_id', 'id');
    }

    /**
     * Find child Folders
     *
     * @return object Media\Model\Folders
     **/
    public function children()
    {
        return $this->hasMany('Media\Model\Folders', 'folder_id', 'id');
    }

    /**
     * Getting folder tree
     *
     * @param int $id
     *
     * @return array $ids
     **/
    public function folderTreeIds($id)
    {
    	$result = static::find($id);

    	$ids = array();

    	$ids[] = $result->id;

    	if ($result->children) {
            $ids = $this->findChild($ids, $result->children);
        }

        return $ids;
    }

    public function pagination($id)
    {
        $result = static::find($id);

        $parents = array();

        $paretns[] = $result;

        $res = $this->findParent($result);

        return array_flatten($res);
    }

    public function findParent(&$obj)
    {
        $i = 0;
        $a = array();

        if (0 !== (int)$obj['folder_id']) {
            $test = static::find($obj['folder_id']);

            $a[] = $test;

            $a[] = $this->findParent($test);
        }

        return $a;
    }

    public function findChild(&$id, $qw)
    {
        foreach ($qw as $q) {

            if ($q->children) {
                $id[] = $q->id;
                $this->findChild($id, $q->children);
            } else {
               $id[] = $q->id;
            }
        }
        

        return $id;
    }

    /**
     * Get 
     *
     * @return void
     * @author 
     **/
    protected function childFile()
    {

        return $this->hasMany('Media\Model\Files', 'folder_id', 'id');

    }

    /**
     * undocumented function
     *
     * @return void
     * @author 
     **/
    public function childFolder()
    {

        return $this->hasMany('Media\Model\Folders', 'folder_id', 'id');

    }

} // END class MediaFolders
