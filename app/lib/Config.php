<?php

class Config {

	const DEFAULT_DELIMITER = '.';
	private static $array = array();
	private static $delimiter = self::DEFAULT_DELIMITER;

	public static function get($property = null, $currentPosition = null) {
		if (empty($currentPosition)) {
			$currentPosition = self::$array;
		}
		$propertyArray = explode(self::$delimiter, $property);
		if (count($propertyArray) > 1 && isset($currentPosition[$propertyArray[0]])) {
			$currentPosition = $currentPosition[$propertyArray[0]];
			array_shift($propertyArray);
			$property = self::get(implode(self::$delimiter, $propertyArray), $currentPosition);
		} else {
			$property = isset($currentPosition[$property]) ? $currentPosition[$property] : null;
		}
		return $property;
	}

	public static function setDelimiter($delimiter = null) {
		if (!empty($delimiter)) {
			$delimiter = self::DEFAULT_DELIMITER;
		}
		self::$delimiter = $delimiter;
		return self::$delimiter;
	}

	public static function setConfig(array $config) {
		self::$array = $config;
	}

	public static function parseConfig($defaultConfigPath, $configPath) {
		$defaultConfig = array();
		$config = array();
		if (file_exists($defaultConfigPath)) {
			$defaultConfig = json_decode(file_get_contents($defaultConfigPath), true);
			if (is_null($defaultConfig)) {
				throw new JsonDecodeException('Could not decode config file: '.$defaultConfigPath);
			}
		}
		if (file_exists($configPath)) {
			$config = json_decode(file_get_contents($configPath), true);
			if (is_null($config)) {
				throw new JsonDecodeException('Could not decode config file: '.$defaultConfigPath);
			}
		}
		return array_replace_recursive($defaultConfig, $config);
	}

	public static function parseAndSetConfig($defaultConfigPath, $configPath) {
		self::setConfig(self::parseConfig($defaultConfigPath, $configPath));
	}
}
