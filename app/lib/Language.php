<?php

class Language {

	const ENGLISH = 'en';
	const GERMAN = 'de';
	private $languages = array(self::ENGLISH, self::GERMAN);
	private $language;

	public function __construct() {
		// read language out of cookie?
	}

	public function getLanguage() {

	}

	public function setLanguage($language) {
		if (in_array(strtolower($language), $this->languages)) {
			$this->language = strtolower($language);
		}
		return $this->language;
	}
}