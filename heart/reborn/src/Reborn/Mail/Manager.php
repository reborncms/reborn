<?php

namespace Reborn\Mail;

use Setting;
use Swift_SmtpTransport;
use Swift_MailTransport;
use Swift_SendmailTransport;

/**
 * SwiftMailer Transport Manager
 *
 * @package Reborn\Mail
 * @author Nyan Lynn Htut
 **/
class Manager
{
	/**
	 * Registered transport instance
	 *
	 * @var array
	 **/
	protected $registered = array();

	/**
	 * Config lists variable
	 *
	 * @var array
	 **/
	protected $config = array();

	/**
	 * Make new transport instance.
	 *
	 * @param array $config
	 * @return \Swift_Transport
	 **/
	public function make($config = array())
	{
		$this->config = $config = $this->mergeConfig($config);

		$type = $this->getType($config);

		return $this->createInstance($type, $config);
	}

	/**
	 * Get Transport Instance base on $config['type'].
	 *
	 * @param array $config
	 * @return \Swift_Transport
	 **/
	public function getTransport($config)
	{
		$this->config = $config = $this->mergeConfig($config);

		$type = $this->getType($config);

		if (isset($this->registered[$type])) {
			return $this->registered[$type];
		}

		return $this->createInstance($type, $config);
	}

	/**
	 * Get config value by key.
	 *
	 * @param string $name
	 * @return mixed
	 **/
	public function config($key)
	{
		if ( isset($this->config[$key]) ) {
			return $this->config[$key];
		}

		return null;
	}

	/**
	 * Merge default config and new config.
	 *
	 * @param array $config
	 * @return array
	 **/
	protected function mergeConfig($config)
	{
		$default = array();

		$lists = \Config::get('mail');

		foreach ($lists as $k => $v) {
			$default[$k] = \Config::get('mail.'.$k);
		}

		return array_merge($default, $config);
	}

	/**
	 * Get transport type.
	 *
	 * @param array $config
	 * @return  string
	 **/
	protected function getType($config)
	{
		return isset($config['type']) ? $config['type'] : 'mail';
	}

	/**
	 * Create new transport instance.
	 *
	 * @param string $type
	 * @param array $config
	 * @return \Swift_Transport
	 **/
	protected function createInstance($type, $config)
	{
		switch ($type) {
			case 'smtp':
				$transport = $this->getSmtpTransport($config);
				break;

			case 'sendmail':
				$transport = $this->getSendmailTransport($config);
				break;

			default:
				$transport = $this->getMailTransport();
				break;
		}

		return $this->registered[$type] = $transport;
	}

	/**
	 * Get SwiftMail Transport instance for SMTP
	 *
	 * @param array $config
	 * @return \Swift_SendmailTransport
	 **/
	protected function getSmtpTransport($config)
	{
		extract($config);

		if (! isset($host) || ! isset($port) ) {
			throw new \InvalidArgumentException("SMTP host and port are required!");
		}

		$username = isset($username) ? $username : Setting::get('smtp_username');
		$password = isset($password) ? $password : Setting::get('smtp_password');

		$transport = Swift_SmtpTransport::newInstance($host , $port);

		if ('' === $username) {
			$transport->setUsername($username)->setPassword($password);
		}

		if (isset($encryption) and '' === $encryption) {
			$transport->setEncryption($encryption);
		}

		return $transport;
	}

	/**
	 * Get SwiftMail Transport instance for Sendmail
	 *
	 * @param array $config
	 * @return \Swift_SendmailTransport
	 **/
	protected function getSendmailTransport($config)
	{
		return Swift_SendmailTransport::newInstance($config['path']);
	}

	/**
	 * Get SwiftMail Transport instance for PHP Mail
	 *
	 * @param array $config
	 * @return \Swift_MailTransport
	 **/
	protected function getMailTransport()
	{
		return Swift_MailTransport::newInstance();
	}

} // END class Manager
