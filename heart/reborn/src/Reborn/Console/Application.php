<?php

namespace Reborn\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
	/**
	 * Reborn Application (IOC) Container instance
	 *
	 * @var \Reborn\Cores\Application
	 **/
	protected $app;

	/**
	 * Set Reborn Application (IOC) Container to Console Application
	 *
	 * @param \Reborn\Cores\Application $app
	 * @return void
	 **/
	public function setRebornApplication($app)
	{
		$this->app = $app;

		$app->setAppEnvironment('test');

		\Reborn\Cores\Facade::setApplication($app);

		$app->start();
	}

	/**
	 * Register Default Command.
	 *
	 * @return void
	 **/
	public function register()
	{
		$this->add(new ModuleCommand);
		$this->add(new CompileCommand);
		$this->add(new MultisiteCommand);
		$this->add(new CacheClearCommand);
		$this->add(new ThemeGenerateCommand);
		$this->add(new ExtensionCheckCommand);
		$this->add(new AuthKeyGenerateCommand);
	}
}
