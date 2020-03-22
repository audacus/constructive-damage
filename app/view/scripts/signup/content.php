<div class="errors"></div>
<form method="post">
	<input required="required" id="username" name="username" placeholder="username" /><br />
	<input required="required" id="email" name="email" type="email" placeholder="email" /><br />
	<input required="required" id="password" name="password" type="password" placeholder="password" /><br />
	<input required="required" id="password" name="password-repeat" type="password" placeholder="repeat password" /><br />
	<input required="required" type="submit" value="signup" />
</form>
<script type="text/javascript">
	// insert form data
	if (window.data.formdata) {
		var formData = window.data.formdata;
		document.getElementById('username').value = formData.username;
		document.getElementById('email').value = formData.email;
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
<?php Helper::printLink('login', 'login'); ?>
