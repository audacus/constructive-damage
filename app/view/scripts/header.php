<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $this->config->get('app.name'); ?></title>
<?php
	// include css files
	foreach ($this->getCssFiles() as $css) {
		echo '<link type="text/css" rel="stylesheet" href="'.$css.'">'."\n";
	}

	// include js files
	foreach ($this->getJsFiles() as $js) {
		echo '<script src="'.$js.'" type="text/javascript" charset="utf-8" async defer></script>'."\n";
	}

	// make the data of the view accessible on the site as json
	echo '<script type="text/javascript">window.data = JSON.parse(\''.json_encode($this->getData()).'\');</script>'."\n";
?>
</head>
<body>
<div class="wrapper">
<h1 class="header"><?php echo $this->config->get('app.name'); ?></h1>
<div class="wrapper-content">