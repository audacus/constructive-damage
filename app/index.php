<?php

if (!defined('APPLICATION_PATH')) {
	define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));
}

if (!defined('DEFAULT_CONFIG_PATH')) {
	define('DEFAULT_CONFIG_PATH', APPLICATION_PATH.'/config/default/application.json');
}

if (!defined('CONFIG_PATH')) {
	define('CONFIG_PATH', APPLICATION_PATH.'/config/application.json');
}

set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH.'/lib'),
	get_include_path()
)));


// include all files in the lib folder
foreach (glob('lib/*.php') as $filename) {
	include $filename;
}

// initialize autoloader
require_once 'Autoloader.php';
spl_autoload_register(array('Autoloader', 'load'));

// // initialize error handler
// require_once 'ErrorHandler.php';
// set_error_handler('ErrorHandler::error');
// set_exception_handler('ErrorHandler::exception');

// include NotORM
require_once 'notorm/NotORM.php';

// start application
new controller\Index();