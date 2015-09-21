<?php

use exception\ControllerNotFoundException;

class Rest {

	public function __construct() {
		require_once 'RestRequest.php';
		$request = new RestRequest();

		$controllerName = $this->getControllerName($request->getUrlElements()[0]);

		try {
			new $controllerName();
		} catch (\Exception $e) {
			// throw new ControllerNotFoundException($controllerName);
			// use default controller
			$controllerName = $this->getControllerName('index');
			new $controllerName();
		}
	}

	private function getControllerName($controller) {
		return 'controller\\'.ucfirst($controller);
	}
}