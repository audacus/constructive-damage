<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo Config::get('app.name').(isset($_SERVER['PATH_INFO']) ? ' - '.substr($_SERVER['PATH_INFO'], 1) : ''); ?></title>
<?php
	// include css files
	foreach ($this->getCssFiles() as $css) {
		echo '<link type="text/css" rel="stylesheet" href="'.$css.'">'."\n";
	}

	// include js files
	foreach ($this->getJsFiles() as $js) {
		echo '<script src="'.$js.'" type="text/javascript" charset="utf-8"></script>'."\n";
	}

	// make the data of the view accessible on the site as json
	echo '<script type="text/javascript">window.data = JSON.parse(\''.json_encode($this->getData()).'\');</script>'."\n";
	// make the error of the view accessible on the site as json
	echo '<script type="text/javascript">window.error = JSON.parse(\''.json_encode($this->getError()).'\');</script>'."\n";
?>
</head>
<body>
<div class="wrapper">
<div id="userinfo">
<?php
	if (!empty($user = Security::getLoggedInUser())) {
		$username = $user->getUsername();
		echo $username.'&commat;'.Config::get('app.url.host').'&#58;'.($_SERVER['REQUEST_URI'] == '/' ? '&#126;' : $_SERVER['REQUEST_URI']).($username == 'root' ? '&#35;' : '&#36;');
	}
?>
</div>
<h1 class="header"><?php Helper::printLink(Config::get('app.name'), 'home'); ?></h1>
<div class="wrapper-content">
