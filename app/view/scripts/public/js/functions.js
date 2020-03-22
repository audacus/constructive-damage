// checks if the given json string can be parsed to an object
function isValidJsonString(jsonString) {
	var valid = false;
	try {
		JSON.parse(jsonString);
		valid = true;
	} catch (e) {
		valid = false;
	}
	return valid;
}

// ajax helper
var Ajax = {
	// request
	request: function() {
		// ajax Methods
		var Methods = Object.freeze({
			GET: 'get',
			DELETE: 'delete',
			POST: 'post',
			PUT: 'put',
			PATCH: 'patch'
		});

		console.log(arguments);

		var args = (arguments[0] ? arguments[0] : {});
		// determine given parameters for the request or default parameters
		var parameters = {
			method: (args.method && Methods.hasOwnProperty(args.method.toUpperCase()) ? Methods[args.method.toUpperCase()] : Methods.GET),
			url: (args.url ? args.url : 'index'),
			async: (args.async ? args.async : true),
			beforeSend: (args.beforeSend ? args.beforeSend : function() {}),
			success: (args.success ? args.success : function() { console.log('success');console.log(arguments); }),
			failure: (args.failure ? args.failure : function() { console.log('failure');console.log(arguments); }),
			data: (args.data ? args.data : null)
		};

		// open request
		var request = new XMLHttpRequest();
		request.open(parameters.method, parameters.url , parameters.async);
		parameters.beforeSend();
		request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		request.onreadystatechange = function() {
			if (request.readyState === 4) {
				if (request.status === 200) {
					parameters.success(request, parameters);
				} else {
					parameters.failure(request, parameters);
				}
			}
		};

		// request send function
		this.send = function() {
			switch (parameters.method.toLowerCase()) {
				case Methods.POST:
				case Methods.PATCH:
				case Methods.PUT:
					request.setRequestHeader('Content-Type', 'application/json');
					request.send(JSON.stringify(parameters.data));
					break;
				case Methods.GET:
				case Methods.DELETE:
				default:
					request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					request.send();
					break;
			}
		};
	}
};
