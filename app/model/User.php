<?php

namespace model;

class User extends AbstractModel {

	protected $id;
	protected $name;
	protected $email;
	protected $password;

	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
		return $this;
	}
}