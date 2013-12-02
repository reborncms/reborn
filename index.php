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

// Display Errors On
ini_set('display_errors', 'On');

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
 * Load Reborn's sites manager file
 * ---------------------------------------------------------
 *
 * Reborn define other constants for application,
 * start timer and memory record for profiling and
 * make class alias to easy access for developer.
 *
 */
$sites = require __DIR__.'/content/sites.php';

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
 * Load Reborn's start file from content
 * ---------------------------------------------------------
 *
 * This is customize file for user.
 * Reborn undefied nothing in this file.
 * So user can be make customize without git cconflict.
 *
 */
require_once __DIR__.'/content/start.php';

/**
 * ---------------------------------------------------------
 * Initial bootup for UTF-8
 * ---------------------------------------------------------
 *
 * Make handling for Utf8 with Patchwork.
 *
 */
\Patchwork\Utf8\Bootup::initAll();

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
if (!isset($_env)) {
	$_env = 'dev';
}
$app->setAppEnvironment($_env);
unset($_env);

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
 * Set Application Timezone
 * ---------------------------------------------------------
 *
 * Reborn set timezone for application.
 * Set timezone for application with "UTC".
 * But this timezone will override in Application::start()
 * base on application setting.
 *
 */
$app->setTimezone();

/**
 * ---------------------------------------------------------
 * Set Site Data variable to application
 * ---------------------------------------------------------
 *
 * Rebirn set sites variable for application.
 * sites variable is data array of multisite configuration.
 *
 */
$app['sites'] = $sites;

/**
 * ---------------------------------------------------------
 * Set Application Object to Facade Class
 * ---------------------------------------------------------
 *
 * Facade class have $app to easy access for Dependency Injection
 * Now, set the application object($app) to facade class.
 *
 */
\Reborn\Cores\Facade::setApplication($app);

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
