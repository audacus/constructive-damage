<?php

namespace controller;

use \Config;
use \Database;
use \Helper;
use \Security;
use model\User;

class Login extends AbstractController {

	const ERROR_WRONG_EMAIL_OR_USERNAME = 'wrong email or username!';
	const ERROR_WRONG_PASSWORD = 'wrong password!';

	public function _get() {
		if (Security::isLoggedIn()) {
			if (isset($this->data['redirect'])) {
				Helper::redirect($this->data['redirect']);
			} else {
				Helper::redirect(Config::get('app.defaultcontroller'));
			}
		}
	}

	public function _post() {
		$this->login($this->data);
	}

	private function login($data) {
		$invalid = true;
		$tableUser = $this->getDb((new Users)->getTableName());
		$this->result = Database::resultToArray($tableUser->where('username', $this->data['username'])
			->or('email', $this->data['username'])
		);
		if (!empty($this->result)) {
			$user = new User(current($this->result));
			if ($user->getActivated()) {
				if (Security::verifyPassword($user, $this->data['password'], $user->getPassword())) {
					// logged in
					$invalid = false;
					Security::loginUser($user, $this->data);
					Helper::getValue('game?a=start');
					// redirect
					if (isset($this->data['redirect'])) {
						Helper::redirect($this->data['redirect']);
					// default -> redirect to game controller
					} else {
						Helper::redirect(Config::get('app.defaultcontroller'));
					}
				} else {
					$tableUser->where('id', $user->getId())->update(array('loginattempts' => $user->getLoginattempts()+1));
					$this->view->appendError(self::ERROR_WRONG_PASSWORD);
				}
			} else {
				$this->view->appendError(self::ERROR_WRONG_EMAIL_OR_USERNAME);
			}
		} else {
			$this->view->appendError(self::ERROR_WRONG_EMAIL_OR_USERNAME);

		}
		if ($invalid) {
			// spend some time gnerating things
			Security::generatePasswordHash(Security::generateToken(), Security::generateToken());
		}
		// set email/username to view
		unset($this->data['password']);
		$this->view->setData(array('formdata' => $this->data));
	}
}
