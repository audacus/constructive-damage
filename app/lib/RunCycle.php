<?php

/* Core-class of the game-engine, handles one full update cycle.
 * The class contains all important functions of a game-engine, 
 * includin input-handling, update all objects, event handling and collision detection.
 */
class RunCycle {

    /* merge values of two arrays into one, also works with php-objects and nested arrays
    /* returns an array that contains content of both arrays */
	public static function mergearrays($former, $latter){
		$one = (array)$former;
		$two = (array)$latter;
		$diff = array();
		foreach($one as $key => $val1){
			if (array_key_exists($key, $two)){
				$val2 = $two[$key];
				if ($val1 === null && $val2 === null){
					// discard if both are null
					continue;
				}
				startspecialcase:
					$type1 = gettype($val1);
				$type2 = gettype($val2);
				if ($val2 === null){
					$diff[$key] = $val1;
				} else if ($val1 === null){
					$diff[$key] = $val2;
				} else if (is_numeric($val1) && is_numeric($val2)){
					$diff[$key] = $val1 + $val2;
				} else if ($type1 == "boolean" && $type2 == "boolean"){
					$diff[$key] = $val1 && $val2;
				} else if ($type1 == "string"){
					$diff[$key] = $val1." | ".(string)$val2;
				} else if ($type2 == "string"){
					$diff[$key] = (string)$val1." | ".$val2;
				} else if (($type1 == "array" || $type1 == "object") && ($type2 == "array" || $type2 == "object")){
					$diff[$key] = self::mergearrays($val1, $val2);
				} else {
					$diff[$key] = $val2;
				}
			} else {
				// only in first
				$diff[$key] = $val1;
				if (gettype($key) == "integer"){
					$ob = (object)$two;
					if(isset($ob->{$key})){
						$val2 = $ob->{$key};
						goto startspecialcase;
					}
				}
			}
		}
		foreach($two as $key => $val2){
			if (!array_key_exists($key, $one)) {
				$diff[$key] = $val2;
			}
		}
		return $diff;
	}

    /* writes out everything that has changed from the previous state to the later one.
     * returns an array that contains only the differences between the passed arrays. */
	public static function diffarrays($former, $latter){
		$one = (array)$former;
		$two = (array)$latter;
		$diff = array();

		foreach($one as $key => $val1){
			if (array_key_exists($key, $two)){
				$val2 = $two[$key];
				if ($val1 === $val2){
					// same value, no diff
					continue;
				}
				if ($val1 === null && $val2 === null){
					// discard if both are null
					continue;
				}
				$type1 = gettype($val1);
				$type2 = gettype($val2);
				if ($val2 === null){
					$diff[$key] = $val1;
				} else if ($val1 === null){
					$diff[$key] = $val2;
				} else if (is_numeric($val1) && is_numeric($val2)){
					$diff[$key] = $val2 - $val1;
				} else if ($type1 == "boolean" && $type2 == "boolean"){
					$diff[$key] = $val2;
				} else if ($type1 == "string"){
					$diff[$key] = (string)$val2;
				} else if ($type2 == "string"){
					$diff[$key] = (string)$val2;
				} else if (($type1 == "array" || $type1 == "object") && ($type2 == "array" || $type2 == "object")){
					$diff[$key] = self::diffarrays($val1, $val2);
				} else {
					$diff[$key] = $val2;
				}
			} else {
				// only in first, ignore since diff always only against old state
			}
		}
		foreach($two as $key => $val2){
			if (!array_key_exists($key, $one)) {
				$diff[$key] = $val2;
			}
		}
		return $diff;
	}

    /* updates an existing difference with a new one (does not overwrite!) */
	private static function updatejson($json, $id){
		Diffs::set($id, self::mergearrays(Diffs::get($id), is_string($json) ? json_decode($json, true) : $json));
	}

    /* saves final differences back into the database */
	private static function writejson($id){
		$row = Database::getDb('lua_instance')[$id];
		$instance = Database::resultToArray($row);
		$row->update(array('json' => json_encode(self::mergearrays(json_decode($instance['json'], true), Diffs::remove($id)))));
	}

    /* destroy each object that is mentioned in the Destroyeds-List */
	private static function destroyobjects(){
		// TODO give children of deleted instance to parnet of the instance
		$tableArgument = Database::getDb('argument');
		$tableHandler = Database::getDb('handler');
		$tableLuaInstance = Database::getDb('lua_instance');
		foreach (Destroyeds::get() as $id) {
			$tableArgument->where('id', $id)->or('transmitter', $id)->delete();
			$tableHandler->where('instance', $id)->delete();
			// TODO: what if object is avatar, what if object has children?
            if(!empty($tableLuaInstance[$id]))
                $tableLuaInstance[$id]->delete();
		}
	}

    /* create every object that is mentined in the Createds-List */
	private static function createobjects(){
        foreach(Createds::get() as $creation){
            Database::getDb('lua_instance')->insert($creation);
            // creation: keys type and creator
        }
        Createds::reset();
        self::updatehandlers();
	}

    /* calculates the Axis-Aligned Bounding Box for the passed object, used for collision detection */
	private static function calculateAABB($self){
		$self = (array)$self;
		$x = $self["x"];
		$xi = $x + $self["width"];
		$y = $self["y"];
		$yi = $y + $self["length"];
		$z = $self["z"];
		$zi = $z + $self["depth"];

		if ($x > $xi){
			$tmp = $x;
			$xi = $x;
			$x = $tmp;
		}
		if ($y > $yi){
			$tmp = $y;
			$yi = $y;
			$y = $tmp;
		}
		if ($z > $zi){
			$tmp = $z;
			$zi = $z;
			$z = $tmp;
		}
		return array("minx" => $x, "miny" => $y, "minz" => $z, "maxx" => $xi, "maxy" => $yi, "maxz" => $zi);
	}

    /* detects collisions among collidable objects
    * if a collision is detection, an event is fired and the two objects get notified */
	private static function collisiondetection(){
		$vals = array();
		$vals = Database::innerJoin3(
			'lua_instance', array(),
			'id', 'handler', array('name' => 'collidable'), 'instance',
			'type', 'lua_script', array(), 'id');
		$collisions = array();
		$aabbs = array();
		$objects = array();
		foreach($vals as $val) {
			$objects[$val["lua_instance.id"]] = json_decode($val["lua_instance.json"]);
		}
		foreach($objects as $outerkey => $outerval){
			if (!array_key_exists($outerkey, $aabbs))
				$aabbs[$outerkey] = self::calculateAABB($outerval);
			foreach($objects as $innerkey => $innerval){
				if ($outerkey === $innerkey)
					continue;
				if (!array_key_exists($innerkey, $aabbs))
					$aabbs[$innerkey] = self::calculateAABB($innerval);
				$outer = $aabbs[$outerkey];
				$inner = $aabbs[$innerkey];
				if ((($outer["minx"] >= $inner["minx"] && $outer["minx"] <= $inner["maxx"])
					|| ($outer["maxx"] >= $inner["minx"] && $outer["maxx"] <= $inner["maxx"]))
					&& (($outer["miny"] >= $inner["miny"] && $outer["miny"] <= $inner["maxy"])
					|| ($outer["maxy"] >= $inner["miny"] && $outer["maxy"] <= $inner["maxy"]))
        // disable z axes, not worth, just confusing
		//			&& (($outer["minz"] >= $inner["minz"] && $outer["minz"] <= $inner["maxz"])
        //  		|| ($outer["maxz"] >= $inner["minz"] && $outer["maxz"] <= $inner["maxz"]))
                    ){
					// intersects
					if (!array_key_exists($outerkey, $collisions))
						$collisions[$outerkey] = array();
					if (!array_key_exists($innerkey, $collisions))
						$collisions[$innerkey] = array();

					$collisions[$outerkey][] = $innerkey;
					$collisions[$innerkey][] = $outerkey;
				}
			}

			// remove itself in order to avoid duplicated tests
			unset($objects[$outerkey]);
		}
		// TODO 2016-01-09 me: intersect method mustn't trigger event
		foreach($collisions as $key => $val){
			foreach($vals as $o) {
				if ($o["lua_instance.id"] === strval($key)){
					$record = $o;
					break;
				}
			}
			$json = json_decode($record['lua_instance.json'], true);
			$lua = LuaScripts::getLuainstance($record['lua_instance.type'], $record['lua_instance.id'], $record['lua_script.source'], $record['lua_instance.json']);
			$lua->setup($record["lua_instance.json"]);
			$lua->triggerevent("intersect", $val);
            $diff = self::diffarrays(json_decode($record["lua_instance.json"], true), json_decode($lua->teardown(), true));
            self::updatejson($diff, $record["lua_instance.id"]);
		}
	}

    /* goes through the Events-List and notifies every object that was near a registered event */
	private static function eventhandling(){
		foreach (Events::get() as $event) {
			$objects = array();
			$objects = Database::innerJoin3(
				'lua_instance', array(),
				'id', 'handler', array('name' => $event->getName()), 'instance',
				'type', 'lua_script', array(), 'id');
			// get every object within distanceofimpact
			$eventx = $event->getX() + $event->getW()/2.0;
			$eventy = $event->getY() + $event->getL()/2.0;
			foreach ($objects as $record){
				if (empty($record["lua_instance.json"]))
					continue;
				$victim = (array)json_decode($record["lua_instance.json"]);

				$victimx = $victim["x"] + $victim["width"]/2.0;
				$victimy = $victim["y"] + $victim["length"]/2.0;

				$diffx = abs($eventx - $victimx);
				$diffy = abs($eventy - $victimy);

				$distance = sqrt($diffx * $diffx + $diffy * $diffy);

				if ($distance >= $event->getReach())
					// out of reach
					continue;

				$lua = LuaScripts::getLuainstance($record['lua_instance.type'], $record['lua_instance.id'], $record['lua_script.source'], $record['lua_instance.json']);
				$lua->triggerevent($event->getName(), $event->getArgs());
				$diff = self::diffarrays(json_decode($record["lua_instance.json"], true), json_decode($lua->teardown(), true));
				self::updatejson($diff, $record["lua_instance.id"]);
			}
		}
        Events::reset();
	}

    /* get registered events from each object and write them into the database */
	public static function updatehandlers(){
		$objects = array();
		$objects = Database::innerJoin(
			'lua_instance', array(),
			'type', 'lua_script', array(), 'id');
		$table = Database::getDb('handler');
		$table->delete();
		// get every object within distanceofimpact
		foreach ($objects as $record){
			$lua = LuaScripts::getLuainstance($record['lua_instance.type'], $record['lua_instance.id'], $record['lua_script.source'], $record['lua_instance.json']);
			$handlers = json_decode($lua->handlers(), true);

			foreach ($handlers as $handler) {
				$table->insert(array(
					'name' => $handler,
					'instance' => $record['lua_instance.id']
				));
			}
		}
	}

    /* run for each object its update function */
	private static function refresh($debug){
		$objects = Database::innerJoin(
			'lua_instance', array(),
			'type', 'lua_script', array(), 'id');
		// get every object within distanceofimpact
		foreach ($objects as $record){
            self::isarray();
			$lua = LuaScripts::getLuainstance($record['lua_instance.type'], $record['lua_instance.id'], $record['lua_script.source'], $record['lua_instance.json']);
			$lua->setup();
			$lua->update();
            $diff = self::diffarrays(json_decode($record["lua_instance.json"], true), json_decode($lua->teardown(), true));
            self::updatejson($diff, $record["lua_instance.id"]);
		}
	}

    /* prints every object as valid jsons
     * this method is called from the view */
    public static function printobjects(){
        $jsons = array();
        $popups = array();
        $avatarid = 1;
        if (!empty(Security::getLoggedInUser()))
            if (!($avatarid = Security::getLoggedInUser()->getAvatar()))
                $avatarid = 1;
        $avatar = Database::resultToArray(Database::getDb('lua_instance')[$avatarid]);
        $avataro = json_decode($avatar["json"], true);

        $avatarx = $avataro["x"] + $avataro["width"]/2.0;
        $avatary = $avataro["y"] + $avataro["length"]/2.0;

        $jsons[] = $avatar["json"];

        $avatarparent = Database::resultToArray(Database::getDb('children')->where('child', $avatarid));
        $objects = array();
        if (empty($avatarparent)){
            $instances = Database::resultToArray(Database::getDb('lua_instance'));
            foreach($instances as $instance){
                if (empty(Database::resultToArray(Database::getDb('children')->where('child', $instance['id']))))
                    $objects[] = $instance;
            }
        } else {
            $avatarparent = reset($avatarparent)["parent"];
            $objects = Database::innerJoin(
            'lua_instance', array(),
            'id', 'children', array('parent' => $avatarparent), 'child');
        }

        // print only these objects that can even be visible for the user; the user doesn't have an infinite screen size
        foreach ($objects as $record) {
            if (!empty($record['json']) && $record["id"] != $avatarid) {
                $object = json_decode($record["json"], true); 
                if (!isset($object["x"]))
                    continue; // FIXME x, y must always be set, error state which should never ever happen
                $objectx = $object["x"] + $object["width"]/2.0;
                $objecty = $object["y"] + $object["length"]/2.0;

                $diffx = abs($avatarx - $objectx);
                $diffy = abs($avatary - $objecty);

                $distance = sqrt($diffx * $diffx + $diffy * $diffy);

                if ($distance <= 300){
                    $jsons[] = $record['json'];

                    if (isset($object['popup']))
                        $popups[] = "object ".$record['id']." says: ".$object['popup'];
                }
            }
        }
        $jsons = array_filter($jsons);
        $result = "{";
        $result = $result.'"avatarid": '.$avatarid.',';
        $result = $result.'"popup": '.'"'.implode("\\n", $popups).'",';
        $result = $result.'"objects": '.'['.implode(',', $jsons).']';
        $result = $result."}";
        return $result;
    }

    /* print every debug message that lua-objcts could have printed */
    public static function printdebugs(){
        $selection = Database::getDb('lua_debug')->where('type', 1);
		$objects = Database::resultToArray($selection);
        if (empty($objects)){
            return "<h1>No debug messages available</h1>";
        }
		$result = array();
        $result[] = "<h3>Debug messages printed by Lua objects</3>";
		foreach ($objects as $record) {
				$result[] = $record['instance'].": ".$record['description']."<br />";
		}
        $selection->delete();
        return implode('<br />', $result);
    }

    /* print every error message that occured while updating lua-objects */
    public static function printerrors(){
        $selection = Database::getDb('lua_errors');
		$objects = Database::resultToArray($selection);
        if (empty($objects)){
            return "<h1>No warnings or errors available</h1>";
        }
		$result = array();
        $result[] = "<h3>Warnings and Errors from Lua objects</h3>";
		foreach ($objects as $record) {
				$result[] = $record['description']."<br />";
		}
        $selection->delete();
        return implode('<br />', $result);
    }

    /* process each keyinput */
	private static function updatekeypresses(){
		$vals = Database::resultToArray(Database::getDb('keypress'));
		$ids = array();
		foreach($vals as $val){
			if (array_key_exists($val["instance"], $ids)){
				$ids[$val["instance"]][] = $val["keycode"];
			} else {
				$ids[$val["instance"]] = array($val["keycode"]);
			}
		}
		foreach($ids as $id => $keycodes){
			$record = current(Database::innerJoin(
				'lua_instance', array('id' => $id),
				'type', 'lua_script', array(), 'id'));

			$lua = LuaScripts::getLuainstance($record['lua_instance.type'], $record['lua_instance.id'], $record['lua_script.source'], $record['lua_instance.json']);
			$lua->keypress($keycodes);
			self::updatejson(self::diffarrays(json_decode($record["lua_instance.json"], true), json_decode($lua->teardown(), true)), $record["lua_instance.id"]);
		}
		Database::getDb('keypress')->delete();
	}

    /* add new keypress to database to process it later */
	public static function keypress($keycode){
        $user = Security::getLoggedInUser();
        if (empty($user))
            $avatarid = 1;
        else
            $avatarid = $user->getAvatar();
		if (!$result = Database::resultToArray(Database::getDb('keypress')->insert(array(
			'instance' => $avatarid,
			'keycode' => $keycode)))) {
			die(base64_decode('ZmluZCBtZSEgOkQ='));
		}
	}

    /* save changes to database */
	private static function writechanges(){
		foreach (Diffs::get() as $id => $diff) {
			if (!empty($diff)) {
				self::writejson($id);
			}
		}
	}

    /* checks if the LuaInstance-List is still valid */
    public static function isarray(){
		$objects = Database::innerJoin(
			'lua_instance', array(),
			'type', 'lua_script', array(), 'id');
        foreach($objects as $record)
			$lua = LuaScripts::getLuainstance($record['lua_instance.type'], $record['lua_instance.id'], $record['lua_script.source'], $record['lua_instance.json']);
            if (is_array($lua)){
				throw new \Exception("LuaInstance is Array instead of model\Luainstance!\n".__FILE__." at ".__LINE__."!");
                die("array");
            }
    }

    /* run one update cycle, update everything */
	public static function run(){
        self::resetErrors();
		self::updatekeypresses();
		self::refresh(false);
		self::eventhandling();
		self::writechanges();
		self::collisiondetection();
		self::writechanges();
		self::destroyobjects();
        self::createobjects();
	}

    /* reest every previously occured error */
    public static function resetErrors(){
        Database::getDb('lua_errors')->delete();
    }

    /* add error to database if one occured */
    public static function printError(\Exception $e)
    {
        $table = Database::getDb('lua_errors');
        $table->insert(array(
            'description' => 
            "<b>".get_class($e)."</b> ".$e->getMessage()." on line ".$e->getLine()." in file ".$e->getFile()."<pre>".$e->getTraceAsString()."</pre>"));
    }
}
