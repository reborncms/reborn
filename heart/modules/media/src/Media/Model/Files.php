<?php

namespace Media\Model;

/**
 * Model for Media Module which served CRUD with media_files table.
 *
 * @package Media\Model
 * @author RebornCMS Development Team
 **/
class Files extends \Reborn\MVC\Model\Search
{

    protected $table = 'media_files';

    public function folder()
    {
        return $this->belongsTo('Media\Model\Folders');
    }

    public function user()
    {
        return $this->belongsTo('User\Model\User');
    }

    public function getThumbAttribute()
    {
    	$ext = $this->attributes['extension'];

		switch ($ext) {
			case 'pdf':
				$thumb = 'pdf-thumb';
				break;

			case 'zip':
			case 'tar':
			case 'gz':
			case 'rar':
			case '7zip':
				$thumb = 'zip-thumb';
				break;

			case 'mp3':
			case 'wma':
			case 'ogg':
				$thumb = 'audio-thumb';
				break;

			case 'wmv':
			case 'mp4':
			case 'flv':
			case 'ogv':
			case 'avi':
				$thumb = 'vdo-thumb';
				break;

			case 'txt':
			case 'rtf':
				$thumb = 'txt-thumb';
				break;

			case 'doc':
			case 'docx':
				$thumb = 'doc-thumb';
				break;

			default:
				$thumb = '';
				break;
		}

		return $thumb;
    }



} // END class MediaFiles
