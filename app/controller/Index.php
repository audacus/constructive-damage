<?php

namespace controller;

use controller\AbstractController;

class Index extends AbstractController {

	public function get() {
		$this->view->setData(array('key' => 'value'));
	}
}