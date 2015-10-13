<?php

class Helper {

	const COST = 10;
	const BLOWFISH = '$2a$%02d$';

	public static function isAjax() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	public static function parseConfig($defaultConfigPath, $configPath) {
		$defaultConfig = array();
		$config = array();
		if (file_exists($defaultConfigPath)) {
			$defaultConfig = json_decode(file_get_contents($defaultConfigPath), true);
		}
		if (file_exists($configPath)) {
			$config = json_decode(file_get_contents($configPath), true);
		}
		return array_replace_recursive($defaultConfig, $config);
	}

	public static function isCssCall() {
		return !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/css') !== false;
	}

	public static function hashPassword($username, $password) {
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		$salt = sprintf(self::BLOWFISH, self::COST).$salt;
		return crypt($password, $salt);
	}

	public static function validatePassword($password, $hash) {
		return hash_equals($hash, crypt($password, $hash));
	}
}