<?php

namespace controller;

class Index extends AbstractController {

	public function get() {
		$this->view->setData(array('key' => 'value'));
	}
}