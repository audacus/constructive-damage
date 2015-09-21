<?php

namespace controller;

use \Helper;
use \Config;
use \Database;
use \exception\ViewNotFoundException;

abstract class AbstractController {

	const VIEW_PREFIX = 'view\\';
	protected $config;
	protected $view;

	public function __construct() {
		$this->init();
		$this->postDispatch();
	}

	private function init() {
		$this->config = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
	}

	private function postDispatch() {
		if (\Helper::isAjax()) {
			// if ajax
		} else {
			$this->renderView();
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
		try {
			if (class_exists(self::VIEW_PREFIX.$name)) {
				$className = self::VIEW_PREFIX.$name;
				$view = new $className();
			}
		} catch (Exception $e) {
			// throw new ViewNotFoundException(self::VIEW_PREFIX.$name);
		}
		return $view;
	}
}