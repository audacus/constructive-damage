<?php

class Helper {

	public static function isAjaxRequest() {
		return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strpos(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']), 'xmlhttprequest') !== false;
	}


	public static function isCliCall() {
		global $cli;
		return isset($cli) && !!$cli;
	}

	public static function isIterable($variable) {
		return (is_array($variable) || $variable instanceof Traversable || $variable instanceof stdClass);
	}

	public static function isAssociativeArray(array $array) {
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}

	public static function hasEmptyValue(array $array) {
		return count(array_filter(array_values($array), function($value) {
			return empty($value);
		})) > 0;
	}

	public static function getLowerCaseClassName($class) {
		$classNameParts = explode('\\', get_class($class));
		return strtolower(end($classNameParts));
	}

	public static function getRelativePath($path) {
		return Config::get('app.url.base').substr($path, strlen(APPLICATION_PATH.Config::get('app.path.public'))+1);
	}

	public static function getRelativePaths(array $paths) {
		$relativePaths = array();
		foreach ($paths as $path) {
			$relativePaths[] = self::getRelativePath($path);
		}
		return $relativePaths;
	}

	public static function redirect($url = null) {
		if (empty($url) || $url === 'home') {
			$url = Config::get('app.url.base');
		} else {
			$url = self::makePathFromParts(array(Config::get('app.url.base'), $url));
		}
		header('Location: '.$url);
		exit;
	}

	public static function getProtocol() {
		return Config::get('app.url.https') ? 'https://' : 'http://';
	}

	public static function getFullClassNameController($controllerName) {
		return self::getAsNamespace(Config::get('app.path.controller'), $controllerName);
	}

	public static function getFullClassNameView($viewName) {
		return self::getAsNamespace(Config::get('app.path.view'), $viewName);
	}

	public static function getFullClassNameModel($modelName) {
		return self::getAsNamespace(Config::get('app.path.model'), $modelName);
	}

	public static function getAsNamespace($path, $className = null) {
		$parts = explode(DIRECTORY_SEPARATOR, $path);
		if (!empty($className)) {
			array_push($parts, ucfirst(strtolower($className)));
		}
		return implode('\\', $parts);
	}

	public static function makePathFromParts($parts, $directorySeparator = null) {
		return implode((empty($directorySeparator) ? DIRECTORY_SEPARATOR : $directorySeparator), $parts);
	}

	public static function makeLink($innerHtml, $url = null, $class = null, $appendBaseUrl = true, $target = null, $print = false) {
		$baseUrl = Config::get('app.url.base');
		if (empty($url)) {
			$url = $innerHtml;
		} else if ($url === 'home') {
			$url = $baseUrl;
		}
		if ($appendBaseUrl && $url !== $baseUrl) {
			$url = self::makePathFromParts(array($baseUrl, $url), '/');
		}
		$link = '<a href="'.$url.'"'.(empty($target) ? '' : ' target="_'.$target.'"').(empty($class) ? '' : ' class="'.$class.'"').'>'.$innerHtml.'</a>';
		if ($print) {
			echo $link;
		}
		return $link;
	}

	public static function printLink($innerHtml, $url = null, $class = null, $appendBaseUrl = true, $target = null, $print = true) {
		return self::makeLink($innerHtml, $url, $class, $appendBaseUrl, $target, $print);
	}

	public static function sendPlainTextMail($to, $subject, $message) {
		return self::sendMail($to, $subject, $message, false);
	}

	public static function sendHtmlMail($to, $subject, $message) {
		return self::sendMail($to, $subject, $message, true);
	}

	public static function sendMail($to, $subject, $message, $html = true) {
		$message = $html ? '<html><body>'.$message.'</body></html>' : $message;
		$headers = "From: ".Config::get('app.mail.name')." <".Config::get('app.mail.address').">\r\nContent-Type: ".($html ? 'text/html; ' : 'text/plain; ')."charset=utf-8\r\n";
		return mail($to, $subject, $message, $headers);
	}

	public static function getValue($url, $method = 'get', array $data = array()) {
		$appUrl = self::getProtocol()
				.Config::get('app.url.host')
				.Config::get('app.url.base');
		if (strpos($url, $appUrl) !== 0 && strpos($url, 'http') !== 0) {
			$url = self::makePathFromParts(array($appUrl, $url), '/');
		}
		$opts = array(
			'http' => array(
				'method' => strtoupper($method),
				'header' => 'X-Requested-With: XMLHttpRequest; Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			)
		);
		return json_decode(file_get_contents($url, false, stream_context_create($opts)), true);
	}

	public static function prependStringToKeys(array $array, $string, $delimiter = '.') {
		$arrayMod = array();
		foreach ($array as $key => $value) {
			if (is_array($value) || is_object($value)) {
				$value = self::prependStringToKeys((array) $value, $string);
			}
			$arrayMod[$string.$delimiter.$key] = $value;
		}
		return $arrayMod;
	}
}
