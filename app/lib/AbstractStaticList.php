<?php

abstract class AbstractStaticList {

	protected static $destroyeds;
	protected static $createds;
	protected static $diffs;
	protected static $events;
	protected static $luascripts;

	public static function get($id = null) {
		if (empty(self::${strtolower(get_called_class())})) {
			self::reset();
		}
		$return = null;
		if (empty($id)) {
			$return = self::${strtolower(get_called_class())};
		} else if (!empty($id) && isset(self::${strtolower(get_called_class())}[$id])) {
			$return = self::${strtolower(get_called_class())}[$id];
		}
		return $return;
	}

	public static function set($id, $element) {
		if (empty(self::${strtolower(get_called_class())})) {
			self::reset();
		}
		$listTemp = self::${strtolower(get_called_class())};
		$listTemp[$id] = $element;
		self::${strtolower(get_called_class())} = $listTemp;
	}

	public static function remove($id) {
		$element = array();
		if (!empty(self::${strtolower(get_called_class())}) && isset(self::${strtolower(get_called_class())}[$id])) {
			$element = self::${strtolower(get_called_class())}[$id];
			unset(self::${strtolower(get_called_class())}[$id]);
		}
		return $element;
	}

	public static function add($element) {
		if (empty(self::${strtolower(get_called_class())})) {
			self::reset();
		}
		array_push(self::${strtolower(get_called_class())}, $element);
	}

	public static function reset() {
		self::${strtolower(get_called_class())} = array();
	}
}
