<?php

namespace controller;

use \Helper;
use \Config;
use \Database;
use \Language;
use \exception\ViewNotFoundException;

abstract class AbstractController {

	const VIEW_PREFIX = 'view\\';

	protected $request;
	protected $config;
	protected $view;

	public function __construct(AbstractController $controller = null) {
		$this->init($controller);
	}

	public abstract function get();

	private function init(AbstractController $controller = null) {
		$this->config = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
		$this->language = new Language();
		if (!empty($controller)) {
			$this->view = $controller->getView();
		} else {
			$this->setView();
		}
	}

	public function getTableName() {
		return substr(\Helper::getLowerCaseClassName($this), 0, -1);
	}

	public function putLanguage($language) {
		// change language (cookie?)
		// set language in view instead of controller?
		$this->language->setLanguage($language);
	}

	public function renderView() {
		echo $this->view->getSiteContent();
	}

	public function setRequest(\RestRequest $request) {
		$this->request = $request;
		return $this->request;
	}

	protected function getView() {
		if (empty($this->view)) {
			$this->setView();
		}
		return $this->view;
	}

	protected function setView(view\AbstractView $view = null) {
		if (empty($view)) {
			$name = end(explode('\\', get_class($this)));
			$className = self::VIEW_PREFIX.$name;
			try {
				$this->view = new $className();
			} catch (\Exception $e) {
				throw new exception\ViewNotFoundException($e->getMessage());
			}
		} else {
			$this->view = $view;
		}
		return $this->view;
	}

}
