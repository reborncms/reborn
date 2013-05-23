<?php

return array(

	/**
	 * Cache File Path for the template
	 */
	'cache_path' => STORAGES.'template'.DS,

	/**
	 * Template cache file's lifetime.
	 * Default is 0. Defination of 0 is no expire time.
	 * Time format is minute. (eg: 60) is equal 1hour
	 */
	'cache_lifetime' => 0,

	/**
	 * File Extension for the template view file
	 * Default extension is '.html'.
	 * Don't forget the '.' dot operator
	 */
	'template_extension' => '.html',

);
