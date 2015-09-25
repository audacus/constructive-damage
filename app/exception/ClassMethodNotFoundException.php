<?php

namespace exception;

use \Exception;

class ClassMethodNotFoundException extends Exception {

	public function __construct($class, $method, $code = 0, Exception $previous = null) {
		// parent::__construct($message, $code, $previous);
		parent::__construct("Class method '".get_class($class).'::'.$method.'()'."' could not be found on line ".$this->getLine()." in file ".$this->getFile(), $code, $previous);
	}
}