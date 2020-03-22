-- ----- automated added, do not remove ----- --
package.path = package.path..";/var/www/workspace/website/constructive-damage/app/lib/lua/?.lua"
god = require("god")
-- ----- end -------------------------------- --

me = {}

me.right = 1
me.up = 1

function me:update()
    self.color = bit32.lshift(math.random(100, 200), 16) + bit32.lshift(math.random(100, 250), 8) + math.random(100, 250);
    me:triggerevent("near", 30, { id = self.id })
end

function me:tostring()
   return "x: "..self.x.." y: "..self.y
end

function me:move(left, down, up, right)
    self.x = self.x - left * 5 + right * 5
    self.y = self.y - down * 5 + up * 5
    me.right = right - left
    me.up = up - down
end

function me:keypress(keycode)
    print('keypress object '..self.id..' keycode: '..keycode)
    if keycode == 32 or keycode == 66 then
        print('create bullet')
        local id = me:createobjectbyname("bullet_n_1")
        local args = {}
        args.movedx = me.right
        args.movedy = me.up

        me:passarguments(id, args)
    end
    -- local args = {}
    -- args.id = self.id
    -- args.keycode = keycode
    -- me:triggerevent("keyinput", 50, args)
end

god.object = me
god.create = nil -- called the very first time once, and then never again
god.destroy = nil -- similar to god.create
god.setup = nil -- called every time the object gets created
god.teardown = nil -- similar to god.setup
god.tostring = me.tostring
god.update = me.update

-- events --
god.move = me.move
god.keypress = me.keypress
god.collidable = true
