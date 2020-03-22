<?php

namespace model;

abstract class AbstractModel {

	const METHOD_GET_ID = 'getId';

	public function __construct(array $data = array(), array $initArgs = array()) {
		if (!empty($data)) {
			$this->fromArray($data);
		}
		$this->init($initArgs);
	}

	protected function init($initArgs) {}

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
				$value = $this->$method();
				// get id if property is a model instance
				if ($value instanceof AbstractModel) {
					if (method_exists($value, self::METHOD_GET_ID)) {
						$value = $value->getId();
					} else {
						$value = null;
					}
				}
				$data[$property] = $value;
			}
		}
		return $data;
	}
}
