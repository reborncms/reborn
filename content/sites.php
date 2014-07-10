<?php

/*
|--------------------------------------------------------------------------
| Site Management for Reborn CMS
|--------------------------------------------------------------------------
|
| This file is multi site configuration data.
| If you want to use multisite with Reborn,
| (1) enable "multi_site"
| (2) set content folder path "content_path"
| (3) set database prefix name "prefix"
|
*/

return array(

	/**
	 * Use Multisite at Reborn CMS
	 *
	 * Default value of this vlaue is "false".
	 * If you want to use multisite, set "true".
	 */
	'multi_site' => false,

	/**
	 * You can set your another site with domain and folder name.
	 * Doesn't need to add (www.)
	 * <code>
	 * 'dev.reborncms.com' => 'reborn_dev',
	 * 'test.reborncms.com' => 'reborn_test',
	 * 'anothersite.com' => 'anothersite'
	 * </code>
	 */
	'content_path' => array(
		//'test.reborn.dev' => 'reborn_test',
	),

	/**
	 * Database Prefix name for domain.
	 * Doesn't need to add underscore(_).
	 *
	 */
	'prefix' => array(
		//'test.reborn.dev' => 'mts'
	),
);

/* End of sites.php */
