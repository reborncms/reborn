<?php

namespace Reborn\Auth\Sentry;

use Cartalyst\Sentry\Sessions\SessionInterface;

/**
 * Symfony Session For Sentry
 *
 * @package Reborn\Auth\Sentry
 * @author Myanmar Links Professional Web Development Team
 **/
class SymfonySession implements SessionInterface
{
    /**
     * Session Store Object
     *
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $store;

    /**
     * Session Key Name for Sentry
     *
     * @var string
     */
    protected $key = 'cartalyst_sentry';

    /**
     * Default instance method.
     *
     * @param  \Reborn\Cores\Application $app
     * @param  string|null               $key Session Key Name for Sentry
     * @return void;
     */
    public function __construct(\Reborn\Cores\Application $app, $key = null)
    {
        $this->store = $app->session;

        if (!is_null($key)) {
            $this->key = $key;
        }
    }

    /**
     * Get Session Key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Put the value to session
     *
     * @param  mixed $value Vlues to put the session
     * @return void
     */
    public function put($value)
    {
        $this->store->set($this->getkey(), $value);
    }

    /**
     * Get the Session value data for Sentry Key
     *
     * @return mixed
     */
    public function get()
    {
        return $this->store->get($this->getKey());
    }

    /**
     * Remove the Sentry Session
     *
     * @return void
     */
    public function forget()
    {
        $this->store->remove($this->getKey());
    }
}
