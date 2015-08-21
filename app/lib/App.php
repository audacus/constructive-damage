<?php

namespace lib;

use controller\IndexController;

class App {

	function __construct() {
		echo 'App consturctor<br />';
		new IndexController();
	}
}