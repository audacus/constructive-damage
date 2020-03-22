<?php

namespace model;

class User extends AbstractModel {

	protected $id;
	protected $email;
	protected $username;
	protected $salt;
	protected $password;
	protected $token;
	protected $series;
	protected $lastlogin;
	protected $loginattempts;
	protected $keymap;
	protected $avatar;
	protected $activated;

	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
		return $this;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username){
		$this->username = $username;
		return $this;
	}

	public function getSalt() {
		return $this->salt;
	}

	public function setSalt($salt){
		$this->salt = $salt;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
		return $this;
	}

	public function getToken() {
		return $this->token;
	}

	public function setToken($token){
		$this->token = $token;
		return $this;
	}

	public function getSeries() {
		return $this->series;
	}

	public function setSeries($series){
		$this->series = $series;
		return $this;
	}

	public function getLastlogin() {
		return $this->lastlogin;
	}

	public function setLastlogin($lastlogin){
		$this->lastlogin = $lastlogin;
		return $this;
	}

	public function getLoginattempts() {
		return $this->loginattempts;
	}

	public function setLoginattempts($loginattempts){
		$this->loginattempts = $loginattempts;
		return $this;
	}

	public function getKeymap() {
		return $this->keymap;
	}

	public function setKeymap($keymap){
		$this->keymap = $keymap;
		return $this;
	}

	public function getAvatar() {
		return $this->avatar;
	}

	public function setAvatar($avatar){
		$this->avatar = $avatar;
		return $this;
	}

	public function getActivated() {
		return $this->activated;
	}

	public function setActivated($activated){
		$this->activated = $activated;
		return $this;
	}
}
