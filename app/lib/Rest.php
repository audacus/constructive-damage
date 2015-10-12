<?php

use \Helper;
use exception\ControllerNotFoundException;
use exception\ClassMethodNotFoundException;
use exception\ViewNotFoundException;

class Rest {

	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION = 'index';
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
			if ($e instanceof ViewNotFoundException) {
				throw $e;
			} else if ($e instanceof FileNotFoundException) {
				throw new ControllerNotFoundException($e->getMessage());
			} else {
				throw $e;
			}
		}


		// controller/id/controller/id

		// GET users/12/posts/42:
		// post 42 of user 12
		// always loop through parameters:
		// 1. GET users/12 -> controller\Users::get(12)
		// 2. GET posts/42 -> controller\Posts::get(user, 42)

		// GET users/12/posts:
		// all posts of user 12
		// 1. GET users/12 -> user = controller\Users::get(12)
		// 2. GET posts/42 -> controller\Posts::get(user, null)

		// POST users/12/posts:
		// create new posts for user 12
		// 1. GET users/12 -> user = controller\Users::get(12)
		// 2. POST posts -> controller\Posts::post(user, post)

		if (!empty($controllerMethodParams)) {
			// $controllerMethodName .= ucfirst(strtolower(array_shift($controllerMethodParams)));
			if (method_exists($controller, $controllerMethodName)) {
				// echo $controller->dispatch($controller->$controllerMethodName(array_merge($controllerMethodParams, $request->getParameters())));
				if (count($controllerMethodParams) === 1) {
					$controllerMethodParams = current($controllerMethodParams);
				}
				echo $controller->dispatch($controller->$controllerMethodName($controllerMethodParams));
			} else {
				throw new ClassMethodNotFoundException($controller, $controllerMethodName);
			}
		} else {
			if (method_exists($controller, self::DEFAULT_ACTION)) {
				$controllerMethodName = self::DEFAULT_ACTION;
				echo $controller->dispatch($controller->$controllerMethodName());
			} else {
				$controller->dispatch();
			}
		}
	}

	private function getControllerClassName($controller) {
		return self::CONTROLLER_PREFIX.ucfirst($controller);
	}
}