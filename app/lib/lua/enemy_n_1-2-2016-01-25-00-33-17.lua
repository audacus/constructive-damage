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
    -- me:destroyme()
    -- me:createobjectbyname("enemy_n_1")

    self.x = math.random(-100, 100)
    self.y = math.random(-100, 100)
    self.z = -5
end

function me:near(args)
    if self.counter == nil then
        self.counter = 0
    end
    self.counter = self.counter + 1
    if self.counter > 1 then
        self.counter = 0
    else
        nearer = self:getobjectargs(args['id'])
        local diffx = self.x - nearer.x
        local diffy = self.y - nearer.y
        print('diffx: '..diffx)
        print('diffy: '..diffy)
        if diffx > 0 then
            self.x = self.x + 1
        else
            self.x = self.x - 1
        end
        if diffy > 0 then
            self.y = self.y + 1
        else
            self.y = self.y - 1
        end
    end
end

god.object = me
god.create = me.create -- called the very first time once, and then never again

-- events --
god.intersect = me.intersect
god.collidable = true
god.near = me.near
