<?php

/**
 * Reborn CMS <==(-^_^-)==> PHP Content Management System
 *
 * @package Reborn CMS
 * @author Reborn CMS Development Team
 */

// Define Directory Sperator
define('DS', DIRECTORY_SEPARATOR);

// Change the current dir
//chdir(__DIR__);

// Define Base Dir Path
define('BASE', __DIR__ . DS);

// load Reborn CMS start file
require_once __DIR__.'/heart/reborn/src/start.php';

// Create Object for Application
$app = new Reborn\Cores\Application();

// Check Reborn is already installed or not
if ($app->installed()) {

	// Start the Application
    $app->start();
} else {
	$app->install();
}

// Clear the $app.
unset($app);
