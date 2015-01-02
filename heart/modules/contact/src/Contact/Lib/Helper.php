<?php

namespace Contact\Lib;

use Contact\Model\EmailTemplate as Etemplate;
use Reborn\Fileupload\Uploader as Uploader;

class Helper
{
    /**
     * Get Slug For Email Template
     *
     * @return string
     * @package Contact\Lib\Helper
     * @author RebornCMS Development Team
     **/
    public static function getSlug()
    {
        $result = Etemplate::all();
        if (count($result) == 0) {
            return null;
        }
        foreach ($result as $value) {
            $tslug[] = $value->slug;
            $tname[] = $value->name;
        }
        $gslug = array_combine($tslug, $tname);

        return $gslug;
    }

    /**
     * Choose Template Contact
     *
     * @package Contact\Lib\Helper
     * @author RebronCMS Development Team
     **/
    public static function getTemplate($data,$template)
    {
        $t = Etemplate::where('slug', '=', \Setting::get($template))->first();

        if ($t == null) {
            $t = Etemplate::where('slug', '=', 'contact')->first();
        }
        $temp = $t->body;

        $temp = static::decodeHtml($data,$temp);

        return $temp;

    }

    /**
     * Choose Template from Email Template
     *
     * @package Contact\Lib\Helper
     * @author RebronCMS Development Team
     **/
    public static function selectTemplate($data, $slug)
    {
        $t = Etemplate::where('slug', '=', $slug)->first();

        if ($t == null) {
            $t = Etemplate::where('slug', '=', 'contact')->first();
        }
        $temp = $t->body;

        $temp = static::decodeHtml($data, $temp);

        return $temp;
    }

    /**
     * HTML Decode
     *
     * @package Contact\Lib\Helper
     * @author RebronCMS Development Team
     **/
    public static function decodeHtml($data, $form)
    {
        foreach ($data as $key => $value) {
            $form = str_replace('{{'.$key.'}}', $value, $form);
        }

        return html_entity_decode($form);
    }

    /**
     * To upload for Mail Attachment
     *
     * @param  string $name (attachment filed name)
     * @param  array  $ext  (Mime Type)
     * @param  string $path (Upload Folder location)
     * @return array
     * @author RebornCMS Development Team
     **/
    public static function mailAttachment($name, $ext = array(), $path = null)
    {

        if ($path == null) {
            $path = UPLOAD.'contact_attachment/';
        }
        
        $uploader = Uploader::initialize(
                $name,
                array('encName'=>true,'path'=> $path, 'createDir' => true, 'allowedExt'=>$ext)
            );
        

        $uploaded = $uploader->upload();

        if (isset($uploaded['error'])) {
            $result['error'] = $uploaded['error'];
            return $result;

        }
        
        return array('path'=>$path.DS.$uploaded['savedName'],
                     'name'=>$uploaded['savedName'], 
                     'realName' => $uploaded['originName']
                    );
    }

    /**
     * To Get User Group Email
     *
     * @param  string $id (Group Id)
     * @return array
     * @author RebornCMS Development Team
     **/
    public static function getEmail($id)
    {   

        $sentry = new \Cartalyst\Sentry\Sentry;
        $name = $sentry->findGroupById($id);
        $user = \Auth::findAllUsersInGroup($name);
        foreach ($user as $value) {
            $result[] = $value->email;
        }
        return $result;
    }    
}
