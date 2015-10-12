<h2>welcome!</h2>
<button onclick="getName()" style="width: 100px">get name</button>
<span id="get">get</span><br />
<button onclick="postName()" style="width: 100px">post name</button>
<span id="post">post</span><br />
<button onclick="putName()" style="width: 100px">put name</button>
<span id="put">put</span><br />
<button onclick="patchName()" style="width: 100px">patch name</button>
<span id="patch">patch</span><br />
<button onclick="deleteName()" style="width: 100px">delete name</button>
<span id="delete">delete</span><br />
<script type="text/javascript">

	function getName() {
		ajax({
			method: 'get',
			url: 'user',
			data: {
				id: 42
			},
			success: function(request, parameters) {
				document.getElementById('get').innerHTML = parameters.data.id + ' => ' + request.responseText;
			},
			failure: function() {
				document.getElementById('get').innerHTML = '<i>ERROR!</i>';
			}
		});
	}

	function postName() {
		ajax({
			method: 'post',
			url: 'user',
			data: {
				id: 42
			},
			success: function(request, parameters) {
				document.getElementById('post').innerHTML = parameters.data.id + ' => ' + request.responseText;
			},
			failure: function() {
				document.getElementById('post').innerHTML = '<i>ERROR!</i>';
			}
		});
	}

	function putName() {
		ajax({
			method: 'put',
			url: 'user',
			data: {
				id: 42
			},
			success: function(request, parameters) {
				document.getElementById('put').innerHTML = parameters.data.id + ' => ' + request.responseText;
			},
			failure: function() {
				document.getElementById('put').innerHTML = '<i>ERROR!</i>';
			}
		});
	}

	function patchName() {
		ajax({
			method: 'patch',
			url: 'user',
			data: {
				id: 42
			},
			success: function(request, parameters) {
				document.getElementById('patch').innerHTML = parameters.data.id + ' => ' + request.responseText;
			},
			failure: function() {
				document.getElementById('patch').innerHTML = '<i>ERROR!</i>';
			}
		});
	}

	function deleteName() {
		ajax({
			method: 'delete',
			url: 'user',
			data: {
				id: 42
			},
			success: function(request, parameters) {
				document.getElementById('delete').innerHTML = parameters.data.id + ' => ' + request.responseText;
			},
			failure: function() {
				document.getElementById('delete').innerHTML = '<i>ERROR!</i>';
			}
		});
	}
</script>