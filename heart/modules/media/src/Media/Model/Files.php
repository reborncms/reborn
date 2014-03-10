<?php

namespace Media\Model;

use Auth;
use File;


/**
 * Model for Media Module which served CRUD with media_files table.
 *
 * @package Media\Model
 * @author RebornCMS Development Team
 **/
class Files extends \Reborn\MVC\Model\Search
{

    /**
     * Database table name
     *
     * @var string
     **/
    protected $table = 'media_files';

    /**
     * Allow multisite
     *
     * @var boolean
     **/
    protected $multisite = true;

    /**
     * Reation to folder table
     *
     * @return Media\Model\Folders
     **/
    public function folder()
    {
        return $this->belongsTo('Media\Model\Folders');
    }

    /**
     * Relation to user table
     *
     * @return Reborn\Auth\Sentry\eloquent\User
     **/
    public function user()
    {
        return $this->belongsTo('Reborn\Auth\Sentry\Eloquent\User');
    }

    public function scopeImageOnly($query)
    {
        $mime_type = array(
                'image/bmp',
                'image/cgm',
                'image/g3fax',
                'image/gif',
                'image/ief',
                'image/jpeg',
                'image/ktx',
                'image/png',
                'image/prs.btif',
                'image/sgi',
                'image/svg+xml',
                'image/tiff',
                'image/vnd.adobe.photoshop',
                'image/vnd.dece.graphic',
                'image/vnd.dvb.subtitle',
                'image/vnd.djvu',
                'image/vnd.dwg',
                'image/vnd.dxf',
                'image/vnd.fastbidsheet',
                'image/vnd.fpx',
                'image/vnd.fst',
                'image/vnd.fujixerox.edmics-mmr',
                'image/vnd.fujixerox.edmics-rlc',
                'image/vnd.ms-modi',
                'image/vnd.ms-photo',
                'image/vnd.net-fpx',
                'image/vnd.wap.wbmp',
                'image/vnd.xiff',
                'image/webp',
                'image/x-3ds',
                'image/x-cmu-raster',
                'image/x-cmx',
                'image/x-freehand',
                'image/x-icon',
                'image/x-mrsid-image',
                'image/x-pcx',
                'image/x-pict',
                'image/x-portable-anymap',
                'image/x-portable-bitmap',
                'image/x-portable-graymap',
                'image/x-portable-pixmap',
                'image/x-rgb',
                'image/x-tga',
                'image/x-xbitmap',
                'image/x-xpixmap',
                'image/x-xwindowdump',
            );

        return $query->whereIn('mime_type', $mime_type);
    }

    /**
     * Used to determine which thumbnail preview must be shown
     *
     * @return String $thumb css class name for thumbnail preview
     **/
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

    /**
     * Save new file data
     *
     * @return
     **/
    public function saveFile($data)
    {

        $this->name = $data['originBaseName'];
        $this->alt_text = $data['originBaseName'];
        $this->description = null;
        $this->folder_id = $data['folder_id'];
        $this->user_id = Auth::getUser()->id;
        $this->filename = $data['savedName'];
        $this->filesize = $data['fileSize'];
        $this->extension = $data['extension'];
        $this->mime_type = $data['mimeType'];
        $this->width = $data['width'];
        $this->height = $data['height'];

        if ($this->save()) {
            return $this;
        }

        return false;

    }

    /**
     * Update file data
     *
     * @return void
     **/
    public function updateFile($data)
    {
        $name = (empty($data['name'])) ? $this->name : $data['name'];
        $folder_id = (empty($data['folder_id'])) ? 0 : $data['folder_id'];

        $this->name = duplicate($name);
        $this->alt_text = $data['alt_text'];
        $this->description = $data['description'];
        $this->folder_id = $folder_id;

        if ($this->save()) {
            return $this;
        }

        return false;

    }

    /**
     * Check the given filename has already existed or not
     *
     * @param String $filename Filename to be check
     *
     * @return boolean
     **/
    public static function hasFile($filename)
    {

        $ins = new static;

        $result = $ins->where('filename', $filename)->first();

        return (is_null($result)) ? false : true;

    }

    /**
     * Deleting file including physical files
     *
     * @return void
     **/
    public function deleteFile()
    {

        $path = UPLOAD . date('Y', strtotime($this->created_at)).DS
                .date('m', strtotime($this->created_at)) . DS  . $this->filename;

        File::delete($path);

        $this->delete();

    }

} // END class MediaFiles
