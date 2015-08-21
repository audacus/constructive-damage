<?php

class Helper {

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
}