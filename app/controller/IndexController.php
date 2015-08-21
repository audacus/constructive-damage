<?php

namespace controller;

use view\index\IndexView;

class IndexController {

	function __construct() {
		echo 'IndexController constructor<br />';
		new IndexView();
	}
}

