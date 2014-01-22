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

		if ($app->installed()) {
			$app->start();
		}
	}

	/**
	 * Register Default Command For Reborn CMS.
	 *
	 * @return void
	 **/
	public function registerForReborn()
	{
		foreach ( $this->getDefaultCommands() as $command ) {
			$this->add($command);
		}
	}

	/**
	 * Get Default Commands from Reborn CMS
	 *
	 * @return array
	 **/
	public function getDefaultCommands()
	{
		return array(
			'module'			=> new ModuleCommand,
			'compile'			=> new CompileCommand,
			'multisite'			=> new MultisiteCommand,
			'cache_clear'		=> new CacheClearCommand,
			'theme' 			=> new ThemeGenerateCommand,
			'extension_check'	=> new ExtensionCheckCommand,
			'auth_key_generate'	=> new AuthKeyGenerateCommand
		);
	}
}
