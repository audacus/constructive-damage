<?php

namespace model;

use \LuaHelper;
use \RunCycle;

class Luainstance extends AbstractModel {

	protected $id;
	protected $json;
	protected $type;
	protected $source;
	protected $lua;

	public function initLua() {
		if (!empty($this->source)) {
			$this->lua = new \Lua(LuaHelper::getLuaFilePath($this->source));

			$this->lua->registerCallback("getparent", array("LuaHelper", "getparent"));
			$this->lua->registerCallback("getchildren", array("LuaHelper", "getchildren"));
			$this->lua->registerCallback("setaschildof", array("LuaHelper", "setaschildof"));
			$this->lua->registerCallback("triggerevent", array("LuaHelper", "triggerevent"));
			$this->lua->registerCallback("createobjectbyname", array("LuaHelper", "createobjectbyname"));
			$this->lua->registerCallback("destroyme", array("LuaHelper", "destroyme"));
			$this->lua->registerCallback("getobjectargs", array("LuaHelper", "getobjectargs"));
			$this->lua->registerCallback("getcreatorobjectid", array("LuaHelper", "getcreatorobjectid"));
			$this->lua->registerCallback("receivearguments", array("LuaHelper", "receivearguments"));
			$this->lua->registerCallback("passarguments", array("LuaHelper", "passarguments"));
            $this->lua->registerCallback("writeoutdebugmessage", array("LuaHelper", "writeoutdebugmessage"));

			$create = false;

			if (empty($this->json) || $this->json === "[]"){ // ??? why is sometimes in database a single []
				$this->json = "{\"id\":".$this->id."}";
				$create = true;
			}

			$this->import();

			if ($create)
				$this->create();
		} else {
            throw new \Exception('source not set for a unknown object');
		}
	}

	public function create(){
		$this->lua->eval('god:_create()');
   //     $diff = RunCycle::diffarrays(json_decode($this->json, true), json_decode($this->teardown(), true));
   //     RunCycle::updatejson($diff, $this->id);
	}

	public function setup(){
		$this->lua->eval("god:_setup()");
	}

	public function teardown(){
		return $this->export();
	}

	public function import($json = null) {
        $j = empty($json) ? $this->json : $json;
		$this->lua->eval('god:_import(\''.$j.'\')');
	}

	private function export(){
		return $this->lua->eval("return god:_export()");
	}

	public function handlers(){
		return $this->lua->eval("return god:_handlers()");
	}

	public function triggerevent($eventname, $eventargs){
		$this->lua->eval("god:_triggerevent('".$eventname."', '".json_encode($eventargs)."')");
	}

	public function tostring(){
		return $this->lua->eval('return god:_tostring()');
	}

	public function update(){
		$this->lua->eval('god:_update()');
	}

	public function keypress($keycodes){
		$this->lua->eval('god:_keypress(\''.json_encode($keycodes).'\')');
	}

	/* getter & setter */
	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getJson() {
		return $this->json;
	}

	public function setJson($json){
		$this->json = $json;
		return $this;
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type){
		$this->type = $type;
		return $this;
	}

	public function getSource() {
		return $this->source;
	}

	public function setSource($source){
		$this->source = $source;
		return $this;
	}

	public function getLua() {
		return $this->lua;
	}

	public function setLua($lua) {
		$this->lua = $lua;
		return $this;
	}
}
