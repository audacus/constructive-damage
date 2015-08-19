<?php

class Helper {

	public static function isAjax() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	public static function parseConfig($configPath) {
		return parse_ini_file($configPath, true);
	}
}