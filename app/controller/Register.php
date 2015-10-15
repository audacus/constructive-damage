<?php

namespace controller;

class Register extends AbstractController {

	public function get() {

	}

	public function post(array $data = array()) {
		return (new Users($this))->post($data);
	}
}