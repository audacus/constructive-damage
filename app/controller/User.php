<?php

namespace controller;

use controller\AbstractController;

class User extends AbstractController {

	public function getName($id = null) {
		return 'Hans';
	}
}