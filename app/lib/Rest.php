<?php

use \Helper;
use exception\ControllerNotFoundException;
use exception\ClassMethodNotFoundException;
use exception\ViewNotFoundException;

class Rest {

	const DEFAULT_CONTROLLER = 'index';
	const CONTROLLER_PREFIX = 'controller\\';

	public function __construct() {
		require_once 'RestRequest.php';
		$request = new RestRequest();
		$urlElements = $request->getUrlElements();
		$controllerName = self::DEFAULT_CONTROLLER;
		$controller = null;
		$controllerMethodName = strtolower($request->getVerb());
		$controllerMethodParams = array_slice($urlElements, 1);

		if (isset($urlElements[0])) {
			$controllerName = $urlElements[0];
		}

		$controllerClassName = $this->getControllerClassName($controllerName);
		try {
			$controller = new $controllerClassName();
		} catch (\Exception $e) {
			if ($e instanceof FileNotFoundException) {
				throw new ControllerNotFoundException($e->getMessage());
			} else {
				throw $e;
			}
		}

		if (!empty($controllerMethodParams)) {
			$controllerMethodName .= ucfirst(strtolower(array_shift($controllerMethodParams)));
			if (method_exists($controller, $controllerMethodName)) {
				echo $controller->dispatch($controller->$controllerMethodName($controllerMethodParams));
			} else {
				throw new ClassMethodNotFoundException($controller, $controllerMethodName);
			}
		} else {
			$controller->dispatch();
		}
	}

	private function getControllerClassName($controller) {
		return self::CONTROLLER_PREFIX.ucfirst($controller);
	}
}