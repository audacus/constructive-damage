<?php

class Rest {

	const DEFAULT_CONTROLLER = 'index';
	const CONTROLLER_PREFIX = 'controller\\';
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const METHOD_DELETE = 'delete';
	const METHOD_PUT = 'put';
	const METHOD_PATCH = 'patch';
	const DEFAULT_METHOD = self::METHOD_GET;
	const FORMAT_JSON = 'json';
	const FORMAT_XML = 'xml';
	const DEFAULT_FORMAT = self::FORMAT_JSON;

	private $request;
	private $controller;
	private $methods = array(

	);
	private $formats = array(
		self::FORMAT_JSON,
		self::FORMAT_XML
	);

	public function __construct() {
		// include RestRequest
		include 'php-rest-api/RestRequest.php';
		$this->request = new RestRequest();
		$urlElements = $this->request->getUrlElements();
		echo $this->dispatch($this->processParams($urlElements));
	}

	private function processParams(array &$urlParams, $previousReturnValue = null) {
		// controller
		$controllerName = self::DEFAULT_CONTROLLER;
		if (isset($urlParams[0])) {
			$controllerName = $urlParams[0];
		}
		$controllerClassName = $this->getControllerClassName($controllerName);
		try {
			$this->controller = new $controllerClassName();
			$this->controller->setRequest($this->request);
		} catch (\Exception $e) {
			if ($e instanceof \ClassNotFoundException) {
				throw new \exception\ControllerNotFoundException($e->getMessage());
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
			switch ($controllerMethod) {
				case self::METHOD_GET:
				case self::METHOD_DELETE:
					if (!empty($previousReturnValue)) {
						$returnValue = $this->controller->$controllerMethod($controllerMethodParam, $previousReturnValue);
					} else {
						$returnValue = $this->controller->$controllerMethod($controllerMethodParam);
					}
					break;
				case self::METHOD_POST:
					$bodyParams = $this->request->getParameters();
					if (!empty($previousReturnValue)) {
						if (!empty($bodyParams)) {
							$returnValue = $this->controller->$controllerMethod($previousReturnValue, $bodyParams);
						} else {
							$returnValue = $this->controller->$controllerMethod($previousReturnValue);
						}
					} else {
						if (!empty($bodyParams)) {
							$returnValue = $this->controller->$controllerMethod($bodyParams);
						} else {
							$returnValue = $this->controller->$controllerMethod();
						}
					}
					break;
				case self::METHOD_PUT:
				case self::METHOD_PATCH:
				default:
					$bodyParams = $this->request->getParameters();
					if (!empty($previousReturnValue)) {
						if (!empty($bodyParams)) {
							$returnValue = $this->controller->$controllerMethod($controllerMethodParam, $previousReturnValue, $bodyParams);
						} else {
							$returnValue = $this->controller->$controllerMethod($controllerMethodParam, $previousReturnValue);
						}
					} else {
						if (!empty($bodyParams)) {
							$returnValue = $this->controller->$controllerMethod($controllerMethodParam, $bodyParams);
						} else {
							$returnValue = $this->controller->$controllerMethod($controllerMethodParam);
						}
					}
					break;
			}
		} else {
			throw new \exception\ClassMethodNotFoundException($this->controller, $controllerMethod);
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
		if (Helper::isAjaxRequest()) {
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
			case self::FORMAT_XML:
				$this->formatXml($value);
				break;
			case self::FORMAT_JSON:
			default:
				$this->formatJson($value);
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