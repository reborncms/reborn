<?php

namespace Contact\Lib;

use Reborn\Fileupload\Uploader as Uploader;
use Contact\Model\EmailTemplate as Etemplate;
use Contact\Model\Mail as Mail;

/**
 * Contact Helper
 *
 * @package Contact\Lib\Helper
 * @author RebornCMS Development Team
 */
class Helper
{
    /**
     * Get Slug For Email Template
     *
     * @return array
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
     * Choose Template for Contact
     *
     * @param array $data [data for mail body]
     * @param string $template [template name]
     * @return string [mail body]
     * @package Contact\Lib\Helper
     * @author RebronCMS Development Team
     **/
    public static function getTemplate($data,$template)
    {
        $t = Etemplate::where('slug', '=', \Setting::get($template))->first();

        if ($t == null) {
            $t = Etemplate::where('id', '=', 2)->first();
        }
        $temp = $t->body;

        $temp = static::decodeHtml($data,$temp);

        return $temp;

    }

    /**
     * Choose Template for Send Mail
     *
     * @param array $data [data for mail body]
     * @param string $slug [template name slug]
     * @return string [mail body]
     * @package Contact\Lib\Helper
     * @author RebronCMS Development Team
     **/
    public static function selectTemplate($data, $slug)
    {
        $t = Etemplate::where('slug', '=', $slug)->first();

        if ($t == null) {
            $t = Etemplate::where('id', '=', 1)->first();
        }

        $temp = $t->body;

        $temp = static::decodeHtml($data, $temp);

        return $temp;
    }

    /**
     * HTML Decode
     *
     * @param array $data [mail input]
     * @param string $form [email template body]
     * @return string
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
     * @package Contact\Lib\Helper
     * @author RebornCMS Development Team
     **/
    public static function mailAttachment($name, $ext = array())
    {
        
        $uploader = Uploader::initialize(
                $name,
                array('encName'=>true,'path'=> \Config::get('contact::contact.attachment_path'), 'createDir' => true, 'allowedExt'=>$ext)
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
        $result = array();
        $sentry = new \Cartalyst\Sentry\Sentry;

        $name = $sentry->findGroupById($id);

        $user = \Auth::findAllUsersInGroup($name);

        foreach ($user as $value) {

            $result[] = $value->email;

        }

        return $result;
    }

    /**
     * Get All User Group For Sending Mail By Admin
     * 
     * @return array [User Group Array]
     * @package Contact\Lib\Helper
     * @author RebornCMS Development Team
     */
    public static function getUserGroup()
    {
        $group = \UserGroup::all();

        $userGroup = array('0'=>'Select User Group');

        foreach ($group as $value) {

            $userGroup[$value->id] = $value->name;
            
        }

        return $userGroup;
    }

    /**
     * Check Slug Duplicate
     * @param  string $slug
     * @param  string $id
     * @return boolean
     */
    public static function slugDuplicateCheck($slug, $id)
    {
        $check = Etemplate::where('slug', $slug)
                    ->where('id', '!=', $id)
                    ->get();
        if (count($check)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * get file from mail
     * @param  int $id [mail id]
     * @return array
     */
    public static function getAttachment($id)
    {
        
        $mail = Mail::find($id);

        if (! is_null($mail) ) {
             $path = \Config::get('contact::contact.attachment_path'). DS .
                    $mail->attachment;

            if (\File::is($path)) {
                return array($path,$mail->attachment_name);
            }
        }
        return null;
    }
}
