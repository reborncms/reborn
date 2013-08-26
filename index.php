<?php

/*    _   _   _   _   _   _     _   _   _
 *  / \ / \ / \ / \ / \ / \   / \ / \ / \
 * ( R | e | b | o | r | n ) ( C | M | S )
 *  \_/ \_/ \_/ \_/ \_/ \_/   \_/ \_/ \_/
 */

/**
 * ---------------------------------------------------------
 * Reborn CMS |--RB C^^$--| PHP Content Management System
 * ---------------------------------------------------------
 *
 * @package Reborn CMS
 * @version 2.0.0
 * @link http://reborncms.com
 * @license http://opensource.org/licenses/MIT MIT License (MIT)
 * @author Myanmar Links Professional Web Development Team
 */

/**
 * ---------------------------------------------------------
 * Define Directory Separator Shortcut as DS
 * ---------------------------------------------------------
 *
 * Reborn redefine PHP's Constant DIRECTORY_SEPARATOR to DS.
 *
 */
define('DS', DIRECTORY_SEPARATOR);

// Change the current dir
chdir(__DIR__);

/**
 * ---------------------------------------------------------
 * Define BASE Path to this folder
 * ---------------------------------------------------------
 *
 * Set reborn base path. Reborn's starting directory path is here!
 *
 */
define('BASE', __DIR__ . DS);

/**
 * ---------------------------------------------------------
 * Load Reborn's start helper file
 * ---------------------------------------------------------
 *
 * Reborn define other constants for application,
 * start timer and memory record for profiling and
 * make class alias to easy access for developer.
 *
 */
require_once __DIR__.'/heart/reborn/src/start.php';

/**
 * ---------------------------------------------------------
 * Now Create the Reborn Application Instance.
 * ---------------------------------------------------------
 *
 * Create Reborn Application Object to start the application.
 *
 */
$app = new Reborn\Cores\Application();

/**
 * ---------------------------------------------------------
 * Set the Reborn CMS Environment
 * ---------------------------------------------------------
 *
 * Set the environment type for reboen.
 * Supported environment are (dev|test|production)
 *  - "dev" mode for Developing Stage.
 *  - "test" mode for Testing Stage.
 *  - "production" mode for Production Stage.
 * ******* You must be set mode is "production" for real running stage. *******
 *
 */
$app->setAppEnvironment('dev');

/**
 * ---------------------------------------------------------
 * Set Error reporting
 * ---------------------------------------------------------
 *
 * Reborn set the error reporting base on application environment.
 * Error report are show at "dev" and "test" mode but hide at "production".
 *
 */
if($app['env'] != 'production') {
	ini_set('display_errors', 'On');
	error_reporting(-1);
} else {
	error_reporting(0);
	ini_set('display_errors', 'Off');
}

/**
 * ---------------------------------------------------------
 * Really start point for Reborn CMS
 * ---------------------------------------------------------
 *
 * First reborn check the application is already installed or not.
 * If application need to install,
 * reborn will run the application installer.
 * If application is already installed,
 * reborn will run the application starter.
 *
 */
if ($app->installed()) {
    $app->start();
} else {
	$app->install();
}

// Clear the $app.
unset($app);
