<?php

namespace Media\Model;

/**
 * Model for Media Module which served CRUD with media_files table.
 *
 * @package Media\Model
 * @author RebornCMS Development Team
 **/
class MediaFiles extends \Eloquent
{

    protected $table = 'media_files';

    public function folder()
    {
        return $this->belongsTo('Media\Model\MediaFolders');
    }

    public function user()
    {
        return $this->belongsTo('User\Model\User');
    }



} // END class MediaFiles
