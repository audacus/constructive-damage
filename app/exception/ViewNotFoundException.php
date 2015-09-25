<?php

namespace exception;

use \Exception;

class ViewNotFoundException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct("View '".$message."' could not be found on line ".$this->getLine()." in file ".$this->getFile(), $code, $previous);
	}
}