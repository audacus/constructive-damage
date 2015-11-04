<?php

namespace controller;

use \Helper;
use \Config;
use \Database;
use \Language;
use \RestRequest;

abstract class AbstractController {

	const VIEW_PREFIX = 'view\\';

	protected $request;
	protected $config;
	protected $view;

	public function __construct(AbstractController $controller = null) {
		$this->config = Config::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH);
		$this->language = new Language();
		if (!empty($controller)) {
			$this->view = $controller->getView();
		} else {
			$this->setView();
		}
	}

	public function get($id = null) {
		return $this->_get($id);
	}

	public function post(array $data = array()) {
		return $this->_post($data);
	}

	public function put($id = null, array $data = array()) {
		return $this->_put($id, $data);
	}

	public function patch($id = null, array $data = array()) {
		return $this->_patch($id, $data);
	}

	public function delete($id) {
		return $this->_delete($id);
	}

	// returns rows with the given id or all rows when no id was given
	public function _get($id = null) {
		$result = Database::getDb($this->getTableName());
		if (!empty($id)) {
			$result->where('id', $id);
		}
		return iterator_to_array($result, false);
	}

	// returns inserted data or false if no data was inserted
	public function _post(array $data = array()) {
		$result = false;
		if (!empty($data)) {
			$result = Database::getDb($this->getTableName())->insert($data);
		}
		return $result;
	}

	// returns number of affected rows
	public function _put($id = null, array $data = array()) {
		$result = null;
		if (!empty($id)) {
			$row = Database::getDb($this->getTableName())->where('id', $id);
			if ($row) {
				$result = $row->update($data);
			}
		}
		return $result;
	}

	// returns number of affected rows
	public function _patch($id = null, array $data = array()) {
		return $this->put($id, $data);
	}

	// returns number of deleted rows
	public function _delete($id = null) {
		$result = null;
		if (!empty($id)) {
			$result = Database::getDb($this->getTableName())->where('id', $id)->delete();
		}
		return $result;
	}

	public function getTableName() {
		return substr(Helper::getLowerCaseClassName($this), 0, -1);
	}

	public function putLanguage($language) {
		// change language (cookie?)
		// set language in view instead of controller?
		$this->language->setLanguage($language);
	}

	public function renderView() {
		echo $this->view->getSiteContent();
	}

	public function setRequest(RestRequest $request) {
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
				if ($e instanceof \ClassNotFoundException) {
					throw new \exception\ViewNotFoundException($e->getMessage());
				} else {
					throw $e;
				}
			}
		} else {
			$this->view = $view;
		}
		return $this->view;
	}

}
