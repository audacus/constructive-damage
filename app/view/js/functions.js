function serialize(obj, prefix) {
	var str = [];
	for (var p in obj) {
		if (obj.hasOwnProperty(p)) {
			var k = prefix ? prefix + '[' + p + ']' : p,
					v = obj[p];
			str.push(typeof v == 'object' ? serialize(v, k) : encodeURIComponent(k) + '=' + encodeURIComponent(v));
		}
	}
	return str.join('&');
}

function ajax() {
	var parameters = arguments[0];
	var request = new XMLHttpRequest();
	// request.open(parameters.method, (parameters.method.toLowerCase() === 'get' || parameters.method.toLowerCase() === 'delete' ? parameters.url + '&' + serialize(parameters.data) : parameters.url), parameters.async);
	request.open(parameters.method, parameters.url, parameters.async);
	if (parameters.beforeSend) {
		parameters.beforeSend();
	}
	request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	request.onreadystatechange = function() {
		if (request.readyState === 4) {
			if (request.status === 200) {
				console.log('success');
				if (parameters.success) {
					parameters.success(request, parameters);
				}
			} else {
				console.log('failure');
				if (parameters.failure) {
					parameters.failure(request, parameters);
				}
			}
		}
	};

	switch (parameters.method.toLowerCase()) {
		case 'get':
		case 'delete':
			request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			request.send();
			break;
		default:
			// post, put
			request.setRequestHeader('Content-Type', 'application/json');
			request.send(JSON.stringify(parameters.data));
			break;
	}

}