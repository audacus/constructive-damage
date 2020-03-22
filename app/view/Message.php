<?php

namespace view;

class Message extends AbstractView {

	const MESSAGE = 'message';
	const MESSAGE_TITLE = 'title';
	const MESSAGE_FIRSTLINE = 'firstline';
	const MESSAGE_SECONDLINE = 'secondline';
	const MESSAGE_TEXT = 'text';

	public function __construct($title = '', $firstline = '', $secondline = '', $text = '') {
		parent::__construct(self::MESSAGE);
		$this->setData(array(
			self::MESSAGE => array(
				self::MESSAGE_TITLE => empty($title) ? self::MESSAGE_TITLE : $title,
				self::MESSAGE_FIRSTLINE => empty($firstline) ? self::MESSAGE_FIRSTLINE : $firstline,
				self::MESSAGE_SECONDLINE => empty($secondline) ? self::MESSAGE_SECONDLINE : $secondline,
				self::MESSAGE_TEXT => empty($text) ? '' : $text
			)
		));
	}

	private function getPart($part) {
		return $this->getData(self::MESSAGE)[$part];
	}

	public function getTitle() {
		return $this->getPart(self::MESSAGE_TITLE);
	}

	public function getFirstline() {
		return $this->getPart(self::MESSAGE_FIRSTLINE);
	}

	public function getSecondline() {
		return $this->getPart(self::MESSAGE_SECONDLINE);
	}

	public function getText() {
		return $this->getPart(self::MESSAGE_TEXT);
	}

	private function setPart($part, $value = '') {
		if (!empty($value)) {
			$message = $this->getData(self::MESSAGE);
			$message[$part] = $value;
			$this->setData(array(self::MESSAGE => $message));
		}
		return $this;
	}

	public function setTitle($title) {
		return $this->setPart(self::MESSAGE_TITLE, $title);
	}

	public function setFirstline($firstline) {
		return $this->setPart(self::MESSAGE_FIRSTLINE, $firstline);
	}

	public function setSecondline($secondline) {
		return $this->setPart(self::MESSAGE_SECONDLINE, $secondline);
	}

	public function setText($text) {
		return $this->setPart(self::MESSAGE_TEXT, $text);
	}
}
