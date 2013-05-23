<?php

namespace User\Model;

class PermissionModel
{
	protected static $table = 'modules';

	private $_groups = array();

	/**
	 * Get all modules from modules table
	 * 
	 * @return	array
	 */
	public static function getall()
	{
		$db_modules = \DB::table(self::$table)->where('name', '!=', 'Admin')->get();

		return $db_modules;
	}
}
