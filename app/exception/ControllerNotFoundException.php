<?php

namespace exception;

use \Exception;

class ControllerNotFoundException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct("Controller '".$message."' could not be found", $code, $previous);
	}
}