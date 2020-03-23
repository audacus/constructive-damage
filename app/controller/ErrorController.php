<?php

namespace controller;

class ErrorController extends \ErrorHandler {

	public static function exception(\Exception $e) {
		// echo get_class($e).'<br />';

		if ($e->getCode() !== 0 && !(error_reporting() & $e->getCode())) {
			// This error code is not included in error_reporting
			return;
		}
		switch ($e->getCode()) {
		case E_USER_ERROR:
			echo '<b>USER ERROR</b> '.$e->getCode().' '.$e->getMessage().'<br />\n';
			echo '  Fatal error on line '.$e->getLine().' in file '.$e->getFile();
			echo ', PHP '.PHP_VERSION.' ('.PHP_OS.')<br />\n';
			echo 'Aborting...<br />\n';
			exit(1);
			break;
		case E_USER_WARNING:
			echo '<b>WARNING</b> '.$e->getCode().' '.$e->getMessage().'<br />\n';
			break;
		case E_USER_NOTICE:
			echo '<b>NOTICE</b> '.$e->getCode().' '.$e->getMessage().'<br />\n';
			break;
		default:
			self::printError($e);
			break;
		}
		/* Don't execute PHP internal error handler */
		return true;
	}

	private static function printError(\Exception $e) {
		echo '<b>'.get_class($e).'</b> '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile().'<pre>'.$e->getTraceAsString().'</pre>';
	}
}
