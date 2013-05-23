<?php

namespace Reborn\Connector\Sentry;

use Cartalyst\Sentry\Sessions\SessionInterface;

/**
 * Symfony Session For Sentry
 *
 * @package Reborn\Connector\Sentry
 * @author Myanmar Links Professional Web Development Team
 **/
class SymfonySession implements SessionInterface
{
	protected $store;

	protected $key = 'cartalyst_sentry';

	public function __construct($key = null)
	{
		$this->store = \Registry::get('app')->session;

		if (isset($this->key))
		{
			$this->key = $key;
		}
	}

	public function getKey()
	{
		return $this->key;
	}

	public function put($value)
	{
		$this->store->set($this->getkey(), $value);
	}

	public function get()
	{
		return $this->store->get($this->getKey());
	}

	public function forget()
	{
		$this->store->remove($this->getKey());
	}
}
