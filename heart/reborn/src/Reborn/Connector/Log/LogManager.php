<?php

namespace Reborn\Connector\Log;

use Reborn\Config\Config;
use Reborn\Cores\Registry;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Log Class for Reborn
 *
 * This class is really adapter only between Monolog and Reborn CMS.
 * Supported logging methods are -
 *  -- debug
 *  -- info
 *  -- notice
 *  -- warning
 *  -- error
 *  -- critical
 *  -- alert
 *  -- emergency
 * You can see defination for above methods at monolog documentation.
 *
 * @package Reborn\Connector\Log
 * @author Myanmar Links Professional Web Development Team
 **/
class LogManager
{

    /**
     * Variable for Log config items
     *
     * @var array
     **/
    public $configs = array();

    /**
     * Variable for monolog object
     *
     * @var object
     **/
    protected $logger = null;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    protected $support = array(
            'debug' => 'addDebug',
            'info' => 'addInfo',
            'notice' => 'addNotice',
            'warning' => 'addWarning',
            'error' => 'addError',
            'critical' => 'addCritical',
            'emergency' => 'addEmergency'
        );

    /**
     * Default constructor method for log object
     * You can pass configs values shch as
     *
     * <code>
     * array(
     *  'path' => 'public/storages/applogs/',
     *  'file_name' => 'mylog-'Date(Y-m-d),
     *  'ext' => '.txt'
     * );
     * </code>
     *
     * @param string $name
     * @param array $configs
     * @return void
     **/
    public function __construct($name = 'rebornCMSLog', $configs = array())
    {
        $defaultConfigs = Config::get('app.log');

        // Merge Default configs and given configs
        $this->configs = array_merge($defaultConfigs, $configs);

        $this->logger = new Logger($name);

        $fullpath = $this->configs['path'].$this->configs['file_name'].$this->configs['ext'];

        $this->logger->pushHandler(new StreamHandler($fullpath, Logger::DEBUG));

        return $this;
    }

    /**
     * Get the Logger Object
     *
     * @return object
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get the supprot method list array
     *
     * @return array
     **/
    public function getSupport()
    {
        return $this->support;
    }

    public static function __callStatic($method, $args)
    {
        $log = Registry::get('app')->log;
        $support = $log->getSupport();

        if (array_key_exists($method, $support)) {
            $method = $support[$method];
        }
        $args = (array)$args;

        if (is_callable(array($log->getLogger(), $method))) {
            return call_user_func_array(array($log->getLogger(), $method), $args);
        }

        throw new \BadMethodCallException("{$method} doesn't exsits in {$ins}");
    }

} // END class LogManager
