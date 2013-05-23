<?php

return array(

	/**
	 * Active (Default) Connection Config name
	 */
	'active' => 'default',

	/**
	 * Default DB Connection Config
	 *
	 */
	'default' => array(
			// Database Driver name (support driver : mysql, pgsql, sqlite, sqlsrv)
			// But Reborn is testing only mysql
			'driver'	=> 'mysql',
			'host'		=> '{HOST}',
			'database'	=> '{DB}',
			'username'	=> '{USER}',
			'password'	=> '{PASSWORD}',
			'port'		=> {PORT},
			'charset'	=> 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'	=> ''
		),

);
