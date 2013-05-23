<?php

namespace Contact\Lib;

use Contact\Model\EmailTemplate as Etemplate;

class Helper {

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
		    $t = Etemplate::where('slug','=' ,'contact')->first();
       	}
        $temp = $t->body;
       	
        foreach ($data as $key => $value) {
        	$temp = str_replace('{{'.$key.'}}', $value, $temp);
        }
        
        return html_entity_decode($temp);
	}
}