<?php

namespace Media\Model;

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

    /**
     * Parent folder's data
     *
     * @return object Media\Model\Folders
     **/
    public function folder()
    {
        return $this->belongsTo('Media\Model\Folders');
    }

    /**
     * Folder creater's data
     *
     * @return object User\Model\User
     **/
    public function user()
    {
        return $this->belongsTo('User\Model\User');
    }

    /**
     * Find parent folder
     *
     * @return object Media\Model\Folders
     **/
    public function parent()
    {
        return $this->hasOne('Media\Model\Folders', 'folder_id');
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
        }/* else {
            $a[] = $obj->id;
        }*/

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

} // END class MediaFolders
