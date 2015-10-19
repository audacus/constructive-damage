<?php

if (!defined('APPLICATION_PATH')) {
	define('APPLICATION_PATH', realpath(dirname(__FILE__)));
}

if (!defined('DEFAULT_CONFIG_PATH')) {
	define('DEFAULT_CONFIG_PATH', APPLICATION_PATH.'/config/default/application.json');
}

if (!defined('CONFIG_PATH')) {
	define('CONFIG_PATH', APPLICATION_PATH.'/config/application.json');
}

// add lib folder to include path
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

// initialize error controller
require_once 'ErrorHandler.php';
set_error_handler('controller\ErrorController::error');
set_exception_handler('controller\ErrorController::exception');

// include NotORM
require_once 'notorm/NotORM.php';

// start application
new App();
