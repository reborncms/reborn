<?php

namespace Reborn\Pagination;

use Reborn\Cores\Facade as Base;

/**
 * Pagination Facade Class
 *
 * @package Reborn\Pagination
 * @author MyanmarLinks Professional Web Development Team
 **/
class PaginationFacade extends Base
{
	protected static $pagi;

	/**
	 * Create new pagination instance
	 *
	 * @param array $options
	 * @return \Reborn\Pagination\BuilderInterface
	 **/
	public static function create($options = array())
	{
		$ins = static::getInstance();

		if ( ! is_null($options) ) {
			$ins->options($options);
		}

		return $ins;
	}

	protected static function getInstance()
	{
		if (is_null(static::$pagi)) {
			static::$pagi = new \Reborn\Pagination\Builder(static::$app);
		}

		return static::$pagi;
	}

} // END class Facade extends Base
