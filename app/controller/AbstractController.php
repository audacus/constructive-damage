<?php

namespace controller;

use \Helper;
use \Config;
use \Language;
use \exception\ViewNotFoundException;

abstract class AbstractController {

	const VIEW_PREFIX = 'view\\';

	protected $config;
	protected $view;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->config = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
		$this->language = new Language();
		$this->view = $this->getView();
	}

	public function putLanguage($language) {
		// change language (cookie?)
		// set language in view instead of controller?
		$this->language->setLanguage($language);
	}

	public function renderView() {
		echo $this->view->getSiteContent();
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
}