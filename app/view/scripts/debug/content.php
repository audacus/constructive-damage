<script type="text/javascript">
			function IsJsonString(str) {
				try {
					JSON.parse(str);
				} catch (e) {
					return false;
				}
				return true;
			}

			function displayRespond(r) {
				var responseText = r.responseText;
				if (IsJsonString(responseText)){
					//responseText = JSON.stringify(JSON.parse(responseText), null, 4);
				}
				var divi = document.getElementById('content');
				divi.innerHTML = divi.innerHTML + "<br /><pre>" + responseText + "</pre><br />";
			}

			function refresh(){
				new Ajax.request({url: 'game?a=refresh', success: displayRespond}).send();
			}

			function luadebug(){
				new Ajax.request({url: 'game?a=luadebug', success: displayRespond}).send();
			}

			function luaerrors(){
				new Ajax.request({url: 'game?a=luaerrors', success: displayRespond}).send();
			}

			function runandprint(){
				new Ajax.request({url: 'game?a=runandprint', success: displayRespond}).send();
			}

			function rundotlog(){
				new Ajax.request({url: 'game?a=rundotlog', success: displayRespond}).send();
			}

			function start(){
				new Ajax.request({url: 'game?a=start', success: displayRespond}).send();
			}

			function stop(){
				new Ajax.request({url: 'game?a=stop', success: displayRespond}).send();
			}

			function keypress(keycode){
                var u = 'game?a=keypress&key='+keycode;
				new Ajax.request({url: u}).send();
			}
		</script>
	<button onclick="luadebug()">debug</button>
	<button onclick="luaerrors()">errors</button>
	<button onclick="refresh()">update</button>
	<button onclick="start()">start</button>
	<button onclick="stop()">stop</button>
	<button onclick="runandprint()">Run once and print</button>
	<button onclick="rundotlog()">Statistics</button>
	<p />
	<div id="content" tabindex="0" style="overflow-y: scroll; height:800px;">
	</div>
	<script type="text/javascript">
			var height = window.innerHeight
			|| document.documentElement.clientHeight
			|| document.body.clientHeight;
			var divii = document.getElementById('content');
			divii.style.height = (height - 100) + "px";
            divii.addEventListener('keydown', function(e){
                keypress(e.keyCode);
            });
	</script>
