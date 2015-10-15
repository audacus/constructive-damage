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
		case 'post':
		case 'put':
			request.setRequestHeader('Content-Type', 'application/json');
			request.send(JSON.stringify(parameters.data));
			break;
		default:
			// do nothing
			break;
	}

}

// Production steps of ECMA-262, Edition 5, 15.4.4.18
// Reference: http://es5.github.io/#x15.4.4.18
if (!Array.prototype.forEach) {

	Array.prototype.forEach = function(callback, thisArg) {

	var T, k;

	if (this == null) {
		throw new TypeError(' this is null or not defined');
	}

	// 1. Let O be the result of calling ToObject passing the |this| value as the argument.
	var O = Object(this);

	// 2. Let lenValue be the result of calling the Get internal method of O with the argument "length".
	// 3. Let len be ToUint32(lenValue).
	var len = O.length >>> 0;

	// 4. If IsCallable(callback) is false, throw a TypeError exception.
	// See: http://es5.github.com/#x9.11
	if (typeof callback !== "function") {
		throw new TypeError(callback + ' is not a function');
	}

	// 5. If thisArg was supplied, let T be thisArg; else let T be undefined.
	if (arguments.length > 1) {
		T = thisArg;
	}

	// 6. Let k be 0
	k = 0;

	// 7. Repeat, while k < len
	while (k < len) {

		var kValue;

		// a. Let Pk be ToString(k).
		//  This is implicit for LHS operands of the in operator
		// b. Let kPresent be the result of calling the HasProperty internal method of O with argument Pk.
		//  This step can be combined with c
		// c. If kPresent is true, then
		if (k in O) {

		// i. Let kValue be the result of calling the Get internal method of O with argument Pk.
		kValue = O[k];

		// ii. Call the Call internal method of callback with T as the this value and
		// argument list containing kValue, k, and O.
		callback.call(T, kValue, k, O);
		}
		// d. Increase k by 1.
		k++;
	}
	// 8. return undefined
	};
}