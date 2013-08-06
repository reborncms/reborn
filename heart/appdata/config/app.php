<?php

return array(

	/**
	 * Default Language for Reborn CMS
	 */
	'lang' => function() {
				return \Setting::get('default_language', 'en');
			},

	/**
	 * Supported Language for Reborn CMS
	 * Now only support english only
	 *
	 */
	'support_langs' => array(
			'en' => 'en',
			'my' => 'my'
		),

	/**
	 * Default Timezone for Reborn CMS
	 */
	'timezone' => 'UTC',

	/**
	 * Default Locale for Reborn CMS
	 */
	'locale' => 'en',

	/**
	 * Fallback Locale for Reborn CMS
	 * Fallback locale is use on default locale is does not work.
	 * Don't change this locale!
	 *
	 */
	'fallback_locale' => 'en',

	/**
	 * Character Encoding for Reborn CMS
	 */
	'encoding' => 'UTF-8',

	/**
	 * Config Values for Logging process
	 */
	'log' => array(

			// Path for saving log file
			'path' => STORAGES.'logs'.DS,

			// log file name use at saving process (eg: rebornlog-20121216.log)
			'file_name' => 'rebornlog-'.Date('Ymd'),

			// Log file extension (default is .log). Don't forget "dot"!
			'ext' => '.log',
		),

	/**
	 * Config for the security
	 */
	'security' => array(
		'csrf_key' => 'csrf_token_key',
		'csrf_expiration' => 10000,
		'token_encrypt' => 'md5',
	),

	/**
	 * Config key for the Module
	 */
	'module' => array(
			'cores' => array(
					'Admin', 'Blog', 'Comment', 'Contact', 'Media', 'Module',
					'Navigation', 'Pages', 'Setting', 'Tag', 'User'
				),
			'system' => array('Admin', 'Pages', 'Navigation', 'User', 'Module', 'Setting'),

			'autoload' => array('User', 'Navigation'),
		),

	/**
	 * Gate of the admin panel link
	 *
	 */
	'adminpanel' => 'admin',

);
