<?php

namespace controller;

class Posts extends AbstractController {

	public function get($id = null, $user = null) {
		$posts = array(
			array(
				'id' => 2,
				'user' => 1,
				'content' => 'Hi.',
			),
			array(
				'id' => 24,
				'user' => 4,
				'content' => 'i got dis fiiileng',
			),
			array(
				'id' => 33,
				'user' => 10,
				'content' => '\'; drop database; -- ',
			),
			array(
				'id' => 45,
				'user' => 10,
				'content' => '£¨ü¨.-¨ü.-¨ü-¨ü-¨ü134970192348/)=&()ç%&+?*(/?ç+',
			),
			array(
				'id' => 60,
				'user' => 10,
				'content' => 'echo $post',
			),
			array(
				'id' => 111,
				'user' => 12,
				'content' => 'Some post.',
			),
			array(
				'id' => 120,
				'user' => 12,
				'content' => 'random text',
			)
		);
		if (!empty($user)) {
			$result = array();
			foreach ($posts as $post) {
				if ($post['user'] == $user['id']) {
					array_push($result, $post);
				}
			}
			$posts = $result;
		}
		if (!empty($id)) {
			$result = array();
			foreach ($posts as $post) {
				if ($post['id'] == $id) {
					$result = $post;
				}
			}
			$posts = $result;
		}
		return $posts;
	}
}