<?php

// Define Application Everoment
if(!defined('ENV'))
{
	$env = isset($_SERVER['REBORN_ENV']) ? $_SERVER['REBORN_ENV'] : 'dev';
	define('ENV', $env);
}

// Error Reporting base on Reborn Enveroment
if($env != 'production')
{
	ini_set('display_errors', 'On');
	error_reporting(-1);
}
else
{
	error_reporting(-1);
	ini_set('display_errors', 'Off');
}


// Define Profiler
if(! defined('PROFILER'))
{
	define('PROFILER', false);
}

// Define Content Path
if(! defined('CONTENT'))
{
	define('CONTENT', realpath(BASE.'content/').DS);
}

// Define Upload Path
if(! defined('UPLOAD'))
{
	define('UPLOAD', realpath(CONTENT.'uploads/').DS);
}

// Define Modules Path
if(! defined('MODULES'))
{
	define('MODULES', realpath(CONTENT.'modules/').DS);
}

// Define Widget Path
if(! defined('WIDGETS'))
{
	define('WIDGETS', realpath(CONTENT.'widgets/').DS);
}

// Define Themes Path
if(! defined('THEMES'))
{
	define('THEMES', realpath(CONTENT.'themes/').DS);
}

// Define System Path
if(! defined('SYSTEM'))
{
	define('SYSTEM', realpath(BASE.'/heart/').DS);
}

// Define Application Original Data Path
if(! defined('APP'))
{
	define('APP', realpath(SYSTEM.'/appdata/').DS);
}

// Define Storages Path
if(! defined('STORAGES'))
{
	define('STORAGES', realpath(BASE.'/storages/').DS);
}

// Define Global Assets Folder
if(! defined('GLOBAL_ASSETS')) {
	define('GLOBAL_ASSETS', realpath(SYSTEM.'/global/').DS);
}

// Define Global Assets Folder URL
if(! defined('GLOBAL_URL')) {
	if ( !isset($_SERVER['argc'])) {
  		$protocol = ('80' == $_SERVER['SERVER_PORT']) ? 'http://' : 'https://';
		$host = $_SERVER['HTTP_HOST'];
		$script = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$fullUrl = $protocol.$host.$script.'heart/global/';
		define('GLOBAL_URL', $fullUrl);
	}
}

// Define System's Cores Path
if(! defined('CORES'))
{
	define('CORES', realpath(SYSTEM.'reborn/src/Reborn/').DS);
}

// Define Core Modules Path
if(! defined('CORE_MODULES'))
{
	define('CORE_MODULES', realpath(SYSTEM.'/modules/').DS);
}

// Define Admin Themes Path
if(! defined('ADMIN_THEME'))
{
	define('ADMIN_THEME', realpath(SYSTEM.'/themes/').DS);
}

if(! defined('EXT'))
{
	define('EXT', '.php');
}

// Require Autoload File
require_once SYSTEM.'vendor/autoload.php';

// Set Time and Memory for Application Start
define('REBORN_START_TIME', microtime(true));
define('REBORN_START_MEMORY', memory_get_usage());

// Class Alias for Reborn Cores Alias Class
class_alias('Reborn\Cores\Alias', 'Alias');
Alias::coreClassAlias();
