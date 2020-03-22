<?php

namespace controller;

use \Config;
use \Database;
use \LuaHelper;
use \Security;

class Luascripts extends AbstractController {

	protected $tableName = 'lua_script';
	protected $foreignKey = 'type';
	protected $noView = true;

	// name-version-Y-m-d-H-i-s.lua
	// testyng-15-2016-01-02-18-00-18.lua
	const FILENAME_FORMAT = '%s-%d-%d-%s-%s-%s-%s-%s.lua';

	public function _get() {
		$this->result = $this->getDb();
		if (isset($this->data['editor'])) {
			$this->result->select('max(id) as id, name, source, description, max(version) as version, max(date) as date, author')
			->group('name')
			->order('name ASC');
		}
		if (!empty($this->id) && is_numeric($this->id)) {
			$this->result = $this->result[$this->id];
		}
		// check previous value
		if (!empty($fkMatch = $this->getForeignKeyMatch())) {
			$this->result = $this->result->where($fkMatch['field'], $fkMatch['value']);
		}
		$this->result;
	}

	public function modifyResultGet() {
		if (isset($this->data['editor'])) {
			$modifiedResult = array();
			foreach ($this->result as $luascript) {
				$path = LuaHelper::getLuaFilePath($luascript['source']);
				$luascript['description'] = Database::resultToArray($this->getDb()[$luascript['id']])['description'];
				if (file_exists($path)) {
					$luascript['source'] = base64_encode(file_get_contents($path));
					if (!empty($luascript['author'])) {
						$author = null;
						$result = Database::resultToArray($this->getDb('user')[$luascript['author']]);
						if (!empty($result)) {
							$author = $result['username'];
						}
						$luascript['author'] = $author;
					}
					$modifiedResult[] = $luascript;
				}
			}
		$this->result = $modifiedResult;
		}
	}

	// {"name":"testyng","description":"gagel","source":"LS0gLS0tLS0gYXV0b21hdGVkIGFkZGVkLCBkbyBub3QgcmVtb3ZlIC0tLS0tIC0tDQpnb2QgPSByZXF1aXJlKCJnb2QiKQ0KbG9jYWwgbWVidWcgPSByZXF1aXJlKCJtZWJ1ZyIpDQotLSAtLS0tLSBlbmQgLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0gLS0NCg0KDQptZSA9IHt9DQoNCmZ1bmN0aW9uIG1lOnJlbmRlcigpDQoJcmV0dXJuICJtZSINCmVuZA0KDQpmdW5jdGlvbiBtZTpjcmVhdGUoKQ0KCXNlbGYudmFsID0gMA0KDQplbmQNCg0KZnVuY3Rpb24gbWU6dXBkYXRlKCkNCglzZWxmLnZhbCA9IHNlbGYudmFsICsgMQ0KICAgIHNlbGYuY29sb3IgPSBiaXQzMi5sc2hpZnQobWF0aC5yYW5kb20oMTAwLCAyNTApLCAxNikgKyBiaXQzMi5sc2hpZnQobWF0aC5yYW5kb20oMTAwLCAyNTApLCA4KSArIG1hdGgucmFuZG9tKDEwMCwgMjUwKTsNCmVuZA0KDQpmdW5jdGlvbiBtZTp0b3N0cmluZygpDQogICAgcmV0dXJuICJ4OiAiLi5zZWxmLnguLiIgeTogIi4uc2VsZi55DQplbmQNCg0KZnVuY3Rpb24gbWU6bW92ZShsZWZ0LCBkb3duLCB1cCwgcmlnaHQpDQogICAgc2VsZi54ID0gc2VsZi54IC0gbGVmdCArIHJpZ2h0DQogICAgc2VsZi55ID0gc2VsZi55IC0gZG93biArIHVwDQplbmQNCg0KZ29kLm9iamVjdCA9IG1lDQpnb2QucmVuZGVyID0gbWUucmVuZGVyDQpnb2QuY3JlYXRlID0gbWUuY3JlYXRlIC0tIGNhbGxlZCB0aGUgdmVyeSBmaXJzdCB0aW1lIG9uY2UsIGFuZCB0aGVuIG5ldmVyIGFnYWluDQpnb2QuZGVzdHJveSA9IG5pbCAtLSBzaW1pbGFyIHRvIGdvZC5jcmVhdGUNCmdvZC5zZXR1cCA9IG5pbCAtLSBjYWxsZWQgZXZlcnkgdGltZSB0aGUgb2JqZWN0IGdldHMgY3JlYXRlZA0KZ29kLnRlYXJkb3duID0gbmlsIC0tIHNpbWlsYXIgdG8gZ29kLnNldHVwDQpnb2QudG9zdHJpbmcgPSBtZS50b3N0cmluZw0KZ29kLnVwZGF0ZSA9IG1lLnVwZGF0ZQ0KZ29kLm1vdmUgPSBtZS5tb3ZlDQoNCi0tIC0tLS0NCg=="}
	public function _post() {
		$version = 1;
		$date = new \DateTime();

		$luascript = new $this->model($this->data);

		// get version
		$this->result = $this->getDb()
				->where('name', $luascript->getName())
				->max('version');
		if (!empty($this->result)) {
			$version = intval($this->result)+1;
		}

		// set values to model
		$author = null;
		if (!empty($user = Security::getLoggedInUser())) {
			$author = $user->getId();
		}
		$luascript
			->setId(null) // set id to null due to insert
			->setVersion($version)
			->setDate($date->format(Config::get('app.date.format.long')))
			->setAuthor($author)
			->setSource(sprintf(self::FILENAME_FORMAT,
				$luascript->getName(),
				$luascript->getVersion(),
				$date->format('Y'),
				$date->format('m'),
				$date->format('d'),
				$date->format('H'),
				$date->format('i'),
				$date->format('s')
			)
		);

		// insert into db
		$this->result = Database::resultToArray($this->getDb()->insert($luascript->toArray()));

		// write source code to file
		$path = str_replace(' ', '\\ ', LuaHelper::getLuaFilePath($luascript->getSource()));
		try {
			$file = fopen($path, 'c');
			if ($file !== false) {
				$written = fwrite($file, base64_decode($this->data['source']));
				if ($written === false) {
					throw new CouldNotWriteToFileException($file);
				}
				fclose($file);
			}
		} catch (\Exception $e) {
			throw $e;
		}
		$this->result = LuaHelper::modifyErrors(trim(shell_exec('luacheck -ga --no-color '.$path)));
	}
}
