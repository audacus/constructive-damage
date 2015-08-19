<?php

if (!defined('APPLICATION_PATH')) {
	define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../app'));
}

if (!defined('CONFIG_PATH')) {
	define('CONFIG_PATH', APPLICATION_PATH.'/config/application.ini');
}


require_once 'Autoloader.php';
spl_autoload_register(array('Autoloader', 'load'));

foreach (glob('lib/*.php') as $filename) {
	include $filename;
}

use controller\Index;
new Index();