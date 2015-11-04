<?php

namespace controller;

use \Helper;
use \Database;

class Users extends AbstractController {

	// errors
	const NO_ERROR = false;
	// username
	const USERNAME_MIN_LENGTH = 3;
	const ERROR_MISSING_USERNAME = 'missing username!';
	const ERROR_USERNAME_TOO_SHORT = 'username must be at least %d characters long!';
	const ERROR_USERNAME_ALREADY_USED = 'username is already in use!';
	// email
	const ERROR_MISSING_EMAIL = 'missing email!';
	const ERROR_INVALID_EMAIL = 'email is invalid!';
	const ERROR_EMAIL_ALREADY_USED = 'email is already in use!';
	// password
	const PASSWORD_MIN_LENGTH = 6;
	const ERROR_MISSING_PASSWORD = 'missing password!';
	const ERROR_PASSWORD_TOO_SHORT = 'password must be at least %d characters long!';

	public function index() {
		die('index function');
	}

	public function post(array $data = array()) {
		$result = array('insert' => false);
		$errors = $this->getErrors($data);
		if (empty($errors)) {
			// add user
			unset($data['register']);
			$data['password'] = Helper::hashPassword($data['username'], $data['password']);
			$result['insert'] = $this->_post($data);
		} else {
			$result['errors'] = $errors;
			$this->view->setData(array('errors' => $errors));
		}
		return $result;
	}

	public function put($id = null, array $data = array()) {
		$result = array('update' => false);
		if (!empty($id)) {
			$row = Database::getDb($this->getTableName())->where('id', $id);
			if ($row) {
				// needed to check errors detailed
				$data['id'] = $id;
				// username shall not be changed
				if (isset($data['username'])) {
					unset($data['username']);
				}
				$errors = $this->getErrors($data);
				if (empty($errors)) {
					if (isset($data['password'])) {
						$data['password'] = Helper::hashPassword($row['username'], $data['password']);
					}
					$result['update'] = $this->_put($id, $data);
				} else {
					$result['errors'] = $errors;
				}
			}
		}
		return $result;
	}

	// if data[id] is set -> check for update else -> check for insert
	private function getErrors(array $data) {
		$errors = array();
		// username
		if (isset($data['username']) && !empty($data['username'])) {
			$username = $data['username'];
			if (strlen($username) < self::USERNAME_MIN_LENGTH) {
				$errors[] = sprintf(self::ERROR_USERNAME_TOO_SHORT, self::USERNAME_MIN_LENGTH);
			} else {
				if (count(Database::getDb($this->getTableName())->where('username = ?', $data['username'])) > 0) {
					$errors[] = self::ERROR_USERNAME_ALREADY_USED;
				}
			}
		} else {
			if (!isset($data['id'])) {
				$errors[] = self::ERROR_MISSING_USERNAME;
			}
		}
		// email
		if (isset($data['email']) && !empty($data['email'])) {
			$email = $data['email'];
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors[] = self::ERROR_INVALID_EMAIL;
			} else {
				$rows = Database::getDb($this->getTableName())->where('email', $data['email']);
				if (count($rows) > 0) {
					if (!isset($data['id'])
						|| (isset($data['id']) && count($rows) > 1)
						|| (isset($data['id']) && count($rows) === 1 && count($rows->where('id', $data['id'])) === 0)) {
						$errors[] = self::ERROR_EMAIL_ALREADY_USED;
					}
				}
			}
		} else {
			if (!isset($data['id'])) {
				$errors[] = self::ERROR_MISSING_EMAIL;
			}
		}
		// password
		if (isset($data['password']) && !empty($data['password'])) {
			$password = $data['password'];
			if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
				$errors[] = sprintf(self::ERROR_PASSWORD_TOO_SHORT, self::PASSWORD_MIN_LENGTH);
			}
		} else {
			if (!isset($data['id'])) {
				$errors[] = self::ERROR_MISSING_PASSWORD;
			}
		}
		return $errors;
	}
}