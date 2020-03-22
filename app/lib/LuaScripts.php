<?php

use model\Luainstance;

class LuaScripts extends AbstractStaticList {
        public static function getLuainstance($type, $id, $source, $json) {
               if (empty(self::$luascripts)) {
                        self::reset();
                }
                if(array_key_exists($type, self::$luascripts)){
                        $lua = self::$luascripts[$type];
                        $json = trim($json);
                        $lua->import($json);
                        if (empty($json) || $json === "[]" || $json === "{}") {
                                $lua->create();
                        }
                } else {
                        $lua = new Luainstance(array('id' => $id, 'source' => $source, 'json' => $json));
                        $lua->initLua();
                        self::$luascripts[$type] = $lua;
                }
                return $lua;
        }
}
