<?php

namespace model;

class Event extends AbstractModel {

	protected $id;
	protected $x;
	protected $y;
	protected $w;
	protected $l;
	protected $name;
	protected $reach;
	protected $args;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getX() {
		return $this->x;
	}

	public function setX($x) {
		$this->x = $x;
		return $this;
	}

	public function getY() {
		return $this->y;
	}

	public function setY($y) {
		$this->y = $y;
		return $this;
	}

	public function getW() {
		return $this->w;
	}

	public function setW($w) {
		$this->w = $w;
		return $this;
	}

	public function getL() {
		return $this->l;
	}

	public function setL($l) {
		$this->l = $l;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getReach() {
		return $this->reach;
	}

	public function setReach($reach) {
		$this->reach = $reach;
		return $this;
	}

	public function getArgs() {
		return $this->args;
	}

	public function setArgs($args) {
		$this->args = $args;
		return $this;
	}
}