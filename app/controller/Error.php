<?php

namespace controller;

class Error extends \ErrorHandler {

	public static function exception(\Exception $e) {

		$severity = 1 * E_ERROR
			| 1 * E_WARNING
			| 1 * E_PARSE
			| 1 * E_NOTICE
			| 1 * E_CORE_ERROR
			| 1 * E_CORE_WARNING
			| 1 * E_COMPILE_ERROR
			| 1 * E_COMPILE_WARNING
			| 1 * E_USER_ERROR
			| 1 * E_USER_WARNING
			| 1 * E_USER_NOTICE
			| 1 * E_STRICT
			| 1 * E_RECOVERABLE_ERROR
			| 1 * E_DEPRECATED
			| 1 * E_USER_DEPRECATED;

		if ($e instanceof \ErrorException && (($e->getSeverity() & $severity) != 0 || $e->getSeverity() === 0)) {
			switch ($e->getSeverity()) {
			case E_USER_ERROR:
				echo "<b>USER ERROR</b> ".$e->getCode()." ".$e->getMessage()."<br />\n";
				echo "Fatal error on line ".$e->getLine()." in file ".$e->getFile();
				echo ", PHP ".PHP_VERSION." (".PHP_OS.")<br />\n";
				echo "Aborting...<br />\n";
				exit(1);
				break;
			case E_USER_WARNING:
				echo "<b>WARNING</b><br />\n";
				self::printError($e);
				break;
			case E_USER_NOTICE:
				echo "<b>NOTICE</b><br />\n";
				self::printError($e);
				break;
			case E_STRICT:
				echo "<b>STRICT</b><br />\n";
				self::printError($e);
				break;
			default:
				self::printError($e);
				break;
			}
		} else {
			self::printError($e);
		}
		// Don't execute PHP internal error handler
		return true;
	}

	private static function printError(\Exception $e) {
        global $cli;
        if (isset($cli))
            \RunCycle::printError($e);
        else
            echo "<b>".get_class($e)."</b> ".$e->getMessage()." on line ".$e->getLine()." in file ".$e->getFile()."<pre>".$e->getTraceAsString()."</pre>";
	}
}
