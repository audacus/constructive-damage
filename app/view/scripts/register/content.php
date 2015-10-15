<h3>register</h3>

<form class="form-register" action="register" method="post" accept-charset="utf-8" autocomplete="off">
	<input name="username" placeholder="username" required="required" /><br />
	<input name="email" type="email" placeholder="email" required="required" /><br />
	<input name="password" type="password" placeholder="password" required="required" /><br />
	<input name="register" type="submit" value="register" />
</form>
<div id="errors"></div>
<script type="text/javascript">
	if (window.data.errors) {
		document.getElementById('errors').innerHTML = '';
		window.data.errors.forEach(function(error) {
			document.getElementById('errors').innerHTML += error+'<br />';
		});
	}
</script>
