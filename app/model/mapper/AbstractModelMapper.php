<?php

use \Helper;
use \Database;

namespace model\mapper;

abstract class AbstractModelMapper {

	private $table;
	private $tableName;

	public function __construct() {
		$this->tableName = Helper::getLowerCaseClassName();
		$this->table = $this->getTable();
	}

	public function __toString() {
		return $this->getTableName();
	}

	public function getTable() {
		if (empty($this->table)) {
			$this->table = \Database::getDb($this->getTableName());
		}
		return $this->table;
	}

	public function getTableName() {
		return $this->tableName;
	}
}