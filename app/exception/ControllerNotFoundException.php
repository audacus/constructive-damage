<?php

namespace exception;

use \Exception;

class ControllerNotFoundException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		// parent::__construct($message, $code, $previous);
		parent::__construct("Controller '".$message."' could not be found on line ".$this->getLine()." in file ".$this->getFile(), $code, $previous);
	}
}