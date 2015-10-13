<?php

namespace controller;

class Comments extends AbstractController {

	public function get($id = null, $post = array()) {
		$comments = array(
			array(
				'id' => 3,
				'post' => 2,
				'text' => 'frist!',
			),
			array(
				'id' => 54,
				'post' => 2,
				'text' => 'i hate that',
			),
			array(
				'id' => 56,
				'post' => 45,
				'text' => 'blablabla',
			),
			array(
				'id' => 67,
				'post' => 120,
				'text' => 'Sarude Dandstorm',
			),
			array(
				'id' => 68,
				'post' => 60,
				'text' => 'nobody cares',
			),
			array(
				'id' => 24,
				'post' => 12,
				'text' => 'sooo cute ^.^',
			),
			array(
				'id' => 99,
				'post' => 60,
				'text' => 'to the winnndooooooowwwww \(._.\) to the waallll (/._.)/',
			)
		);
		if (!empty($post)) {
			$result = array();
			foreach ($comments as $comment) {
				if ($comment['post'] == $post['id']) {
					array_push($result, $comment);
				}
			}
			$comments = $result;
		}
		if (!empty($id)) {
			$result = array();
			foreach ($comments as $comment) {
				if ($comment['id'] == $id) {
					$result = $comment;
				}
			}
			$comments = $result;
		}
		return $comments;
	}
}