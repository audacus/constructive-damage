<?php

use \Helper;
use \Config;

class Database {

	private static $db;
	private static $dbConfig;

	public static function getDb() {
		if (empty(self::$db)) {
			self::setDb();
		}
		return self::$db;
	}

	public static function setDb(NotORM $db = null) {
		if (empty($db)) {
			if (empty(self::$dbConfig)) {
				self::setDbConfig();
			}
			$dsn = self::$dbConfig->get('db.pdo.type')
				.':dbname='.self::$dbConfig->get('db.name')
				.';host='.self::$dbConfig->get('db.host');
			$user = self::$dbConfig->get('db.user');
			$password = self::$dbConfig->get('db.password');

			self::$db = new NotORM(new PDO($dsn, $user, $password));
		} else {
			self::$db = $db;
		}
		return self::$db;
	}

	public static function getDbConfig() {
		if (empty(self::$dbConfig)) {
			self::setDbConfig();
		}
		return self::$dbConfig;

	}

	public static function setDbConfig(Config $config = null) {
		if (empty($config)) {
			if (empty(self::$dbConfig) && file_exists(DEFAULT_CONFIG_PATH) && file_exists(CONFIG_PATH)) {
				self::$dbConfig = new Config(Helper::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH));
			}
		} else {
			self::$dbConfig($config);
		}
		return self::$dbConfig;
	}
}