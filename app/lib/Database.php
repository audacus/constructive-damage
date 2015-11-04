<?php

class Database {

	private static $db;
	private static $dbConfig;

	public static function getDb($tableName = null) {
		if (empty(self::$db)) {
			self::setDb();
		}
		$db = self::$db;
		if (!empty($tableName)) {
			$db = self::$db->$tableName;
		}
		return $db;
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
			$structure = new NotORM_Structure_Convention(
				$primary = 'id',
				$foreign = '%s',
				$table = '%s',
				$prefix = ''
			);

			self::$db = new NotORM(new PDO($dsn, $user, $password), $structure);
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
			if (empty(self::$dbConfig)) {
				self::$dbConfig = Config::parseConfig(DEFAULT_CONFIG_PATH, CONFIG_PATH);
			}
		} else {
			self::$dbConfig($config);
		}
		return self::$dbConfig;
	}
}