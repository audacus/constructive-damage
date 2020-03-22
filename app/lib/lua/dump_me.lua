
local _nativeprint = print
print = function (o)
    _nativeprint(tostring(o))
end

function dump_type(o)
	print(type(o))
end

function dump_html(o)
    print("<br />")
    print(o)
    print("<br />")
end

function var_dump(o, title)
	if title ~= nil then
		print("---", title, "---")
	end
	if type(o) == "table" then
		for k, v in pairs(o) do
			print(k, v)
		end
	elseif type(o) == "string" then
		print(o)
	else
		print(type(o))
	end
	if title ~= nil then
		print("---\tend\t---")
	end
end

function var_dump_html(o, title)
	if title ~= nil then
        print("<br />---")
        print(title)
        print("---")
	end
	if type(o) == "table" then
		for k, v in pairs(o) do
            print("<br />")
            print(k)
            print(" => ")
            print(v)
		end
	else
        print("<br />")
		print(type(o))
        print(": ")
        print(o)
	end
	if title ~= nil then
		print("<br />---end---")
	end
end


function var_dump_r(o, title)
	if title ~= nil then
		print("---", title, "---")
	end
	if type(o) == "table" then
		var_dump(o)
		local mt = getmetatable(o)
		if type(mt) ~= "table" then
			return
		end
		if type(mt.__index) ~= "table" then
			return
		end
		var_dump_r(mt.__index)
	else
		var_dump(o)
	end
	if title ~= nil then
		print("---\tend\t---")
	end
end

function var_dump_tree(o, i)
	if i == nil then
		i = 0
	end
	if type(o) == "table" then
		local mt = getmetatable(o)
		if type(mt) ~= "table" then
			return i
		end
		if type(mt.__index) ~= "table" then
			return i
		end
		local l = var_dump_tree(mt.__index, i + 1)
		local sb = ""
		for j = 2,l - i do
			sb = sb.."\t"
		end
		sb = sb.."classname: "..o._classname.." id: "..o._id
		print(sb)
		return l
	else
		print("not a table:", o)
	end
end

function var_dump_tree_r(o, title, i)
	if i == nil then
		i = 0
	end
	if title ~= nil then
		print("---", title, "---")
	end
	if type(o) == "table" then
		local mt = getmetatable(o)
		if type(mt) ~= "table" then
			return i
		end
		if type(mt.__index) ~= "table" then
			return i
		end
		local l = var_dump_tree_r(mt.__index, nil, i + 1)
		local tabs = ""
		for j = 2,l - i do
			tabs = tabs.."\t"
		end
		for k,v in pairs(o) do
			print(tabs..k, v)
		end
		return l
	else
		print("not a table:", o)
	end
	if title ~= nil then
		print("---\tend\t---")
	end
end

-- stolen from http://stackoverflow.com/a/29246308/2529745
function getargs(fun)
	local args = {}
	local hook = debug.gethook()

	local argHook = function( ... )
	    local info = debug.getinfo(3)
	    if 'pcall' ~= info.name then return end

	    for i = 1, math.huge do
		local name, value = debug.getlocal(2, i)
		if '(*temporary)' == name then
		    debug.sethook(hook)
		    error('')
		    return
		end
		table.insert(args,name)
	    end
	end

	debug.sethook(argHook, "c")
	pcall(fun)

	var_dump(args)
end

