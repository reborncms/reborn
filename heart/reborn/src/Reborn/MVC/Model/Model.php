<?php

namespace Reborn\MVC\Model;

use Reborn\Connector\DB\Manager as DB;

/**
 * Model Class for Reborn
 * Model Class will include some of basic CRUD method.
 *
 * @package Reborn\MVC\Model
 * @author Myanmar Links Professional Web Development Team
 **/
class Model
{
    protected static $table = null;

    protected static $pk = 'id';

    public static function get($id)
    {
        $result = DB::table(static::$table)->where(static::$pk, '=', $id)->get();
        return (count($result) > 0) ? $result[0] : array();
    }

    public static function get_by($key, $value)
    {
        $result = DB::table(static::$table)->where($key, '=', $value)->get();
        return (count($result) > 0) ? $result[0] : array();
    }

    public static function get_all($limit, $offset)
    {

    }

    public static function get_all_by($where, $limit, $offset)
    {

    }

} // END class Model
