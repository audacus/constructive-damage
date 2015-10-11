<h2>welcome!</h2>
<span id="name">name</span>
<button onclick="getName()">get name</button>
<script type="text/javascript">
	function getName() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (xhttp.readyState == 4 && xhttp.status == 200) {
				document.getElementById('name').innerHTML = xhttp.responseText;
			}
		}
		xhttp.open('get', 'user/name', false);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}
</script>