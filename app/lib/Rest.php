<?php

use exception\ControllerNotFoundException;
use exception\ClassMethodNotFoundException;
use exception\ViewNotFoundException;

class Rest {

	public function __construct() {
		require_once 'RestRequest.php';
		$request = new RestRequest();
		$urlElements = $request->getUrlElements();
		$controllerName = $urlElements[0];
		$controller = null;
		$controllerMethodName = strtolower($request->getVerb());
		$controllerMethodParams = array_slice($urlElements, 1);

		if (empty($controllerName)) {
			$controllerName = 'index';
		}
		$controllerClassName = $this->getControllerClassName($controllerName);
		try {
			$controller = new $controllerClassName();
		} catch (\Exception $e) {
			if ($e instanceof ViewNotFoundException) {
				throw $e;
			} else {
				throw new ControllerNotFoundException($e->getMessage());
			}
		}

		if (!empty($controllerMethodParams)) {
			$controllerMethodName .= ucfirst(strtolower(array_shift($controllerMethodParams)));
			if (method_exists($controller, $controllerMethodName)) {
				echo $controller->$controllerMethodName($controllerMethodParams);
			} else {
				throw new ClassMethodNotFoundException($controller, $controllerMethodName);
			}
		}
	}

	private function getControllerClassName($controller) {
		return 'controller\\'.ucfirst($controller);
	}
}