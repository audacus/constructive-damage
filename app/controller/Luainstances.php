<?php

namespace controller;

use \Config;
use \Database;
use \Helper;
use \model\Luainstance;
use \model\Luascript;

class Luainstances extends AbstractController {

	protected $tableName = 'lua_instance';
	protected $foreignKey = 'avatar';
	protected $noView = true;

	// returns inserted data
	public function _post() {
		if (!empty($this->data)) {
			// TODO 2016-01-09 david: unset every NON-DB field from data
			$this->result = $this->getDb()->insert(
				array(
				'type' => $this->data['type']
			));
		}
		$this->result;
	}

	public function getDefaultAvatar() {
		$avatar = $this->getAvatarScript();
		$properties = array(
			'type' => $avatar->getId()
		);
		$model = new Luainstance($properties);
		return $model->toArray();
	}

	private function getAvatarScript() {
		$type = null;
		if (!empty($result = Database::resultToArray($this->getDb('lua_script')->where('name', Config::get('app.lua.avatarscript'))->order('version DESC')->limit(1)))) {
			$type = new Luascript(current($result));
		}
		return $type;
	}
}
