<?php

namespace controller;

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

	public function get($id = null) {
		$users = array();
		$result = \Database::getDb('user');
		if (!empty($id)) {
			$result->where('id', $id);
		}
		return iterator_to_array($result);
	}

	public function post(array $data = array()) {
		$tableUser = 'user';
		$result = null;
		$errors = $this->getErrors($data);
		if (empty($errors)) {
			// add user
			unset($data['register']);
			$data['password'] = \Helper::hashPassword($data['username'], $data['password']);
			$result = \Database::getDb($tableUser)->insert($data);
			if ($result === false) {
				throw new exception\DatabaseError();
			}
		} else {
			$this->view->setData(array('errors' => $errors));
		}
		return $result;
	}

	public function put($id = null, array $data = array()) {

	}

	public function patch($id = null, array $data = array()) {

	}

	public function delete($id = null) {

	}

	private function getErrors(array $data) {
		$errors = array();
		// username
		if (isset($data['username']) && !empty($data['username'])) {
			$username = $data['username'];
			if (strlen($username) < self::USERNAME_MIN_LENGTH) {
				$errors[] = sprintf(self::ERROR_USERNAME_TOO_SHORT, self::USERNAME_MIN_LENGTH);
			} else {
				if (count(\Database::getDb($this->getTableName())->where('username = ?', $data['username'])) > 0) {
					$errors[] = self::ERROR_USERNAME_ALREADY_USED;
				}
			}
		} else {
			$errors[] = self::ERROR_MISSING_USERNAME;
		}
		// email
		if (isset($data['email']) && !empty($data['email'])) {
			$email = $data['email'];
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors[] = self::ERROR_INVALID_EMAIL;
			} else {
				if (count(\Database::getDb($this->getTableName())->where('email', $data['email'])) > 0) {
					$errors[] = self::ERROR_EMAIL_ALREADY_USED;
				}
			}
		} else {
			$errors[] = self::ERROR_MISSING_EMAIL;
		}
		// password
		if (isset($data['password']) && !empty($data['password'])) {
			$password = $data['password'];
			if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
				$errors[] = sprintf(self::ERROR_PASSWORD_TOO_SHORT, self::PASSWORD_MIN_LENGTH);
			}
		} else {
			$errors[] = self::ERROR_MISSING_PASSWORD;
		}
		return $errors;
	}
}