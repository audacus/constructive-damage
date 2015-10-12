<?php

namespace controller;

use controller\AbstractController;

class Users extends AbstractController {


	public function get($id = null) {
		echo '<pre>'.print_r(func_get_args(),1).'</pre>';die();
		$users = array(
			1 => 'david',
			4 => 'thomas',
			10 => 'severin',
			12 => 'john'
		);
		if (!empty($id)) {
			$users = $users[$id];
		}
		return $users;
	}

	public function post($data = array()) {

	}

	public function put($data = array()) {

	}

	public function patch($data = array()) {

	}

	public function delete($id = null) {

	}
}