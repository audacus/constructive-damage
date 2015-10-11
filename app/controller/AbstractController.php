<?php

namespace controller;

use \Helper;
use \Config;
use \Database;
use \exception\ViewNotFoundException;

abstract class AbstractController {

	const VIEW_PREFIX = 'view\\';
	const FORMAT_JSON = 'json';
	const FORMAT_XML = 'xml';
	const DEFAULT_FORMAT = self::FORMAT_JSON;
	protected $config;
	protected $view;
	private $formats = array(
		self::FORMAT_JSON,
		self::FORMAT_XML
	);

	public function __construct() {
		$this->init();
		// $this->postDispatch();
	}

	private function init() {
		$this->config = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
	}

	public function dispatch($returnValue = null) {
		if (empty($returnValue)) {
			$this->renderView();
		} else {
			echo $this->formatValue($returnValue);
		}
		if (\Helper::isAjax()) {
			// if ajax
		} else {
		}
	}


	private function renderView() {
		$this->view = $this->getView();
		echo $this->view->getContent();
	}

	private function getView() {
		$backtrace = debug_backtrace();
		$view = null;
		$name = end(explode('\\', get_class($this)));
		$className = self::VIEW_PREFIX.$name;
		try {
			$view = new $className();
		} catch (\Exception $e) {
			throw new ViewNotFoundException($e->getMessage());
		}
		return $view;
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
		return $value = json_encode($value);
	}

	private function formatXml(&$value) {
		return $value = 'XML not yet supported.<br />'.$value;
	}
}