<?php

namespace controller;

use model\Luainstance;
use \Config;
use \Database;
use \Helper;
use \Security;

class Users extends AbstractController {

	protected $noView = true;
	protected $foreignKey = 'author';

	public function modifyResultGet() {
		// for security reasons
		if (Helper::isAjaxRequest()) {
			$modifiedResult = array();
			foreach ($this->result as $result) {
				$result['email'] = 'cnAuYXN0bGV5QHBvbHlkb3Iub3Jn';
				unset($result['salt']);
				$result['password'] = 'fedef920f598b065584fb00d2bce58d6';
				unset($result['token']);
				unset($result['series']);
				$modifiedResult[] = $result;
			}
			$this->result = $modifiedResult;
		}
	}

	public function _post() {
		$this->result = null;
		if ($this->validateData($this->data)) {
			$user = new $this->model($this->data);
			$user->setSalt(Security::generateToken())
				->setToken(Security::generateToken())
				->setSeries(Security::generateToken())
				->setKeymap(self::getDefaultKeymap())
				->setPassword(Security::generatePasswordHash($user->getPassword(), $user->getSalt()))
				->setAvatar($this->generateAvatar());
			$array = $user->toArray();
			// unset fields for db insert
			unset($array['id'], $array['lastlogin'], $array['loginattempts'], $array['activated']);
			$this->result = Database::resultToArray($this->getDb()->insert($array));
		}
		return $this->result;
	}

	private function generateAvatar() {
		$controllerLuainstances = new Luainstances();
		$result = $controllerLuainstances->post($controllerLuainstances->getDefaultAvatar());
		return $controllerLuainstances->newModel($result);
	}

	private function validateData($data) {
		$valid = true;
		// if user with username already exist
		if (!isset($data['username'])) {
			$this->view->appendError('no username given!');
			$valid = false;
		} else if (count($this->getDb()->where('username like ?', $data['username'])) > 0) {
			$this->view->appendError('username is already in use!');
			$valid = false;
		}
		// if entered passwords the same
		if (!isset($data['password'])) {
			$this->view->appendError('no password given!');
			$valid = false;
		} else if ($data['password'] !== $data['password-repeat']) {
			$this->view->appendError('passwords are not the same!');
			$valid = false;
		}
		// if user with email already exist
		if (!isset($data['email'])) {
			$this->view->appendError('no email given!');
			$valid = false;
		} else if (count($this->getDb()->where('email like ?', $data['email'])) > 0) {
			$this->view->appendError('email is already in use!');
			$valid = false;
		}
		return $valid;
	}

	private static function getDefaultKeymap() {
		return '{"some":"json"}';
	}
}
