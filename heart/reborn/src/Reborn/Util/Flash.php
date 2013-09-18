<?php

namespace Reborn\Util;

/**
 * Flash Message class
 *
 * @package Reborn\Util
 * @author Myanmar Links Professional Web Development Team
 **/
class Flash
{
	/**
	 * Get the Flash Bag [Symfony Session Flashbag]
	 *
	 * @return Symfony\Component\HttpFoundation\Session\Flash\FlashBag
	 **/
	public static function getFlash()
	{
		 $session = \Registry::get('app')->session;
		 return $session->getFlashBag();
	}

	/**
	 * Set the Info Flash Message type
	 *
	 * @param string $message
	 * @return void
	 **/
	public static function info($message)
	{
		static::getFlash()->set('info', $message);
	}

	/**
	 * Set the Error Flash Message type
	 *
	 * @param string $message
	 * @return void
	 **/
	public static function error($message)
	{
		static::getFlash()->set('error', $message);
	}

	/**
	 * Set the Success Flash Message type
	 *
	 * @param string $message
	 * @return void
	 **/
	public static function success($message)
	{
		static::getFlash()->set('success', $message);
	}

	/**
	 * Set the Warning Flash Message type
	 *
	 * @param string $message
	 * @return void
	 **/
	public static function warning($message)
	{
		static::getFlash()->set('warning', $message);
	}

	/**
	 * Get the Flash Message by type
	 *
	 * @param string $type Message Type (info, error, success, warning)
	 * @return mixed
	 **/
	public static function get($type)
	{
		return static::getFlash()->get($type);
	}

	/**
	 * Get the Flash Message HTML Block
	 *
	 * @param string $class Class name for the flash container div
	 * @return string
	 **/
	public static function flashBox($class = null)
	{
		if ($msg = static::get('error')) {
			$type = 'error';
		} elseif ($msg = static::get('success')) {
			$type = 'success';
		} elseif ($msg = static::get('info')) {
			$type = 'info';
		} elseif ($msg = static::get('warning')) {
			$type = 'warning';
		} else {
			return '';
		}

		return static::build($type, $msg, $class);
	}

	/**
	 * Build the Flash message box.
	 *
	 * @param string $type
	 * @param array $msg
	 * @param string|null $class
	 * @return string
	 **/
	protected static function build($type, $msg, $class)
	{
		$class = is_null($class) ? "alert" : $class;
		$output = '<div class="'.$class.' '.$class.'-'.$type.'" >';

		if (is_string($msg)) {
			$output .= $msg;
		} else {
			foreach ($msg as $m) {
				$output .= $m;
			}
		}

		$output .= '</div>';

		return $output;
	}

} // END Flash class
