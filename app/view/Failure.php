<?php

namespace view;

class Failure extends Message {

	public function __construct($firstline = '', $secondline = '', $text = '') {
		parent::__construct('failure', $firstline, $secondline, $text);
	}
}
