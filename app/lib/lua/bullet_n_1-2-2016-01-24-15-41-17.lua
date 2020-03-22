-- ----- automated added, do not remove ----- --
package.path = package.path..";/var/www/workspace/website/constructive-damage/app/lib/lua/?.lua"
god = require("god")
-- ----- end -------------------------------- --

me = {}

function me:render()
end

function me:create()
    print('bullet create()')
    local creatorid = self:getcreatorobjectid()
    local creatorargs = self:getobjectargs(creatorid)
    self.x = creatorargs.x
    self.y = creatorargs.y
    self.z = creatorargs.z
    self.counter = 0
    self.distancex = 0
    self.distancey = 0
    self.startx = self.x
    self.starty = self.y

    self.width = 4
    self.length = 4
    self.depth = 4

    -- local args = self:receivearguments()
    -- var_dump(args, "received arguments")
    -- args = args[creatorid]
    -- self.counterx = args.movedx
    -- self.countery = args.movedy
    self.counterx = 1;
    self.countery = 0;
end

function me:update()
    self.counter = self.counter + 1;
    self.distancex = self.distancex + self.counterx * 5
    self.distancey = self.distancey + self.countery * 5
    self.x = self.startx + self.distancex
    self.y = self.starty + self.distancey
    if self.counter > 30 then
        print('destroy me, id: '..self.id)
        me:destroyme()
    end
end

function me:tostring()
   return "x: "..self.x.." y: "..self.y
end

function me:intersect(args)
    for k,v in pairs(args) do
        if v ~= 1 then
            me:destroyme()
        end
    end
end

function me:near(args)
    god.object.color = bit32.lshift(math.random(100, 200), 16) + bit32.lshift(math.random(100, 250), 8) + math.random(100, 250)
end

god.object = me
god.render = me.render
god.create = me.create -- called the very first time once, and then never again
god.destroy = nil -- similar to god.create
god.setup = nil -- called every time the object gets created
god.teardown = nil -- similar to god.setup
god.tostring = me.tostring
god.update = me.update

-- events --
god.intersect = me.intersect
god.near = me.near
god.collidable = true
