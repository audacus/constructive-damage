<?php

namespace controller;

use \Helper;
use \Security;

class Logout extends AbstractController {

	protected $noView = true;

	public function _get() {
		Security::logout();
		Helper::redirect();
	}
}
