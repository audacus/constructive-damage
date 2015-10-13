<?php

namespace model;

abstract class AbstractModel {

	public function __construct(array $data = array()) {
		if (!empty($data)) {
			$this->fromArray($data);
		}
	}

	public function fromArray(array $data) {
		$methods = get_class_methods($this);
		foreach ($data as $key => $value) {
			$method = 'set'.ucfirst(strtolower($key));
			if (method_exists($this, $method)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function toArray() {
		$data = array();
		$properties = get_object_vars($this);
		foreach ($properties as $property => $value) {
			$method = 'get'.ucfirst(strtolower($property));
			if (method_exists($this, $method)) {
				$data[$property] = $this->$method();
			}
		}
		return $data;
	}
}