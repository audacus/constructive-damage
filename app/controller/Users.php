<?php

namespace controller;

use controller\AbstractController;

class Users extends AbstractController {

	public function index() {
		die('index function');
	}


	public function get($id = null) {
		$users = array(
			array(
				'name' => 'david',
				'id' => 1,
			),
			array(
				'name' => 'thomas',
				'id' => 4,
			),
			array(
				'name' => 'severin',
				'id' => 10,
			),
			array(
				'name' => 'john',
				'id' => 12,
			)
		);
		if (!empty($id)) {
			$result = array();
			foreach ($users as $user) {
				if ($user['id'] == $id) {
					$result = $user;
					break;
				}
			}
			$users = $result;
		}
		return $users;
	}

	public function post($data = array()) {
		echo '<pre>'.print_r(func_get_args(),1).'</pre>';die();
	}

	public function put($data = array()) {

	}

	public function patch($data = array()) {

	}

	public function delete($id = null) {

	}
}