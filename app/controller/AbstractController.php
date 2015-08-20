<?php

namespace controller;

use \Helper;
use \Config;

abstract class AbstractController {

	protected $config;
	protected $view;

	public function __construct() {
		$this->init();
		$this->postDispatch();
	}

	private function init() {
		$this->config = new Config(Helper::parseConfig(CONFIG_PATH));
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
		$name = end(explode(BACKSLASH, get_class($this)));
		try {
			if (class_exists('view'.BACKSLASH.$name)) {
				$className = 'view'.BACKSLASH.$name;
				$view = new $className();
			}
		} catch (Exception $e) {
			// the view class could not be found
		}
		return $view;
	}
}