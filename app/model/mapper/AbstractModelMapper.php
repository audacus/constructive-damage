<?php

use \Helper;

namespace model\mapper;

abstract class AbstractModelMapper {

	private $table;
	private $tableName;

	public function __construct() {
		$this->tableName = Helper::getLowerCaseClassName();
	}

	public function __toString() {
		return $this->getTableName();
	}

	public function getDb() {

	}

	public function getTableName() {
		return $this->tableName;
	}
}