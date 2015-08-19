<?php

class Config {

	const DEFAULT_DELIMITER = '.';
	private $array;
	private $delimiter;

	public function __construct(array $configArray) {
		$this->array = $configArray;
		$this->delimiter = self::DEFAULT_DELIMITER;
	}

	public function get($property = null, $currentPosition = null) {
		if (empty($currentPosition)) {
			$currentPosition = $this->array;
		}
		$propertyArray = explode($this->delimiter, $property);
		if (count($propertyArray) > 1 && isset($currentPosition[$propertyArray[0]])) {
			$currentPosition = $currentPosition[$propertyArray[0]];
			array_shift($propertyArray);
			$property = $this->get(implode($this->delimiter, $propertyArray), $currentPosition);
		} else {
			$property = isset($currentPosition[$property]) ? $currentPosition[$property] : null;
		}
		return $property;
	}

	public function setDelimiter($delimiter = null) {
		$this->delimiter = $delimiter;
		return $this->delimiter;
	}

}