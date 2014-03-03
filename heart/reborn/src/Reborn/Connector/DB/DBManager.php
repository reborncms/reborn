<?php

namespace Reborn\Connector\DB;

use Reborn\Event\EventManager as Event;
use Reborn\Config\Config;
use Illuminate\Container\Container;
use Illuminate\Database\Connectors\ConnectionFactory as ConnectionFactory;
use Illuminate\Database\ConnectionResolver as Resolver;

/**
 * DB Manager class for Reborn to connect Illuminate Database.
 * Special thank for Illuminate Database.
 *
 * @package Reborn\Connector\DB
 * @author Myanmar Links Professional Web Development Team
 **/
class DBManager
{
    protected static $connections = null;

    protected static $eloquentIsSet = false;

    protected static $app;

    /**
     * Initialize the DB class
     *
     * @return void
     **/
    public static function initialize($app, $name = null)
    {
        static::$app = $app;

        Event::call('reborn.db.initialize');

        if (is_null($name)) {
            $name = static::getDefaultConnectionName();
        }

        if (! isset(static::$connections[$name])) {
            new static($name);
        }
    }

    /**
     * Constructor Method for DB Class
     *
     * @param  string                          $name Name of db config. But this is optional.
     * @return \Illuminate\Database\Connection
     */
    public function __construct($name = null)
    {
        $config = $this->getConfig($name);

        if (! isset(static::$connections[$name])) {
            $conn = new ConnectionFactory(new Container);
            $connection = $conn->make($config);
            $connection->setFetchMode(\PDO::FETCH_OBJ);

            static::$connections[$name] = $connection;
        }

        if (! static::$eloquentIsSet) {
            static::setEloquent();
        }

        return static::$connections[$name];
    }

    /**
     * Set the Eloquent ORM
     */
    public static function setEloquent()
    {
        $resolver = new Resolver(static::$connections);
        $resolver->setDefaultConnection(static::getDefaultConnectionName());
        \Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);
    }

    /**
     * Get the Default Connection Name
     *
     * @return string
     **/
    public static function getDefaultConnectionName()
    {
        return static::$app['config']->get("db.active");
    }

    /**
     * Get the Database Configuration
     *
     * @param  string $name Name of the config key from db.php
     * @return array
     */
    protected function getConfig($name)
    {
        $config = array(
                'driver'    => static::$app['config']->get("db.$name.driver"),
                'database'  => static::$app['config']->get("db.$name.database"),
                'host'      => static::$app['config']->get("db.$name.host"),
                'port'      => static::$app['config']->get("db.$name.port", 3306),
                'username'  => static::$app['config']->get("db.$name.username"),
                'password'  => static::$app['config']->get("db.$name.password"),
                'charset'   => static::$app['config']->get("db.$name.charset"),
                'collation' => static::$app['config']->get("db.$name.collation"),
                'prefix'    => static::$app['config']->get("db.$name.prefix")
            );

        return $config;
    }

    public function __call($method, $param = array())
    {
        $default = static::getDefaultConnectionName();

        if (! is_null(static::$connections[$default])) {
            return call_user_func_array(array(static::$connections[$default], $method), $param);
        }
    }

    public static function __callStatic($method, $param = array())
    {
        $default = static::getDefaultConnectionName();

        if (! is_null(static::$connections[$default])) {
            return call_user_func_array(array(static::$connections[$default], $method), $param);
        }
    }

} // END class DBManager
