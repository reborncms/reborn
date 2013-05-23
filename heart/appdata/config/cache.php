<?php

return array(

	/**
	 * Default cache driver. Support driver are [file, db]
	 */
	'default_driver' => 'file',

	/**
	 * File Cache Config
	 *
	 */
	'file' => array(
			'storage_path'	=> STORAGES.'cache'.DS,
		),

);
