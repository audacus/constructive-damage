<?php

namespace view;

class Success extends Message {

	public function __construct($firstline = '', $secondline = '', $text = '') {
		parent::__construct('success', $firstline, $secondline, $text);
	}
}
