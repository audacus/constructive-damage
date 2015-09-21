<?php

use \Bootstrap;
use \Rest;

class App {

	public function __construct() {
		// bootstrap
		new Bootstrap();
		// rest
		new Rest();
	}
}