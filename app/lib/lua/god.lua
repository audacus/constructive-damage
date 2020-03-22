require("dump_me")
require("json")

god = {}

function god:_import(str)
    if god.object == nil then
        print("god.object is nil")
        return {}
    end
    if str == nil then
        print("str is nil")
        return {}
    end
    local instance = json.decode(str)
    for k,v in pairs(instance) do
        god.object[k] = v
    end

    _init()
end

function _cleanobject(obj)
    o = {}
    for k,v in pairs(obj) do
        if type(v) ~= "function" then
            o[k] = v
        end
    end
    return o
end


function _init()
    function god.object:getparent() return getparent(_cleanobject(self)) end
    function god.object:getchildren() return getchildren(_cleanobject(self)) end
    function god.object:setaschildof(parent) return setaschildof(_cleanobject(self), parent) end
    function god.object:createobjectbyname(objectname) return createobjectbyname(_cleanobject(self), objectname) end
    function god.object:triggerevent(eventname, distanceofimpact, eventargs) return triggerevent(_cleanobject(self), eventname, distanceofimpact, eventargs) end
    function god.object:destroyme() return destroyme(_cleanobject(self)) end
    function god.object:getobjectargs(id) return json.decode(getobjectargs(_cleanobject(self), id)) end
    function god.object:getcreatorobjectid() return getcreatorobjectid(_cleanobject(self)) end
    function god.object:receivearguments() return receivearguments(_cleanobject(self)) end
    function god.object:passarguments(id, arguments) return passarguments(_cleanobject(self), id, arguments) end
    function print(o) writeoutdebugmessage(_cleanobject(god.object), o) end

    math.randomseed(os.time())
end

-- god setup function {{{
function god:_setup()
    if god.setup ~= nil then
        god.setup(god.object)
    end
end
-- }}}

function god:_export()
    return json.encode(god.object)
end

function _startsWith(String,Start)
    return string.sub(String,1,string.len(Start))==Start
end

function _endsWith(String,End)
    return End=='' or string.sub(String,-string.len(End))==End
end

function god:_handlers()
    local g = {}
    local i = 0
    for k,v in pairs(self) do
        if type(v) == "boolean" or
        (type(v) == "function" 
            and not _startsWith(k, "_") 
            and k ~= "render"
            and k ~= "create"
            and k ~= "destroy"
            and k ~= "setup"
            and k ~= "teardown"
            and k ~= "tostring") then
            g[i] = tostring(k)
            i = i + 1
        end
    end
    if i == 0 then
        return "{}"
    end
    return json.encode(g)
end

function god:_tostring()
    return god.tostring(god.object)
end

function god:_update()
    if god.update ~= nil then
        god.update(god.object)
    end
end

function god:_create()
    god.object.x = 0
    god.object.y = 0
    god.object.z = 0
    god.object.width = 10
    god.object.length = 10
    god.object.depth = 10
    god.object.color = bit32.lshift(math.random(100, 250), 16) + bit32.lshift(math.random(100, 250), 8) + math.random(100, 250)

    if god.create ~= nil then
        god.create(god.object)
    end
end

function god:_keypress(keycodes)
    local codes = json.decode(keycodes)
    local right = 0;
    local left = 0;
    local up = 0;
    local down = 0;
    local count = 0;
    for k,keycode in pairs(codes) do
        keycode = tonumber(keycode)
        if keycode == 38 or keycode == '38' then
            up = up + 1;
            count = count + 1;
        elseif keycode == 39 or keycode == '39' then
            right = right + 1;
            count = count + 1;
        elseif keycode == 40 or keycode == '40' then
            down = down + 1;
            count = count + 1;
        elseif keycode == 37 or keycode == '37' then
            left = left + 1;
            count = count + 1;
        end

        if god.keypress ~= nil then
            god.keypress(god.object, keycode)
        end
    end
    if count > 0 then
        self:_move(left, down, up, right)
    end
end

function god:_move(left, down, up, right)
    if god.move ~= nil then
        god.move(god.object, left, down, up, right)
    end
end

function god:_triggerevent(name, args)
    local arguments = json.decode(args)
    if god[name] ~= nil then
        god[name](god.object, arguments)
    end
end

return god


