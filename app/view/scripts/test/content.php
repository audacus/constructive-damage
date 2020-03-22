<?php if (Security::isLoggedIn()) : ?>
<h2><?php Helper::printLink('logout'); ?></h2>
<?php else : ?>
<h2><?php Helper::printLink('login'); ?></h2>
<h2><?php Helper::printLink('signup'); ?></h2>
<?php endif; ?>
<h2><?php Helper::printLink(Config::get('app.game.controller')); ?></h2>
<h2><?php Helper::printLink('editor'); ?></h2>
<h2><?php Helper::printLink('debug'); ?></h2>
<input id="method" placeholder="method" /><br />
<input id="url" placeholder="url" /><br />
<input id="data" placeholder="data" /><br />
<button onclick="sendAjax()" style="width: 100px">send ajax</button><br />
<pre><span id="result">result</span></pre>
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
