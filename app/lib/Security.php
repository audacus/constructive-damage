<?php

use model\User;

/*
 * This class provides functions for a secure application.
 * This class contains methods for hashing the password, generating tokens and login stuff.
 * With a database connection it performs checks against the database to maintain a secure request.
 */
class Security {

	const GENERATE_TOKEN_LENGTH = 24;
	const GENERATE_PASSWORD_HASH_LOOPS = 1024;
	const GENERATE_PASSWORD_HASH_ALGO = 'sha512';
	const GENERATE_PASSWORD_HASH_RAW_OUTPUT = true;

	public static function generateToken() {
		return base64_encode(openssl_random_pseudo_bytes(self::GENERATE_TOKEN_LENGTH));
	}

	public static function generatePasswordHash($password, $salt) {
		$passwordHash = $password.$salt;
		for ($i = 0; $i < self::GENERATE_PASSWORD_HASH_LOOPS; $i++) {
			$passwordHash = hash(self::GENERATE_PASSWORD_HASH_ALGO, $passwordHash, self::GENERATE_PASSWORD_HASH_RAW_OUTPUT);
		}
		return base64_encode($passwordHash);
	}

	public static function verifyPassword(model\User $user, $password, $db) {
		return self::generatePasswordHash($password, $user->getSalt()) === $db;
	}

	public static function loginUser(model\User $user, array $data = array()) {
		// echo __METHOD__.'<br />';
		$user = self::refreshUser($user, true);
		// if stay logged in -> set cookie
		if (isset($data['persistent'])) {
			Security::setCookie($user);
		// else set session
		} else {
			Security::setSession($user);
		}
	}

	public static function getLoggedInUser() {
		// echo __METHOD__.'<br />';
		$user = null;
		$array = null;
		if (self::verifySession()) {
			$array = $_SESSION;
		} else if (self::verifyCookie()) {
			$array = $_COOKIE;
		}
		if (!empty($array)) {
			$user = new User(current(Database::resultToArray(Database::getDb('user')->where('username', $array['username']))));
		}
		return $user;
	}

	public static function isLoggedIn() {
		// echo __METHOD__.'<br />';
		return !empty(self::getLoggedInUser());
	}

	public static function logout() {
		// echo __METHOD__.'<br />';
		if (self::isLoggedIn()) {
			self::killSession();
			self::killCookie();
		}
	}

	public static function killCookie() {
		// echo __METHOD__.'<br />';
		if (isset($_COOKIE)) {
			$expire = new \DateTime('now -'.Config::get('app.security.cookie.expire'));
			if (isset($_COOKIE['username'])) {
				setcookie('username', null, $expire->getTimestamp());
				unset($_COOKIE['username']);
			}
			if (isset($_COOKIE['series'])) {
				setcookie('series', null, $expire->getTimestamp());
				unset($_COOKIE['series']);
			}
			if (isset($_COOKIE['token'])) {
				setcookie('token', null, $expire->getTimestamp());
				unset($_COOKIE['token']);
			}
		}
	}

	public static function killSession() {
		// echo __METHOD__.'<br />';
		session_start();
		if (isset($_SESSION)) {
			session_destroy();
		}
	}

	public static function doesCookieExist() {
		// echo __METHOD__.'<br />';
		return isset($_COOKIE['username'])
			&& isset($_COOKIE['series'])
			&& isset($_COOKIE['token']);
	}

	public static function doesSessionExist() {
		// echo __METHOD__.'<br />';
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		return isset($_SESSION['username'])
			&& isset($_SESSION['series'])
			&& isset($_SESSION['token'])
			&& session_status() === PHP_SESSION_ACTIVE;
	}

	public static function setCookie(model\User $user) {
		// echo __METHOD__.'<br />';
		$expire = new \DateTime('now +'.Config::get('app.security.cookie.expire'));
		setcookie('username', $user->getUsername(), $expire->getTimestamp());
		setcookie('series', $user->getSeries(), $expire->getTimestamp());
		setcookie('token', $user->getToken(), $expire->getTimestamp());
		$_COOKIE['username'] = $user->getUsername();
		$_COOKIE['series'] = $user->getSeries();
		$_COOKIE['token'] = $user->getToken();
	}

	public static function setSession(model\User $user) {
		// echo __METHOD__.'<br />';
		if (!self::doesSessionExist()) {
			session_start();
		}
		session_regenerate_id(true);
		$_SESSION['username'] = $user->getUsername();
		$_SESSION['series'] = $user->getSeries();
		$_SESSION['token'] = $user->getToken();
	}

	public static function update(model\User $user = null) {
		// echo __METHOD__.'<br />';
		$updateCookie = false;
		$updateSession = false;
		if (empty($user)) {
			$user = self::getLoggedInUser();
		}
		if (!empty($user)) {
			if (self::doesSessionExist() && self::verifySession()) {
				$updateSession = true;
			} else if (self::doesCookieExist() && self::verifyCookie()) {
				$updateCookie = true;
			}
			$user = self::refreshUser($user);
			if ($updateCookie) {
				self::setCookie($user);
			}
			if ($updateSession) {
				self::setSession($user);
			}
		}
		return $user;
	}

	public static function verifyCookie() {
		// echo __METHOD__.'<br />';
		return self::verify($_COOKIE);
	}

	public static function verifySession() {
		// echo __METHOD__.'<br />';
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		return self::verify($_SESSION);
	}

	public static function refreshUser(model\User $user, $refreshSeries = false) {
		// echo __METHOD__.'<br />';
		$user->setLoginattempts(0)
			->setLastlogin((new \DateTime('now'))->format(Config::get('app.date.format.long')))
			->setToken(Security::generateToken());
		if ($refreshSeries) {
			$user->setSeries(Security::generateToken());
		}
		if (!empty($id = $user->getId())) {
			// persist updated user to db
			$tableUser = Database::getDb('user');
			$tableUser[$id]->update(array(
					'loginattempts' => $user->getLoginattempts(),
					'lastlogin' => $user->getLastlogin(),
					'token' => $user->getToken(),
					'series' => $user->getSeries()
				)
			);
		}
		return $user;
	}

	private static function verify(array $global) {
		// echo __METHOD__.'<br />';
		$valid = false;
		$tableUser = Database::getDb('user');
		if (isset($global['username'])) {
			$result = Database::resultToArray($tableUser->where('username', $global['username']));
			// username
			if (!empty($result)) {
				$user = new User(current($result));
				// token
				// if ($global['token'] === $user->getToken()) {
					// series
					if ($global['series'] === $user->getSeries() && $user->getActivated()) {
						$valid = true;
					} else {
						// echo 'security: series does not match!!!<br />';
					}
				// } else {
				// 	echo 'TOKEN DOES NOT MATCH!!!<br />';
				// }
			} else {
				// echo 'security: username does not match!!!<br />';
			}
		}
		return $valid;
	}
}
