<?php

namespace controller;

class Users extends AbstractController {

	public function index() {
		die('index function');
	}

	public function get($id = null) {
		$mapperUser = new \model\mapper\User();
		foreach (\Database::getDb()->$mapperUser as $user) {
			echo print_r($user, 1);
		}
		// $users = array(
		// 	array(
		// 		'name' => 'david',
		// 		'id' => 1,
		// 	),
		// 	array(
		// 		'name' => 'thomas',
		// 		'id' => 4,
		// 	),
		// 	array(
		// 		'name' => 'severin',
		// 		'id' => 10,
		// 	),
		// 	array(
		// 		'name' => 'john',
		// 		'id' => 12,
		// 	)
		// );
		// if (!empty($id)) {
		// 	$result = array();
		// 	foreach ($users as $user) {
		// 		if ($user['id'] == $id) {
		// 			$result = $user;
		// 			break;
		// 		}
		// 	}
		// 	$users = $result;
		// }
		// return $users;
	}

	public function post(array $data = array()) {
		$mapperUser = new \model\mapper\User;




		echo '<pre>'.print_r(\Database::getDb()->$mapperUser->insert($data)->fetch(),1).'</pre>';die();

	}

	public function put($id = null, array $data = array()) {

	}

	public function patch($id = null, array $data = array()) {

	}

	public function delete($id = null) {

	}
}