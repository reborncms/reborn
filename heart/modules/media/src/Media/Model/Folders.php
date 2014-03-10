<?php

namespace Media\Model;

use Auth;
use Config;

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
     * @param array $data
     *
     * @return Media\Model\Folders
     **/
    public function createFolder($data)
    {

        $folder_id = (empty($data['folder_id'])) ? 0 : $data['folder_id'];
        $name = (empty($data['name'])) 
                ? Config::get('media::media.default_name')
                : $data['name'];

        $name = $this->duplication('name', $name, $folder_id);
        $slug = slug($name);
        $slug = $this->duplication('slug', $slug);

        $this->name = $name;
        $this->slug = $slug;
        $this->description = $data['description'];
        $this->folder_id = $folder_id;
        $this->user_id = Auth::getUser()->id;
        $this->depth = defineDepth($folder_id);

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

        $folder_id = (empty($data['folder_id'])) ? 0 : $data['folder_id'];
        $name = (empty($data['name'])) ? $this->name : $data['name'];
        $name = $this->duplication('name', $name, $folder_id);
        $slug = slug($name);
        $slug = $this->duplication('slug', $slug);

        $this->name = $name;
        $this->slug = $slug;
        $this->description = $data['description'];
        $this->folder_id = $folder_id;
        $this->user_id = Auth::getUser()->id;
        $this->depth = defineDepth($folder_id);

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

        if (0 !== (int) $obj['folder_id']) {
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

    /**
     * Solve name duplication
     *
     * @return String $name renamed
     **/
    protected function duplication($key, $value, $folder = null)
    {

        $name = $value;

        while (static::hasFolder($key, $name, $folder)) {
            $name = increasemental($name, false);
        }

        return $name;

    }

    /**
     * Check folder name or slug has already existed or not
     *
     * @return boolean
     **/
    public function hasFolder($key, $value, $folder = null)
    {

        $ins = new static;

        $result = $ins->where($key, $value);

        $result = (is_null($folder)) 
                    ? $result->where('folder_id', $folder)->first()
                    : $result->first();

        return (is_null($result)) ? false : true;

    }

} // END class MediaFolders
