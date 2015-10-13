<?php

namespace controller;

class Register extends AbstractController {

	// errors
	const NO_ERROR = false;
	// username
	const USERNAME_MIN_LENGTH = 3;
	const ERROR_MISSING_USERNAME = 'missing username!';
	const ERROR_USERNAME_TOO_SHORT = 'username must be at least %d characters long!';
	// email
	const ERROR_MISSING_EMAIL = 'missing email!';
	const ERROR_INVALID_EMAIL = 'email is invalid!';
	// password
	const PASSWORD_MIN_LENGTH = 6;
	const ERROR_MISSING_PASSWORD = 'missing password!';
	const ERROR_PASSWORD_TOO_SHORT = 'password must be at least %d characters long!';


	public function get() {

	}

	public function post(array $data = array()) {
		// TODO 2015-10-13: allow url-encoded POST
		echo '<pre>'.print_r(func_get_args(),1).'</pre>';die();
		$errors = $this->getErrors($data);
		echo '<pre>'.print_r($errors,1).'</pre>';die();
		if (empty($errors)) {

		} else {
			$this->view->setData(array('errors' => $errors));
		}
	}

	private function getErrors(array $data) {
		$errors = array();
		// username
		if (isset($data['username'])) {
			$username = $data['username'];
			if (strlen($username) < self::USERNAME_MIN_LENGTH) {
				$errors[] = sprintf(self::ERROR_USERNAME_TOO_SHORT, self::USERNAME_MIN_LENGTH);
			}
		} else {
			$errors[] = self::ERROR_MISSING_USERNAME;
		}
		// email
		if (isset($data['email'])) {
			$email = $data['email'];
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors[] = self::ERROR_INVALID_EMAIL;
			}
		} else {
			$errors[] = self::ERROR_MISSING_EMAIL;
		}
		// password
		if (isset($data['password'])) {
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