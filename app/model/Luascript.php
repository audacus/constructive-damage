<?php

namespace model;

class Luascript extends AbstractModel {

	protected $id;
	protected $name;
	protected $description;
	protected $source;
	protected $version;
	protected $date;
	protected $author;

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

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description){
		$this->description = $description;
		return $this;
	}

	public function getSource() {
		return $this->source;
	}

	public function setSource($source){
		$this->source = $source;
		return $this;
	}

	public function getVersion() {
		return $this->version;
	}

	public function setVersion($version){
		$this->version = $version;
		return $this;
	}

	public function getDate() {
		return $this->date;
	}

	public function setDate($date){
		$this->date = $date;
		return $this;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function setAuthor($author){
		$this->author = $author;
		return $this;
	}
}
