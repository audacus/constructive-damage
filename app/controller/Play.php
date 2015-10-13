<?php

namespace controller;

class Play extends AbstractController {

	public function get() {
		$this->view->setData($_SERVER['HTTP_USER_AGENT']);
	}
}