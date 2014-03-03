<?php

namespace Reborn\Connector\DB;

use Reborn\Connector\DB\DBManager as DB;

/**
 * Schema Class is Bridge between Reborn and Illuminate's Schema.
 *
 * @package Reborn\Connector\DB
 * @author Myanmar Links Professional Web Development Team
 **/
class Schema
{
    public static function __callStatic($method, $params = array())
    {
        $args = $params;
        $schemaBuilder = DB::getSchemaBuilder();

        return call_user_func_array(array($schemaBuilder, $method), $args);
    }

} // END class Schema
