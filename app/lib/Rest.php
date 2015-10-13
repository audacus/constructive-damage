<?php

use \Helper;
use exception\ControllerNotFoundException;
use exception\ClassMethodNotFoundException;
use exception\ViewNotFoundException;

class Rest {

	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_METHOD = 'get';
	const CONTROLLER_PREFIX = 'controller\\';
	const FORMAT_JSON = 'json';
	const FORMAT_XML = 'xml';
	const DEFAULT_FORMAT = self::FORMAT_JSON;

	private $request;
	private $controller;
	private $formats = array(
		self::FORMAT_JSON,
		self::FORMAT_XML
	);

	public function __construct() {
		require_once 'RestRequest.php';
		$this->request = new RestRequest();
		$urlElements = $this->request->getUrlElements();

		echo $this->dispatch($this->processParams($urlElements));
	}

	private function processParams(&$urlParams, $previousReturnValue = null) {
		// controller
		$controllerName = self::DEFAULT_CONTROLLER;
		if (isset($urlParams[0])) {
			$controllerName = $urlParams[0];
		}
		$controllerClassName = $this->getControllerClassName($controllerName);
		try {
			$this->controller = new $controllerClassName();
		} catch (\Exception $e) {
			if ($e instanceof ViewNotFoundException) {
				throw $e;
			} else if ($e instanceof FileNotFoundException) {
				throw new ControllerNotFoundException($e->getMessage());
			} else {
				throw $e;
			}
		}

		// method
		if (count($urlParams) > 2) {
			$controllerMethod = self::DEFAULT_METHOD;
		} else {
			$controllerMethod = strtolower($this->request->getVerb());
		}

		// param
		$controllerMethodParam = null;
		if (isset($urlParams[1])) {
			$controllerMethodParam = $urlParams[1];
		}

		// process
		$returnValue = null;
		if (method_exists($this->controller, $controllerMethod)) {
			if (!empty($previousReturnValue)) {
				$returnValue = $this->controller->$controllerMethod($controllerMethodParam, $previousReturnValue);
			} else {
				$returnValue = $this->controller->$controllerMethod($controllerMethodParam);
			}
		} else {
			throw new ClassMethodNotFoundException($this->controller, $controllerMethod);
		}

		// prepare for next loop
		$urlParams = array_slice($urlParams, 2);
		if (!empty($urlParams)) {
			$returnValue = $this->processParams($urlParams, $returnValue);
		}
		return $returnValue;
	}

	private function dispatch($value = null) {
		$returnValue = null;
		if (Helper::isAjax()) {
			$returnValue = $this->formatJson($value);
		} else {
			if (is_null($value)) {
				$this->controller->renderView();
			} else {
				$returnValue = $this->formatValue($value);
			}
		}
		return $returnValue;
	}

	private function formatValue(&$value) {
		$format = self::DEFAULT_FORMAT;
		if (isset($_REQUEST['format']) && in_array($_REQUEST['format'], $this->formats)) {
			$format = $_REQUEST['format'];
		} else {
			foreach (array_keys($_REQUEST) as $param) {
				if (in_array($param, $this->formats)) {
					$format = $param;
				}
			}
		}
		switch ($format) {
			case self::FORMAT_JSON:
				$this->formatJson($value);
				break;
			case self::FORMAT_XML:
				$this->formatXml($value);
				break;
			default:
				// do nothing
				break;
		}
		return $value;
	}

	private function formatJson(&$value) {
		header('Content-Type: application/json');
		return $value = json_encode($value);
	}

	private function formatXml(&$value) {
		header('Content-Type: text/xml');
		return $value = 'XML not yet supported.<br />'.print_r($value, true);
	}

	private function getControllerClassName($controller) {
		return self::CONTROLLER_PREFIX.ucfirst($controller);
	}
}