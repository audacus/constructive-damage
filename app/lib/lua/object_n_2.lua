-- ----- automated added, do not remove ----- --
package.path = package.path..";/var/www/workspace/website/constructive-damage/app/lib/lua/?.lua"
god = require("god")
-- ----- end -------------------------------- --

me = {}

function me:move(left, down, up, right)
    print('hiii')
    self.x = self.x - left * 5 + right * 5
    self.y = self.y - down * 5 + up * 5
end

function me:keypress(keycode)
    print(keycode)
    if keycode == 80 then
        print(self.popup)
        if type(self.popup) == 'nil' then
            self.popup = 'hi'
        else
            self.popup = nil
        end
    end
end

god.object = me
god.move = me.move
god.keypress = me.keypress
