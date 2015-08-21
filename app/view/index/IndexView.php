<?php

namespace view\index;

use view\AbstractView;

class IndexView extends AbstractView {

	function __construct() {
		parent::__construct();
		echo 'IndexView constructor';
	}
}