<?php

namespace Reborn\Module;

use Reborn\Cores\Application;

/**
 * Module Manager Class for Reborn
 *
 * @package Reborn\Module
 * @author MyanmarLinks Professional Web Development Team
 **/
class ModuleManager
{
	/**
	 * ModuleManager instances lisst
	 *
	 * @var array|null
	 **/
	protected static $instances;

	/**
	 * Module Handler instance
	 *
	 * @var \Reborn\Module\Handler\BaseHandler
	 **/
	protected $handler;

	/**
	 * Application IOC Container instance
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * Initialize method for Module Manager.
	 *
	 * @param \Reborn\Cores\Application $app
	 * @param string $name
	 * @return void
	 **/
	public static function initialize(Application $app, $name = 'default')
	{
		if (! isset(static::$instances[$name]) ) {
			static::$instances[$name] = new static($app);
		}
	}

	/**
	 * Get Module Manager instance by name.
	 *
	 * @param string $name
	 * @return \Reborn\Module\ModuleManager
	 **/
	public static function getInstance($name = 'default')
	{
		if ( isset(static::$instances[$name]) ) {
			return static::$instances[$name];
		}

		return null;
	}

	/**
     * Create new module table.
     *
     * @param string|null $prefix
     * @return void
     **/
    public static function createNewModuleTable($prefix)
    {
    	if (! \Schema::hasTable($prefix.'modules') ) {
    		\Schema::table($prefix.'modules', function($table)
	        {
	            $table->create();
	            $table->increments('id');
	            $table->string('uri', 100);
	            $table->string('name', 255);
	            $table->text('description');
	            $table->tinyInteger('enabled');
	            $table->string('version', 10);
	        });
    	}
    }

	/**
	 * Default instance method.
	 *
	 * @param \Reborn\Cores\Application $app
	 * @return void
	 **/
	public function __construct(Application $app)
	{
		if ($app->site_manager->isMulti()) {
			$this->createMultisiteHandler($app);
		} else {
			$this->createBaseHandler($app);
		}

		$this->handler->registerInstalledModules();
	}

	/**
	 * Get Module Handler Instance
	 *
	 * @return \Reborn\Module\Handler\BaseHandler
	 **/
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * Create Base Handler instance.
	 * Base Handler is use for single site.
	 *
	 * @param \Reborn\Cores\Application $app
	 * @return void
	 **/
	protected function createBaseHandler(Application $app)
	{
		$this->handler = new \Reborn\Module\Handler\BaseHandler($app);
	}

	/**
	 * Create Multisite Handler instance.
	 *
	 * @param \Reborn\Cores\Application $app
	 * @return void
	 **/
	protected function createMultisiteHandler(Application $app)
	{
		$this->handler = new \Reborn\Module\Handler\MultisiteHandler($app);
	}

	/**
	 * Dynamicaly method access with PHP's magic method.
	 *
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 **/
	public static function __callStatic($method, $args)
	{
		$instance = static::getInstance()->getHandler();

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}

} // END class ModuleManager
