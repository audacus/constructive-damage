// array for each
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

// array include
if (!Array.prototype.includes) {
	Array.prototype.includes = function(searchElement /*, fromIndex*/ ) {
		'use strict';
		var O = Object(this);
		var len = parseInt(O.length) || 0;
		if (len === 0) {
			return false;
		}
		var n = parseInt(arguments[1]) || 0;
		var k;
		if (n >= 0) {
			k = n;
		} else {
			k = len + n;
			if (k < 0) {k = 0;}
		}
		var currentElement;
		while (k < len) {
			currentElement = O[k];
			if (searchElement === currentElement ||
				 (searchElement !== searchElement && currentElement !== currentElement)) {
				return true;
			}
			k++;
		}
		return false;
	};
}

// object urlEncode
if (!Object.prototype.urlEncode) {
	Object.prototype.urlEncode = function (prefix) {
		'use strict';
		var str = [];
		for (var p in this) {
			if (this.hasOwnProperty(p)) {
				var k = prefix ? prefix + '[' + p + ']' : p,
						v = this[p];
				str.push(typeof v == 'object' ? urlEncode(v, k) : encodeURIComponent(k) + '=' + encodeURIComponent(v));
			}
		}
		return str.join('&');
	};
}