<?php

namespace controller;

use \Helper;
use \Config;

abstract class AbstractController {

	protected $config;

	public function __construct() {
		$this->init();
		$this->postDispatch();
	}

	private function init() {
		$this->config = new Config(Helper::parseConfig(CONFIG_PATH));
		// echo '<pre>'.var_export($this->config->get('database.dbname'), true).'</pre>';die();
	}

	private function postDispatch() {
		if (\Helper::isAjax()) {
			// if ajax
		} else {
			$this->render();
		}
	}
}