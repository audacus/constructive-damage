<?php
global $cli;
if (!isset($cli) || !$cli) {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: X-Requested-With');
}
if (!defined('APPLICATION_PATH')) {
	define('APPLICATION_PATH', realpath(dirname(__FILE__)).'/..');
}

if (!defined('DEFAULT_CONFIG_PATH')) {
	define('DEFAULT_CONFIG_PATH', APPLICATION_PATH.'/config/default/application.json');
}

if (!defined('CONFIG_PATH')) {
	define('CONFIG_PATH', APPLICATION_PATH.'/config/application.json');
}

// require and initialize config
try {
	require_once APPLICATION_PATH.'/lib/Config.php';
	Config::parseAndSetConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH);
} catch (\Exception $e) {
	die($e->getMessage());
}

// include all files in the lib folder
$pathPartsLib = array(
	APPLICATION_PATH,
	Config::get('app.path.lib'),
	'*.php'
);
foreach (glob(implode(DIRECTORY_SEPARATOR, $pathPartsLib)) as $lib) {
	include_once $lib;
}

// require abstract exception
require_once Config::get('app.exception.file');


// include all exceptions
$pathPartsException = array(
	APPLICATION_PATH,
	Config::get('app.path.exception'),
	'*.php'
);
foreach (glob(Helper::makePathFromParts($pathPartsException)) as $exception) {
	include_once $exception;
}

// initialize autoloader
require_once Config::get('app.autoloader.file');
spl_autoload_register(array(Config::get('app.autoloader.class'), Config::get('app.autoloader.function')));

// initialize error controller
require_once Config::get('app.errorhandler.file');
set_error_handler(Config::get('app.errorhandler.errorfunction'));
set_exception_handler(Config::get('app.errorhandler.exceptionfunction'));

// require NotORM
require_once Config::get('db.notorm.file');

// start application
new App();
