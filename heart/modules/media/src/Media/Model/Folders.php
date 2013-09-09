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

    protected $table = 'media_folders';

    public function folder()
    {
        return $this->belongsTo('Media\Model\Folders');
    }

    public function user()
    {
        return $this->belongsTo('User\Model\User');
    }

} // END class MediaFolders
