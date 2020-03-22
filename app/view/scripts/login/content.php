<div class="errors"></div>
<form method="post">
	<input required="required" id="username" name="username" placeholder="email or username" /><br />
	<input required="required" id="password" name="password" type="password" placeholder="password" /><br />
	<input id="persistent" name="persistent" type="checkbox" value="true" checked="checked" /><label for="persistent">stay logged in</label><br />
	<input type="submit" value="login" />
</form>
<script type="text/javascript">
	// insert form data
	if (window.data.formdata) {
		var formData = window.data.formdata;
		document.getElementById('username').value = formData.username;
		document.getElementById('persistent').value = formData.username;
	}
	// display errors
	if (window.error) {
		var errorElements = document.getElementsByClassName('errors');
		for (var i = 0; i < errorElements.length; i++) {
			var element = errorElements[i];
			element.innerHTML = '';
			window.error.forEach(function(error) {
				element.innerHTML += error+'<br />';
			});
		}
	}
</script>
<?php Helper::printLink('signup', 'signup'); ?>
