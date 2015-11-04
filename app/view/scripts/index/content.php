<h2><a href="play">play</a></h2>
<input id="method" placeholder="method" /><br />
<input id="url" placeholder="url" /><br />
<input id="data" placeholder="data" /><br />
<button onclick="sendAjax()" style="width: 100px">send ajax</button><br />
<span id="result">result</span>
<script type="text/javascript">

	function sendAjax() {
		new Ajax.request({
			method: (document.getElementById('method').value ? document.getElementById('method').value : null),
			url: (document.getElementById('url').value ? document.getElementById('url').value : null),
			data: (isValidJsonString(document.getElementById('data').value) ? JSON.parse(document.getElementById('data').value) : null),
			success: function(request, parameters) {
				document.getElementById('result').innerHTML = request.responseText;
			},
			failure: function() {
				document.getElementById('result').innerHTML = '<i>ERROR!</i>';
			}
		}).send();
	}
</script>