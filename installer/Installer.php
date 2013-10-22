<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Reborn\MVC\View\View;
use Reborn\Form\Validation;
use Reborn\Filesystem\File;
use Reborn\Connector\Sentry\Sentry;
use Reborn\Cores\Setting;
use Reborn\Module\ModuleManager as Module;
use Reborn\Connector\DB\DBManager as DB;
use Cartalyst\Sentry\Hashing\BcryptHasher;
use Composer\Autoload\ClassLoader as Loader;

/**
 * Reborn CMS Installer Class
 *
 * @package installer
 * @author Myanmar Links Web Development Team
 **/
class Installer
{

	protected static $request;

	protected static $url;

	protected static $view_path;

	protected static $view;

	protected static $sess;

	protected static $step = array();

	public static function init()
	{
		static::$view_path = __DIR__.DS.'view'.DS;

		static::$sess = new Session();
		static::$sess->start();

		static::setUrl();

		static::$view = new View();
		static::$view->url = static::getUrl();
	}

	public static function start()
	{
		$uri = static::uri(1);

		// First We check the Folder Write Access
		if ((is_null($uri)) || !static::$sess->get('step1')) {
			static::step1();
		}

		if (!static::$sess->get('step1') and ('' != $uri)) {
			$redirect = new RedirectResponse(static::$url, 302);
        	return $redirect->send();
		}

		// Second we collect site database data
		if (static::$sess->get('step1') and ('step2' == $uri)) {
			static::step2();
		}

		if (!static::$sess->get('step2') and ('step2' != $uri)) {
			$redirect = new RedirectResponse(static::$url.'step2', 302);
        	return $redirect->send();
		}

		// Third we collect site site data and user data
		if (static::$sess->get('step2') and ('step3' == $uri)) {
			static::step3();
		}

		exit;
	}

	public static function step1()
	{
		$result = array();
		// Check Storages Path
		$storages = glob(STORAGES.'*', GLOB_ONLYDIR);
		$result['storages'] = static::pathCheck(array_merge(array(STORAGES), $storages));

		// Check Content Path
		$content = glob(CONTENT.'*', GLOB_ONLYDIR);
		$result['content'] = static::pathCheck(array_merge(array(CONTENT), $content));

		// Check Config Path
		$result['config'] = static::pathCheck(array(APP.'config'));

		// Check Config Files
		$conf_files = glob(APP.'config'.DS.'*.php');
		$result['config_files'] = static::fileCheck($conf_files);

		static::$view->title = 'Reborn CMS Installer Step 1';
		static::$view->result = $result;
		static::$view->exts = static::checkExtensions();
		static::$view->status = static::$step['step1'];

		static::$sess->set('step1', static::$step['step1']);

		echo static::$view->render(static::$view_path.'step1.php');
		exit;
	}

	protected static function step2()
	{
		static::$view->title = 'Reborn CMS Installer Step 2';

		if(static::$request->getMethod() == 'POST') {
			$v = static::inputCheck('step2');
			if($v->valid()) {

				static::$sess->set('db', $_POST);

				static::$sess->set('step2', true);

				$redirect = new RedirectResponse(static::$url.'step3', 302);
        		return $redirect->send();
			} else {
				static::$view->error = 'Please Fill correct data!';
			}
		}

		echo static::$view->render(static::$view_path.'step2.php');
		exit;
	}

	public static function step3()
	{
		static::$view->title = 'Reborn CMS Installer Step 3';

		if(static::$request->getMethod() == 'POST') {
			$v = static::inputCheck('step3');
			if($v->valid()) {
				static::$sess->set('data', $_POST);

				$result = static::makeInstallation();

				if ('success' == $result['status']) {
					static::$sess->set('step3', true);
					static::$sess->clear();
					$redirect = new RedirectResponse(static::$url, 302);
        			return $redirect->send();
				} else {
					static::$view->error = $result['msg'];
				}
			} else {
				static::$view->error = 'Please Fill correct data!';
			}
		}

		echo static::$view->render(static::$view_path.'step3.php');
		exit;
	}

	public static function getUrl()
	{
		return static::$url;
	}

	protected static function makeInstallation()
	{
		$data = static::$sess->get('data');
		$db = static::$sess->get('db');
		$db['server'] = $db['hostname'].':'.$db['port'];
		// Return Error when MySQL connection dows not work
		if(! $mysqldb = @mysql_connect($db['server'],$db['mysql_username'],$db['mysql_password']) ) {
			return array('status' => 'fail', 'msg' => 'Database Connection Error');
		}

		// Create Database if requirement
		if ( ! empty($db['db_create'] )) {
			mysql_query('CREATE DATABASE IF NOT EXISTS '.$db['db'].' CHARACTER SET utf8 COLLATE utf8_unicode_ci', $mysqldb);
		}

		// Select DB
		if( !mysql_select_db($db['db'], $mysqldb) ) {
			return array('status' => 'fail', 'msg' => 'No Database select!');
		}

		$d = new DateTime;

		$pass = static::passwordHash($data['password']);

		$sql = file_get_contents(__DIR__.DS.'data'.DS.'mysql.sql');
		$sql = str_replace('{SITETITLE}',  mysql_real_escape_string($data['site_title']), $sql);
		$sql = str_replace('{SLOGAN}',  mysql_real_escape_string($data['slogan']), $sql);
		$sql = str_replace('{NOW}',  $d->format('Y-m-d H:i:s'), $sql);
		$sql = str_replace('{EMAIL}',  $data['email'], $sql);
		$sql = str_replace('{PASS}',  $pass, $sql);
		$sql = str_replace('{EMAIL}',  $data['email'], $sql);
		$sql = str_replace('{FIRSTNAME}',  $data['first_name'], $sql);
		$sql = str_replace('{LASTNAME}',  $data['last_name'], $sql);
		$sql = explode("--- db ---", $sql);

		// Loop and query for each MySQL Command
		foreach ($sql as $q) {
			$mysql = rtrim( trim($q), "\n;");
	        if(strlen($mysql) > 0) {
	            mysql_query($mysql, $mysqldb);
	        }
	        // Check MySQL Error No
	        if (mysql_errno($mysqldb) > 0) {
				return array('status' => 'fail', 'msg' => mysql_error($mysqldb));
			}
		}

		$dbfile = file_get_contents(__DIR__.DS.'data'.DS.'db.php');
		$dbfile = str_replace('{DB}', $db['db'], $dbfile);
		$dbfile = str_replace('{HOST}',  $db['hostname'], $dbfile);
		$dbfile = str_replace('{PORT}',  $db['port'], $dbfile);
		$dbfile = str_replace('{USER}',  $db['mysql_username'], $dbfile);
		$dbfile = str_replace('{PASSWORD}',  $db['mysql_password'], $dbfile);

		File::write(APP.'config'.DS, 'db.php', $dbfile);

		static::moduleInstall();

		return array('status' => 'success');
	}

	protected static function moduleInstall()
	{
		// Start the Database initialize
        DB::initialize();

        // Start the Setting initialize
        Setting::initialize();

        // Start the Module initialize
        Module::initialize();

        $modules = Module::getAll();

        foreach ($modules as $name => $mod) {
        	static::loadModule($mod);
        	Module::install($name, $mod['uri'], true, false);
        }
	}

	protected static function loadModule($data)
	{
		if (!class_exists('Composer\Autoload\ClassLoader')) {
            throw new \RbException("Need \"Composer\Autoload\ClassLoader\" to install Reborn CMS");
        }
        $path = $data['path'];
		$namespace = $data['ns'];
        $loader = new Loader();
        $loader->add($namespace, $path.'src');
        $loader->register();
	}

	protected static function passwordHash($pass)
	{
		$hash = new BcryptHasher();
		return $hash->hash($pass);
	}

	protected static function pathCheck($paths = array())
	{
		$result = array();
		foreach ($paths as $path) {
			// Permission change 0777
			@chmod($path, 0777);
			$result[$path] = is_writable($path);
		}

		if (!isset(static::$step['step1'])) {
			static::$step['step1'] = true;
		}

		foreach ($result as $r) {
			if (!$r) {
				static::$step['step1'] = false;
			}
		}

		return $result;
	}

	protected static function fileCheck($files = array())
	{
		$result = array();
		foreach ($files as $file) {
			// Permission change 0666
			@chmod($file, 0666);
			$result[$file] = is_writable($file);
		}

		foreach ($result as $r) {
			if (!$r) {
				static::$step['step1'] = false;
			}
		}

		return $result;
	}

	protected static function checkExtensions()
	{
		$data = array();
		$data['pass'] = false;
		$data['php']['version'] = phpversion();
		$data['php']['status'] = (version_compare(PHP_VERSION, '5.3.4', '>=')) ? true : false;

		$data['mysql']['status'] = function_exists('mysql_connect') ? true : false;

		// Check Mod_Rewrite
		if(function_exists('apache_get_modules')) {
			$modules = apache_get_modules();
  			$data['mod_rewrite']['status'] = (in_array('mod_rewrite', $modules)) ? true : false;
		} else {
			ob_start();
			phpinfo(INFO_MODULES);
			$contents = ob_get_contents();
			ob_end_clean();
			$data['mod_rewrite']['status'] = (strpos($contents, 'mod_rewrite')) ? true : false;
		}
		// Check cUrl
		$data['curl']['status'] = (function_exists('curl_init')) ? true : false;

		// Check GD
		if (function_exists('gd_info')) {
			$gd = gd_info();
			$data['gd']['status'] = true;
			$data['gd']['vs'] = version_compare($gd['GD Version'], '2.0', '>=');
		} else {
			$data['gd']['status'] = false;
		}

		if ($data['php']['status'] and $data['mysql']['status']
			and $data['mod_rewrite']['status'] and $data['curl']['status']) {
			$data['pass'] = true;
		}

		if (!$data['pass']) {
			static::$step['step1'] = false;
		}
		return $data;
	}

	protected static function inputCheck($step)
	{
		if ('step2' == $step) {
			$rules = array(
					'db' => 'required',
					'mysql_username' => 'required',
					'port' => 'required',
					'hostname' => 'required'
				);
		}

		if ('step3' == $step) {
			$rules = array(
					'first_name' => 'required|minLength:2|maxLength:15',
					'last_name' => 'required|minLength:2|maxLength:15',
					'email' => 'required|email',
					'password' => 'required|minLength:6',
					'conf_password' => 'required|equal:password',
					'site_title' => 'required',
					'slogan' => 'required'
				);
		}

		$val = new Validation($_POST, $rules);

		return $val;
	}

	protected static function setUrl()
	{
		static::$request = Request::createFromGlobals();

		static::$url = static::$request->getSchemeAndHttpHost().static::$request->getBaseUrl().'/';
	}

	protected static function uri($no = null)
	{
		$uris = static::$request->getPathInfo();

		if ($uris == '/' || $uris == '') {
			$uri = array();
		} else {
			$uri = explode('/', ltrim($uris, '/'));
		}

		if (is_null($no)) {
			return $uri;
		} else {
			return isset($uri[$no - 1]) ? $uri[$no - 1] : null;
		}
	}

} // END class Installer
