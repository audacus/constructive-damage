<?php

use model\Event;

/* provides functions to Lua-Scripts so that they can communicate with the Game-Engine */
class LuaHelper {

	// getparent (gets id of parent
	public static function getparent($self){
		$objects = Database::resultToArray(Database::getDb('children')->where('child', $self['id']));
		if (!empty($objects)) {
			return current($objects)["parent"];
		}
		return -1;
	}

	// getchildren (get id array of children)
    public static function getchildren($self){
		$objects = Database::resultToArray(Database::getDb('children')->where('parent', $self['id']));
        $ids = array();
        foreach($objects as $rec){
            $ids[] = $rec['child'];
        }
		return $ids;
	}

    /* move object between layers */
	public static function setaschildof($self, $parent){
		Database::getDb('children')->where('child', $self['id'])->delete();
        Database::getDb('children')->insert(array('parent' => $parent, 'child' => $self['id']));
	}

    /* triggers an event */
	public static function triggerevent($self, $eventname, $distanceofimpact, $eventargs){
		// save event for later execution
		$event = array(
			"id" => $self["id"],
			"x" => $self["x"],
			"y" => $self["y"],
			"w" => $self["width"],
			"l" => $self["length"],
			"name" => $eventname,
			"reach" => $distanceofimpact,
			"args" => $eventargs
		);
		$model = (new Event($self))
			->setW($self['width'])
			->setL($self['length'])
			->setName($eventname)
			->setReach($distanceofimpact)
			->setArgs($eventargs);
		Events::add($model);
	}

    /* creates a new object */
	public static function createobjectbyname($self, $objectname){
        $creation = array(
            "creator" => $self["id"],
            "type" => reset(Database::resultToArray(Database::getDb('lua_script')->where('name', $objectname)))['id']);
        Createds::add($creation);
	}

    /* deletes the current object */
	public static function destroyme($self){
		Destroyeds::add($self['id']);
	}

    /* get all properties of an object */
	public static function getobjectargs($self, $id){
		$objects = Database::resultToArray(Database::getDb('lua_instance')[$id]);
		if (!empty($objects)) {
			// $return = json_decode($objects["json"]);
            return $objects["json"];
		}
		return "{}";
	}

    /* get id of creator */
	public static function getcreatorobjectid($self){
		$return = null;
		$objects = array();
		$objects = Database::resultToArray(Database::getDb('lua_instance')[$self['id']]);
		if (!empty($objects)) {
			$return = $objects['creator'];
		}
		return $return;
	}

    /* receive sent aguments */
	public static function receivearguments($self){
		$table = Database::getDb('argument');
		$result = $table->where('receiver', $self['id']);
		$objects = Database::resultToArray($result);
		$args = array();
		foreach($objects as $val){
			if (array_key_exists($val["transmitter"], $args)){
				$args[$val["transmitter"]] = self::mergearrays($args[$val["transmitter"]], (array)json_decode($val["json"]));
			} else {
				$args[$val["transmitter"]] = (array)json_decode($val["json"]);
			}
		}

		$result->delete();
		return $args;
	}

    /* pass an argument to a object */
	public static function passArguments($self, $id, $arguments){
		return Database::getDb('argument')->insert(array(
			'transmitter' => $self['id'],
			'receiver' => $id,
			'json' => json_decode($arguments)
		));
	}

    /* print debug message */
    public static function writeoutdebugmessage($self, $message){
        $table = Database::getDb('lua_debug');
        $table->insert(array(
            'instance' => $self['id'],
            'description' => $message,
            'type' => 1));
    }

    /* pet the filepath where all lua-scripts are stored */
    public static function getLuaFilePath($filename = '') {
        $parts = array(
            APPLICATION_PATH,
            Config::get('app.lua.filepath'),
            $filename
        );
        return Helper::makePathFromParts($parts);
    }

    /* replace filepath in console output with passed text */
    public static function modifyErrors($output) {
        return str_replace(self::getLuaFilePath(), '', $output);
    }

}
