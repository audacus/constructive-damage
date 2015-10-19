<?php

class Helper {

	const COST = 10;
	const BLOWFISH = '$2a$%02d$';

	public static function isAjax() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}
	public static function isIterable($variable) {
		return (is_array($variable) || $variable instanceof Traversable || $variable instanceof stdClass);
	}

	public static function hashPassword($username, $password) {
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		$salt = sprintf(self::BLOWFISH, self::COST).$salt;
		return crypt($password, $salt);
	}

	public static function validatePassword($password, $hash) {
		return hash_equals($hash, crypt($password, $hash));
	}

	public static function getLowerCaseClassName($class) {
		return strtolower(end(explode('\\', get_class($class))));
	}

	public static function getRelativePath($path) {
		return substr($path, strlen(APPLICATION_PATH)+1);
	}

	public static function getRelativePaths(array $paths) {
		$relativePaths = array();
		foreach ($paths as $path) {
			$relativePaths[] = self::getRelativePath($path);
		}
		return $relativePaths;
	}
}