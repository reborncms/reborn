<?php

/**
 * Swift Mailer Mail Config
 *
 */

return array(

	/**
	 * Mail Driver Type
	 * Supported Driver : "smtp", "sendmail", "mail"
	 *
	 */
	'type' => function() {
		$type = Setting::get('transport_mail');

		if ('' == $type) {
			$type = 'mail';
		}

		return $type;
	},

	/**
	 * Host name for smtp transport
	 *
	 */
	'host' => function() {
		return Setting::get('smtp_host');
	},

	/**
	 * Port No: for smtp transport
	 *
	 */
	'port' => function() {
		return Setting::get('smtp_port');
	},

	/**
	 * Username for smtp transport
	 *
	 */
	'username' => function() {
		return Setting::get('smtp_username');
	},

	/**
	 * Password for smtp transport
	 *
	 */
	'passsword' => function() {
		return Setting::get('smtp_password');
	},

	/**
	 * Path for sendmail transport
	 *
	 * Example : '/usr/sbin/sendmail -bs'
	 */
	'path' => function() {
		$path = Setting::get('sendmail_path');
		if ('' == $path) {
			$path = '/usr/sbin/sendmail -bs';
		}

		return $path;
	},

	/**
	 * Email address for Send from CMS
	 *
	 */
	'sender_mail' => function() {
		return Setting::get('server_mail', Auth::getSuperuserEmail());
	},

	/**
	 * Email address for Receive from contact person
	 *
	 */
	'receive_mail' => function() {
		return Setting::get('site_mail', Auth::getSuperuserEmail());
	},

);
