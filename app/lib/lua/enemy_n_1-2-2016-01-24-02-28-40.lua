-- ----- automated added, do not remove ----- --
package.path = package.path..";/var/www/workspace/website/constructive-damage/app/lib/lua/?.lua"
require("god")
-- ----- end -------------------------------- --
me = {}

function me:create()
    self.x = math.random(-200, 200)
    self.y = math.random(-200, 200)
    self.z = -5

    self.width = 10
    self.length = 10
    self.depth = 10
end

function me:intersect(args)
    me:destroyme()
    local id = me:createobjectbyname("enemy_n_1")
    var_dump(id, "id")
end

function me:keyinput(args)
    self.tostring = args.keycode
end

god.object = me
god.create = me.create -- called the very first time once, and then never again

-- events --
god.intersect = me.intersect
god.collidable = true
god.keyinput = me.keyinput
