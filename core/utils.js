//=================================================================================================
//	common javascript utilities which are widely used by kapenta modules
//=================================================================================================

/* 
 * More info at: http://kevin.vanzonneveld.net/techblog/category/php2js
 * 
 * php.js is copyright 2008 Kevin van Zonneveld.
 * 
 * Portions copyright Ates Goral (http://magnetiq.com), Legaev Andrey,
 * _argos, Jonas Raoni Soares Silva (http://www.jsfromhell.com),
 * Webtoolkit.info (http://www.webtoolkit.info/), Carlos R. L. Rodrigues, Ash
 * Searle (http://hexmen.com/blog/), Tyler Akins (http://rumkin.com), mdsjack
 * (http://www.mdsjack.bo.it), Alexander Ermolaev
 * (http://snippets.dzone.com/user/AlexanderErmolaev), Andrea Giammarchi
 * (http://webreflection.blogspot.com), Bayron Guevara, Cord, David, Karol
 * Kowalski, Leslie Hoare, Lincoln Ramsay, Mick@el, Nick Callen, Peter-Paul
 * Koch (http://www.quirksmode.org/js/beat.html), Philippe Baumann, Steve
 * Clay, booeyOH
 * 
 * Licensed under the MIT (MIT-LICENSE.txt) license.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL KEVIN VAN ZONNEVELD BE LIABLE FOR ANY CLAIM, DAMAGES 
 * OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR 
 * OTHER DEALINGS IN THE SOFTWARE.
 */


//==================================================================================================
//  kutils object
//==================================================================================================

function Kapenta_Utility() {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	//TODO: consider adding form checks here, or perhaps spin that off as another object

	this.userUID = '';		
	this.pageUID = '';		
	this.serverPath = '';	
	
	if (jsUserUID) { this.userUID = jsUserUID; } else { alert('No jsUserUID.'); }
	if (jsPageUID) { this.pageUID = jsPageUID; } else { alert('No jsPageUID'); }
	if (jsServerPath) { this.serverPath = jsServerPath; } else { alert('serverPath not set.'); }

	//==============================================================================================
	//	KAPENTA SPECIFIC
	//==============================================================================================

	//-------------------------------------------------------------------------------------------------
	//.	create a long random number
	//-------------------------------------------------------------------------------------------------
	//returns: unique identifier [string]

	this.createUID = function() {
		var theUID = '';
		for(var i = 0; i < 5; i++) { theUID += ( Math.floor ( Math.random ( ) * 9000 + 1000 ) ) + ''; }
		return theUID;
	}

	//==============================================================================================
	//	NETWORK IO
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	make an XMLHTTPRequest GET
	//----------------------------------------------------------------------------------------------
	//arg: url - url to get [string]
	//arg: callback - a function to invoke when request completes [function]

	this.httpGet = function(url, callback) {
		//TODO:
	}

	//----------------------------------------------------------------------------------------------
	//.	make an XMLHTTPRequest POST
	//----------------------------------------------------------------------------------------------
	//arg: url - url to get [string]
	//arg: params - urlencoded from values [string]
	//arg: callback - a function to invoke when request completes [function]

	this.httpPost = function(sendUrl, params, callback) {

		$.ajax({
			type: "POST",
			url: sendUrl,
			data: params
		}).done(function(msg) {
			callback(msg, '200');
		}).fail(function(jqXHR, textStatus) {
			callback(jqXHR.responseText, textStatus);
			//alert("Request failed: " + textStatus );
		});

		/* -- code used prior to jQuery integration, remove once tested
		var req;

		if (window.XMLHttpRequest) {
			//--------------------------------------------------------------------------------------
			//	create XMLHttpRequest object (Modern Browsers)
			//--------------------------------------------------------------------------------------
			req = new XMLHttpRequest();

			req.open('POST', sendUrl, true);
			req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			req.setRequestHeader('Content-length', params.length);
			req.setRequestHeader('Connection', 'close');

			//--------------------------------------------------------------------------------------
			//	create handler for returned content
			//--------------------------------------------------------------------------------------
			req.kapentaCallback = callback;
			req.onreadystatechange = function (aEvt) {  
				//klive.log('loading: ' + req.status);
				if (4 == req.readyState) {
					req.kapentaCallback(req.responseText, req.status);
				}
			}

			//--------------------------------------------------------------------------------------
			//	send the request
			//--------------------------------------------------------------------------------------
			req.send(params);

		} else {
			//--------------------------------------------------------------------------------------
			//	create XMLHttpRequest object (IE6)
			//--------------------------------------------------------------------------------------
			//req = new ActiveXObject("Microsoft.XMLHTTP");
			req = new ActiveXObject("Msxml2.XMLHTTP.6.0");

			req.Open('POST', sendUrl, true);
			req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
			req.setRequestHeader('Content-length', params.length);
			req.setRequestHeader('Connection', 'close');

			//--------------------------------------------------------------------------------------
			//	create handler for returned content
			//--------------------------------------------------------------------------------------

			var handleChange = function () {
				//klive.log('loading: ' + req.status);
				if (4 == req.readyState) {
					req.kapentaCallback(req.responseText, req.status);
				}
			}

			req.kapentaCallback = callback;
			req.onReadyStateChange = handleChange;

			//--------------------------------------------------------------------------------------
			//	send the request
			//--------------------------------------------------------------------------------------
			req.Send(params);


		}

		if (!req) {
			alert("Your browser does not support AJAX, please upgrade to use this site.");
		}
		*/
	}

	//-------------------------------------------------------------------------------------------------
	//.	set element content from a block
	//-------------------------------------------------------------------------------------------------
	//arg: elemId - ID of an HTML element [string]
	//arg: block - a kapenta block tag / view [string]
	//arg: throbber - replace block content with throbber [bool]
	//returns: true if async request sent, false if not [bool]

	this.loadBlock = function(elemId, block, throbber) {
		var theElem = document.getElementById(elemId);
		if (!theElem) { return false; }

		theElem.style.backgroundColor = '#444444';
		if (true == throbber) {
			theElem.innerHTML = ''
			 + "<img"
			 + " style='float: right;'"
			 + " src='" + this.serverPath + "themes/clockface/images/throbber-inline.gif'"
			 + " />"
		}

		var block64 = kutils.base64_encode(block);
		var params = 'b=' + escape(block64);

		var cbFn = function(responseText, status) {
			if (200 == status) { theElem.innerHTML = responseText; }
			else {
				theElem.style.backgroundColor = '#ffffff';
				theElem.innerHTML = ''
				 + "<span class='ajaxerror'>" + status + '<br/>' + responseText + '</span>';
			}
		}

		this.httpPost(kutils.serverPath + 'live/getblock/', params, cbFn);
		return true;
	}

	//==============================================================================================
	//	STRING MANIPULATION - BASE64, UTF8 AND HASHING
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	trim whitespace from both ends of a string
	//----------------------------------------------------------------------------------------------
	//	source: http://www.webtoolkit.info/javascript-trim.html
 
	this.trim = function(str, chars) { 
		return this.ltrim(this.rtrim(str, chars), chars); 
	}

	//----------------------------------------------------------------------------------------------
	//.	trim whitespace left of string
	//----------------------------------------------------------------------------------------------
 
	this.ltrim = function(str, chars) {
		chars = chars || "\\s";
		return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
	}

	//----------------------------------------------------------------------------------------------
	//.	trim whitespace right of string
	//----------------------------------------------------------------------------------------------
 
	this.rtrim = function(str, chars) {
		chars = chars || "\\s";
		return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
	}

	//----------------------------------------------------------------------------------------------
	//.	replace html entities to allow printing of HTML, XML, etc
	//----------------------------------------------------------------------------------------------

	this.htmlEntities = function(str) {
	    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	//-------------------------------------------------------------------------------------------------
	//.	base64 encode (PHP.Js implementation)
	//-------------------------------------------------------------------------------------------------
	//;	credit: Kevin van Zonneveld
	//;	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_base64_encode/
	//arg: data - string to encode [string]
	//returns: bease 64 encoded string [string]

	this.base64_encode = function(data, safe) {
    	// http://kevin.vanzonneveld.net
    	// +   original by: Tyler Akins (http://rumkin.com)
    	// +   improved by: Bayron Guevara
    	// +   improved by: Thunder.m
    	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// +   bugfixed by: Pellentesque Malesuada
    	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// -    depends on: utf8_encode
    	// *     example 1: base64_encode('Kevin van Zonneveld');
    	// *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
 
    	// mozilla has this native
    	// - but breaks in 2.0.0.12!
    	//if (typeof this.window['atob'] == 'function') {
    	//    return atob(data);
    	//}
        
		safe = safe || false;
		if (safe) { b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_*"; }

    	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, enc="", tmp_arr = [];
 
    	if (!data) {
    	    return data;
    	}
 
    	data = this.utf8_encode(data+'');
    
    	do { // pack three octets into four hexets
    	    o1 = data.charCodeAt(i++);
    	    o2 = data.charCodeAt(i++);
    	    o3 = data.charCodeAt(i++);
 
    	    bits = o1<<16 | o2<<8 | o3;
 
    	    h1 = bits>>18 & 0x3f;
    	    h2 = bits>>12 & 0x3f;
    	    h3 = bits>>6 & 0x3f;
    	    h4 = bits & 0x3f;
 
    	    // use hexets to index into b64, and append result to encoded string
    	    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
    	} while (i < data.length);
    
    	enc = tmp_arr.join('');
    
    	switch (data.length % 3) {
    	    case 1:
    	        enc = enc.slice(0, -2) + '==';
    	    break;
    	    case 2:
    	        enc = enc.slice(0, -1) + '=';
    	    break;
    	}
 
    	return enc;
	}

	//-------------------------------------------------------------------------------------------------
	//.	base64 decode (PHP.Js implementation)
	//-------------------------------------------------------------------------------------------------
	//;	credit: Kevin van Zonneveld
	//;	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_base64_decode/

	this.base64_decode = function(data, safe) {
    	// http://kevin.vanzonneveld.net
    	// +   original by: Tyler Akins (http://rumkin.com)
    	// +   improved by: Thunder.m
    	// +      input by: Aman Gupta
    	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// +   bugfixed by: Onno Marsman
    	// +   bugfixed by: Pellentesque Malesuada
    	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// +      input by: Brett Zamir (http://brett-zamir.me)
    	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// -    depends on: utf8_decode
    	// *     example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
    	// *     returns 1: 'Kevin van Zonneveld'
 
    	// mozilla has this native
    	// - but breaks in 2.0.0.12!
    	//if (typeof this.window['btoa'] == 'function') {
    	//    return btoa(data);
	    //}
 
    	var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    	var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, dec = "", tmp_arr = [];

		safe = safe || false;
		if (safe) { b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_*"; }
 
    	if (!data) { return data; }
    	data = this.trim(data + '');			// ensure it's a string
		data = data.replace(/\n/g, '');			// remove whitespace and newlines
 
    	do {  
			//--------------------------------------------------------------------------------------
			// unpack four hexets into three octets using index points in b64
			//--------------------------------------------------------------------------------------
    	    h1 = b64.indexOf(data.charAt(i++));
    	    h2 = b64.indexOf(data.charAt(i++));
    	    h3 = b64.indexOf(data.charAt(i++));
    	    h4 = b64.indexOf(data.charAt(i++));
 
    	    bits = h1<<18 | h2<<12 | h3<<6 | h4;
 
    	    o1 = bits>>16 & 0xff;
    	    o2 = bits>>8 & 0xff;
    	    o3 = bits & 0xff;
 
    	    if (h3 == 64) {
    	        tmp_arr[ac++] = String.fromCharCode(o1);
    	    } else if (h4 == 64) {
    	        tmp_arr[ac++] = String.fromCharCode(o1, o2);
    	    } else {
    	        tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
    	    }
    	} while (i < data.length);
 
    	dec = tmp_arr.join('');
    	dec = this.utf8_decode(dec);
 
    	return dec;
	}

	//-------------------------------------------------------------------------------------------------
	//.	utf8 encode (PHP.Js implementation)
	//-------------------------------------------------------------------------------------------------
	//;	credit: Kevin van Zonneveld
	//;	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_utf8_encode/
	//arg: argString - string to encode [string]
	//returns: utf8 encoded string [string]

	this.utf8_encode = function(argString) {
    	// http://kevin.vanzonneveld.net
    	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// +   improved by: sowberry
    	// +    tweaked by: Jack
    	// +   bugfixed by: Onno Marsman
    	// +   improved by: Yves Sucaet
    	// +   bugfixed by: Onno Marsman
    	// +   bugfixed by: Ulrich
    	// *     example 1: utf8_encode('Kevin van Zonneveld');
    	// *     returns 1: 'Kevin van Zonneveld'
 
    	var string = (argString+''); 	// .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
    	var utftext = "";				//%	return value [string]
    	var start = 0;					//%	[int]
		var end = 0;					//%	[int]
    	var stringl = 0;
 
    	stringl = string.length;
    	for (var n = 0; n < stringl; n++) {
    	    var c1 = string.charCodeAt(n);
    	    var enc = null;						
 
    	    if (c1 < 128) {
    	        end++;
    	    } else if (c1 > 127 && c1 < 2048) {
    	        enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
    	    } else {
    	        enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
    	    }
	        if (enc !== null) {
    	        if (end > start) { utftext += string.substring(start, end); }
    	        utftext += enc;
    	        start = end = n+1;
    	    }
	    }
 	
    	if (end > start) { utftext += string.substring(start, string.length); }
    	return utftext;
	}

	//-------------------------------------------------------------------------------------------------
	//.	utf8 decode (PHP.Js implementation)
	//-------------------------------------------------------------------------------------------------
	//	credit: Kevin van Zonneveld
	//	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_utf8_decode/
	//arg: str_data - string to encode [string]
	//returns: utf8 decoded data [string]

	this.utf8_decode = function(str_data) {
    	// http://kevin.vanzonneveld.net
    	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    	// +      input by: Aman Gupta
    	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// +   improved by: Norman "zEh" Fuchs
    	// +   bugfixed by: hitwork
    	// +   bugfixed by: Onno Marsman
    	// +      input by: Brett Zamir (http://brett-zamir.me)
    	// +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    	// *     example 1: utf8_decode('Kevin van Zonneveld');
    	// *     returns 1: 'Kevin van Zonneveld'
 
    	var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
    	str_data += '';
    
    	while ( i < str_data.length ) {
    	    c1 = str_data.charCodeAt(i);
    	    if (c1 < 128) {
    	        tmp_arr[ac++] = String.fromCharCode(c1);
    	        i++;
    	    } else if ((c1 > 191) && (c1 < 224)) {
    	        c2 = str_data.charCodeAt(i+1);
    	        tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
    	        i += 2;
    	    } else {
    	        c2 = str_data.charCodeAt(i+1);
    	        c3 = str_data.charCodeAt(i+2);
    	        tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
    	        i += 3;
    	    }
    	}
 
    	return tmp_arr.join('');
	}

	//----------------------------------------------------------------------------------------------
	//  SHA-1 implementation in JavaScript (c) Chris Veness 2002-2009                                 
	//----------------------------------------------------------------------------------------------
	// source: http://www.movable-type.co.uk/scripts/sha1.html
	// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/

	this.sha1Hash = function(msg) {
    	// constants [§4.2.1]
    	var K = [0x5a827999, 0x6ed9eba1, 0x8f1bbcdc, 0xca62c1d6];

	    // PREPROCESSING 
    	msg += String.fromCharCode(0x80); // add trailing '1' bit (+ 0's padding) to string [§5.1.1]

    	// convert string msg into 512-bit/16-integer blocks arrays of ints [§5.2.1]
    	var l = msg.length/4 + 2;  // length (in 32-bit integers) of msg + ‘1’ + appended length
    	var N = Math.ceil(l/16);   // number of 16-integer-blocks required to hold 'l' ints
    	var M = new Array(N);
    	for (var i=0; i<N; i++) {
    	    M[i] = new Array(16);
    	    for (var j=0; j<16; j++) {  // encode 4 chars per integer, big-endian encoding
            M[i][j] = (msg.charCodeAt(i*64+j*4)<<24) | (msg.charCodeAt(i*64+j*4+1)<<16) | 
                      (msg.charCodeAt(i*64+j*4+2)<<8) | (msg.charCodeAt(i*64+j*4+3));
    	    }
    	}
    	// add length (in bits) into final pair of 32-bit integers (big-endian) [5.1.1]
    	// note: most significant word would be (len-1)*8 >>> 32, but since JS converts
    	// bitwise-op args to 32 bits, we need to simulate this by arithmetic operators
    	M[N-1][14] = ((msg.length-1)*8) / Math.pow(2, 32); M[N-1][14] = Math.floor(M[N-1][14])
    	M[N-1][15] = ((msg.length-1)*8) & 0xffffffff;

    	// set initial hash value [§5.3.1]
    	var H0 = 0x67452301;
    	var H1 = 0xefcdab89;
    	var H2 = 0x98badcfe;
    	var H3 = 0x10325476;
    	var H4 = 0xc3d2e1f0;

    	// HASH COMPUTATION [§6.1.2]

    	var W = new Array(80); var a, b, c, d, e;
   		for (var i=0; i<N; i++) {

    	    // 1 - prepare message schedule 'W'
    	    for (var t=0;  t<16; t++) W[t] = M[i][t];
    	    for (var t=16; t<80; t++) W[t] = this.sha1ROTL(W[t-3] ^ W[t-8] ^ W[t-14] ^ W[t-16], 1);

    	    // 2 - initialise five working variables a, b, c, d, e with previous hash value
    	    a = H0; b = H1; c = H2; d = H3; e = H4;

    	    // 3 - main loop
    	    for (var t=0; t<80; t++) {
    	        var s = Math.floor(t/20); // seq for blocks of 'f' functions and 'K' constants
    	        var T = (this.sha1ROTL(a,5) + this.sha1f(s,b,c,d) + e + K[s] + W[t]) & 0xffffffff;
    	        e = d;
    	        d = c;
    	        c = this.sha1ROTL(b, 30);
    	        b = a;
    	        a = T;
    	    }

    	    // 4 - compute the new intermediate hash value
    	    H0 = (H0+a) & 0xffffffff;  // note 'addition modulo 2^32'
    	    H1 = (H1+b) & 0xffffffff; 
    	    H2 = (H2+c) & 0xffffffff; 
    	    H3 = (H3+d) & 0xffffffff; 
    	    H4 = (H4+e) & 0xffffffff;
    	}

    	return H0.toHexStr() + H1.toHexStr() + H2.toHexStr() + H3.toHexStr() + H4.toHexStr();
	}

	//--------------------------------------------------------------------------------------------------
	//. function 'f' [§4.1.1]
	//--------------------------------------------------------------------------------------------------

	this.sha1f = function(s, x, y, z) {
    	switch (s) {
    	case 0: return (x & y) ^ (~x & z);           // Ch()
    	case 1: return x ^ y ^ z;                    // Parity()
    	case 2: return (x & y) ^ (x & z) ^ (y & z);  // Maj()
    	case 3: return x ^ y ^ z;                    // Parity()
    	}
	}

	//----------------------------------------------------------------------------------------------
	//. rotate left (circular left shift) value x by n positions [§3.2.5]
	//----------------------------------------------------------------------------------------------

	this.sha1ROTL = function(x, n) { return (x<<n) | (x>>>(32-n)); }

	//--------------------------------------------------------------------------------------------------
	// extend Number class with a tailored hex-string method (note toString(16) is 
	// implementation-dependant, and in IE returns signed numbers when used on full words)

	Number.prototype.toHexStr = function() {
	    var s="", v;
	    for (var i=7; i>=0; i--) { v = (this>>>(i*4)) & 0xf; s += v.toString(16); }
	    return s;
	}

	//==============================================================================================
	//	IFRAMES AND DIVS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	toggle visiblity of a div (from navtitlebar);
	//----------------------------------------------------------------------------------------------
	//;	source: kapenta.org.uk

	this.toggleNTVisible = function(navImgId, divId) {
		
		var cbFn = function() { if (window.parent) { kutils.resizeIFrame(); } }
		var icoDir = jsServerPath + 'themes/' + jsTheme;

		if ($('#' + divId).is(':hidden')) {
			$('#' + divId).css('visibility', 'visible');
			$('#' + navImgId).attr('src', icoDir + '/images/icons/btn-minus.png');
			$('#' + divId).show('fast', cbFn);

			

		} else {
			$('#' + divId).hide('fast', cbFn);
			$('#' + navImgId).attr('src', icoDir + '/images/icons/btn-plus.png');
		}

		//	-- version used before introduction of jQuery, delete once stable
		//var theDiv = document.getElementById(divId);
		//var theImg = document.getElementById(navImgId);

		//----------------------------------------------------------------------------------------------
		//	make the target div visible or not
		//----------------------------------------------------------------------------------------------

		//if (theDiv.style.visibility == 'hidden') { 
		//	this.show(divId);
		//	theImg.src = jsServerPath + 'themes/clockface/images/icons/btn-minus.png';
		//	theImg.onclick = "toggleVisible('" + navImgId + "', '" + divId + "');";
		//
		//} else {
		//	this.hide(divId);
		//	theImg.src = jsServerPath + 'themes/clockface/images/icons/btn-plus.png';
		//	theImg.onclick = "toggleVisible('" + navImgId + "', '" + divId + "');";
		//
		//}
		//
		this.resizeAllIFrames();
	}

	//----------------------------------------------------------------------------------------------
	//.	hide a div or other block element
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.hide = function(divId) {
		$('#' + divId).hide('slow'); 
		//var theDiv = document.getElementById(divId);
		//if (!theDiv) { return false; }
		//theDiv.style.visibility = 'hidden'; 
		//theDiv.style.display = 'none'; 		
		//return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a hidden div visible (or other block element)
	//----------------------------------------------------------------------------------------------

	this.show = function(divId) {
		var theDiv = document.getElementById(divId);
		if (!theDiv) { return false; }
		//alert('show: ' + divId);
		//$('#' . divId).show('slow'); 
		theDiv.style.visibility = 'visible'; 
		theDiv.style.display = 'block'; 		
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	resize all child iframes to fit their content
	//----------------------------------------------------------------------------------------------
	//;	source: kapenta.org.uk

	this.resizeAllIFrames = function() {
		var allFrames = document.getElementsByTagName('IFRAME');		// get all iframes
		for (i = 0; i < allFrames.length; i++) {
			var currFrame = allFrames[i];
			
			//------------------------------------------------------------------------------------------
			//	children where class='consoleif'
			//------------------------------------------------------------------------------------------
			var className = currFrame.getAttribute('class');
			if (className) {
				if ('consoleif' == className) {
					//alert('child frame: ' + currFrame.name);
					currFrame.contentWindow.kutils.resizeAllIFrames();
				}
			}

		}

		this.resizeIFrame();
	}

	//----------------------------------------------------------------------------------------------
	//.	resize the current iframe to fit content
	//----------------------------------------------------------------------------------------------
	//;	note that this only works is the iframe has a name

	this.resizeIFrame = function() {
		if (self == top) { return false; }		// not an iframe, no parent
		frameObj = window.parent.document.getElementsByName(window.name);
		if (frameObj[0]) {
			if (-1 == ifMaxHeight) {
				// iframe height same as content height
				frameObj[0].height = document.body.offsetHeight + 40;

			} else {
				// iframe height same as content height unless greater than maxHeight
				if ((document.body.offsetHeight + 40) > ifMaxHeight) {
					frameObj[0].height = ifMaxHeight;
				} else {
					frameObj[0].height = document.body.offsetHeight + 40;
				}
			}
		}
	}

} // end of Kapenta_Utilities

	kutils = new Kapenta_Utility();

//-------------------------------------------------------------------------------------------------
//	create a long random number
//-------------------------------------------------------------------------------------------------

function createUID() {
	if ('admin' == jsUserUID) { alert('JS Deprecated: createUID()'); }
	return kutils.createUID()
}

//-------------------------------------------------------------------------------------------------
//	base64 encode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------

function base64_encode (data) {
	if ('admin' == jsUserUID) { alert('JS Deprecated: base64_encode()'); }
	return kutils.base64_encode(data);
}

//-------------------------------------------------------------------------------------------------
//	base64 decode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------

function base64_decode (data) {
	if ('admin' == jsUserUID) { alert('JS Deprecated: base64_decode()'); }
	return kutils.base64_decode(data);	
}

//-------------------------------------------------------------------------------------------------
//	utf8 encode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------

function utf8_encode(argString) { return kutils.utf8_encode(argString);	}

//-------------------------------------------------------------------------------------------------
//	utf8 decode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------

function utf8_decode(str_data) { return kutils.utf8_decode(str_data); }

//-------------------------------------------------------------------------------------------------
//	functions to trim whitespace from a string
//-------------------------------------------------------------------------------------------------
//	source: http://www.webtoolkit.info/javascript-trim.html
 
function trim(str, chars) { 
	if ('admin' == jsUserUID) { alert('JS Deprecated: trim()'); }
	return kutils.trim(str, chars); 
}
 
function ltrim(str, chars) {
	if ('admin' == jsUserUID) { alert('JS Deprecated: ltrim()'); }
	return kutils.ltrim(str, chars);
}
 
function rtrim(str, chars) {
	if ('admin' == jsUserUID) { alert('JS Deprecated: rtrim()'); }
	return kutils.rtrim(str, chars);
}

//-------------------------------------------------------------------------------------------------
//	load a textarea with base64_encoded data
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function base64_loadTextArea(taId, b64data) {
	var theTa = document.getElementById(taId);
	if (null == theTa) { return false; }
	theTa.value = kutils.base64_decode(b64data);
	return true;
}


//--------------------------------------------------------------------------------------------------
//.	resize all child iframes, so that the container fits the content
//--------------------------------------------------------------------------------------------------

function toggleVisible(navImgId, divId) { kutils.toggleNTVisible(navImgId, divId); }

function resizeAllIFrames() {
	var allFrames = document.getElementsByTagName('IFRAME');		// get all iframes
	for (i = 0; i < allFrames.length; i++) {
		var currFrame = allFrames[i];
			
		//------------------------------------------------------------------------------------------
		//	children where class='consoleif'
		//------------------------------------------------------------------------------------------
		var className = currFrame.getAttribute('class');
		if (className) {
			if ('consoleif' == className) {
				alert('child frame: ' + currFrame.name);
				currFrame.contentWindow.resizeFrame();
			}
		}

	}

	//if (hasParentFrame) { resizeFrame(); }
}

//-------------------------------------------------------------------------------------------------
//	attach onclick event to something
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function attachOnClick(elemId, jsCode) {
	var theImg = document.getElementById(elemId);
	if (null == theImg) { return false; }
	theImg.onclick = jsCode;
	return true;
}


//-------------------------------------------------------------------------------------------------
//	set the contents of a div
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function divSetContent(divId, divContent) {
	var theDiv = document.getElementById(divId);
	if (null == theDiv) { return false; }
	theDiv.innerHTML = divContent;
}

//-------------------------------------------------------------------------------------------------
//	discover if a value exists in an array (roughtly equivalent to PHP in_array() function)
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function in_array(needle, haystack) {
	for (var i in haystack) { if (haystack[i] == needle) { return true; } }
	return false;
}

//-------------------------------------------------------------------------------------------------
//	read all values in a form and convert to application/x-www-form-urlencoded
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function urlEncodeForm(theForm) {
	var formFields = new Array();
	var allowedTypes = new Array('hidden', 'textarea', 'text', 'select');
	for (var i in theForm.elements) {
		var elem = theForm.elements[i];
		if (elem) {
			if (elem.name) {
				if (in_array(elem.type, allowedTypes) == true) { 
					formFields.push(elem.name + '=' + encodeURIComponent(elem.value)); 
				}
			}
		}
	}
	return formFields.join('&');
}

//--------------------------------------------------------------------------------------------------
//	print a page of items
//--------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk
//	divID is the id of a div whose content will be replaced by the page
//	callBackFnName is the name of a function to ba called with pageNo when the page is changed
//	items should be a 2d array, [0] -> UID [1] -> html

function printPage(divId, callBackFnName, items, pageNo, pageSize) {
	var html = ''
	html = html + mkPagination(callBackFnName, items, pageNo, pageSize);

	var startItem = pageNo * pageSize;
	var endItem = startItem + pageSize;
	for (i = startItem; ((i < endItem) && (i < items.length)); i++) {
		html = html + base64_decode(items[i][1]);
	}

	divSetContent(divId, html);
}

//--------------------------------------------------------------------------------------------------
//	make pagination bar (page caterpillar: << | >> [1][2][3][4]...[15][16] )
//--------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk
//	items should be a 2d array, [0] -> UID [1] -> html
//	page is the page number

function mkPagination(callBackFnName, items, pageNo, pageSize) {
	var prevLink = '';
	var nextLink = '';
	var numPages = Math.ceil(items.length / pageSize);
	var pgBarId = '#pagination' + Math.random();
	var html =  "<a name='" + pgBarId + "'></a>";

	if (pageNo > 0) { 
		ocjs = callBackFnName + "(" + (pageNo - 1) + ");";
		prevLink = "<a href='" + pgBarId + "' onClick=\"" + ocjs + "\" class='black'><< previous </a> | ";
	}

	if (pageNo < (numPages - 1)) { 
		ocjs = callBackFnName + "(" + (pageNo + 1) + ");";
		nextLink = "<a href='" + pgBarId + "' onClick=\"" + ocjs + "\" class='black'> next >> </a>"; 
	}

	var pagination = '';
	for (var i = 1; i <= numPages; i++) {
		if (i == (pageNo + 1)) {
			pagination = pagination + "<b>[" + i + "]</b> \n";
		} else {
			ocjs = callBackFnName + "(" + (i - 1) + ");";
			pagination = pagination + "<a href='" + pgBarId + "' onClick=\"" + ocjs + "\" class='black'>[" + i + "]</a> \n";
		}
	}

	html = html + "<table noborder width='100%'><tr><td bgcolor='#dddddd'>\n&nbsp;&nbsp;";
	html = html + prevLink + nextLink + ' ' + pagination + "<br/>\n";
	html = html + "</td></tr></table>\n";	

	return html;
}

//--------------------------------------------------------------------------------------------------
//  SHA-1 implementation in JavaScript (c) Chris Veness 2002-2009                                 
//--------------------------------------------------------------------------------------------------
// source: http://www.movable-type.co.uk/scripts/sha1.html
// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/

function sha1Hash(msg) {
    // constants [§4.2.1]
    var K = [0x5a827999, 0x6ed9eba1, 0x8f1bbcdc, 0xca62c1d6];

    // PREPROCESSING 
 
    msg += String.fromCharCode(0x80); // add trailing '1' bit (+ 0's padding) to string [§5.1.1]

    // convert string msg into 512-bit/16-integer blocks arrays of ints [§5.2.1]
    var l = msg.length/4 + 2;  // length (in 32-bit integers) of msg + ‘1’ + appended length
    var N = Math.ceil(l/16);   // number of 16-integer-blocks required to hold 'l' ints
    var M = new Array(N);
    for (var i=0; i<N; i++) {
        M[i] = new Array(16);
        for (var j=0; j<16; j++) {  // encode 4 chars per integer, big-endian encoding
            M[i][j] = (msg.charCodeAt(i*64+j*4)<<24) | (msg.charCodeAt(i*64+j*4+1)<<16) | 
                      (msg.charCodeAt(i*64+j*4+2)<<8) | (msg.charCodeAt(i*64+j*4+3));
        }
    }
    // add length (in bits) into final pair of 32-bit integers (big-endian) [5.1.1]
    // note: most significant word would be (len-1)*8 >>> 32, but since JS converts
    // bitwise-op args to 32 bits, we need to simulate this by arithmetic operators
    M[N-1][14] = ((msg.length-1)*8) / Math.pow(2, 32); M[N-1][14] = Math.floor(M[N-1][14])
    M[N-1][15] = ((msg.length-1)*8) & 0xffffffff;

    // set initial hash value [§5.3.1]
    var H0 = 0x67452301;
    var H1 = 0xefcdab89;
    var H2 = 0x98badcfe;
    var H3 = 0x10325476;
    var H4 = 0xc3d2e1f0;

    // HASH COMPUTATION [§6.1.2]

    var W = new Array(80); var a, b, c, d, e;
    for (var i=0; i<N; i++) {

        // 1 - prepare message schedule 'W'
        for (var t=0;  t<16; t++) W[t] = M[i][t];
        for (var t=16; t<80; t++) W[t] = sha1ROTL(W[t-3] ^ W[t-8] ^ W[t-14] ^ W[t-16], 1);

        // 2 - initialise five working variables a, b, c, d, e with previous hash value
        a = H0; b = H1; c = H2; d = H3; e = H4;

        // 3 - main loop
        for (var t=0; t<80; t++) {
            var s = Math.floor(t/20); // seq for blocks of 'f' functions and 'K' constants
            var T = (sha1ROTL(a,5) + sha1f(s,b,c,d) + e + K[s] + W[t]) & 0xffffffff;
            e = d;
            d = c;
            c = sha1ROTL(b, 30);
            b = a;
            a = T;
        }

        // 4 - compute the new intermediate hash value
        H0 = (H0+a) & 0xffffffff;  // note 'addition modulo 2^32'
        H1 = (H1+b) & 0xffffffff; 
        H2 = (H2+c) & 0xffffffff; 
        H3 = (H3+d) & 0xffffffff; 
        H4 = (H4+e) & 0xffffffff;
    }

    return H0.toHexStr() + H1.toHexStr() + H2.toHexStr() + H3.toHexStr() + H4.toHexStr();
}

//--------------------------------------------------------------------------------------------------
// function 'f' [§4.1.1]

function sha1f(s, x, y, z) {
    switch (s) {
    case 0: return (x & y) ^ (~x & z);           // Ch()
    case 1: return x ^ y ^ z;                    // Parity()
    case 2: return (x & y) ^ (x & z) ^ (y & z);  // Maj()
    case 3: return x ^ y ^ z;                    // Parity()
    }
}

//--------------------------------------------------------------------------------------------------
// rotate left (circular left shift) value x by n positions [§3.2.5]

function sha1ROTL(x, n) { return (x<<n) | (x>>>(32-n)); }

//--------------------------------------------------------------------------------------------------
// extend Number class with a tailored hex-string method (note toString(16) is 
// implementation-dependant, and in IE returns signed numbers when used on full words)

Number.prototype.toHexStr = function() {
    var s="", v;
    for (var i=7; i>=0; i--) { v = (this>>>(i*4)) & 0xf; s += v.toString(16); }
    return s;
}

//--------------------------------------------------------------------------------------------------
//  copy the contents of one div into another, replacing whatever was in the original
//--------------------------------------------------------------------------------------------------
//arg: fromID - ID of a div [string]
//arg: toID - ID of a div [string]
//returns: true on success, false on failure [bool]

function divCopyInnerHtml(fromId, toId) {
	var fromDiv = document.getElementById(fromId);
	var toDiv = document.getElementById(toId);

	// TODO: better error checking here
	if ((undefined == fromDiv) || (null == fromDiv)) { return false; }
	if ((undefined == toDiv) || (null == toDiv)) { return false; }

	toDiv.innerHTML = fromDiv.innerHTML;
	return true;
}

//--------------------------------------------------------------------------------------------------
//  add a form hash (used to alert users when they navigate away from a page with unsaved changes)
//--------------------------------------------------------------------------------------------------
// source: http://kapenta.org.uk
// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/
// notes: form checks are stored in an array of formName|base64EncodedHash
// notes: formId is a string containing an XHTML element ID

function formCheckAdd(formId) {
	formChecks[formChecks.length] = formId + '|' + formCheckMakeHash(formId);
}

//--------------------------------------------------------------------------------------------------
//  make a form hash
//--------------------------------------------------------------------------------------------------
// source: http://kapenta.org.uk
// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/
// notes: formID is a string containing an XHTML element ID

function formCheckMakeHash(formId) {
	var theForm = document.getElementById(formId);
	if (null == theForm) { return false; }
	var strFormData = urlEncodeForm(theForm);
	return sha1Hash(strFormData);
}

//--------------------------------------------------------------------------------------------------
//  remove a form hash (when form is submitted)
//--------------------------------------------------------------------------------------------------
// source: http://kapenta.org.uk
// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/

function formCheckRemove(formId) {
	for (var i = 0; i < formChecks.length; i++) {	// go through all formCheck entries
		pair = formChecks[i];
		parts = pair.split('|');
		if (parts[0] == formId) { formChecks.splice(i, 1); }	// if formId is found, remove it
	}
}

//--------------------------------------------------------------------------------------------------
//  check form hashes against stored value (ie, are these unsaved changes)
//--------------------------------------------------------------------------------------------------
// source: http://kapenta.org.uk
// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/

function formCheckExecuteAll() {
	for (var i = 0; i < formChecks.length; i++) {	// go through all formCheck entries

		pair = formChecks[i];						// get the next pair
		parts = pair.split('|');					// break into formID (part 0) and hash (part 1)
		formId = parts[0];
		oldHash = parts[1];
		newHash = formCheckMakeHash(formId);		// hash the forms contents as at now

		if (oldHash == newHash) {					// compare
			// anything here?
		} else {
			return 'You have unsaved changes, leave the page anyway?';
		}
	}	
	return false;
}

//--------------------------------------------------------------------------------------------------
//  for debugging, can remove this in production
//--------------------------------------------------------------------------------------------------
// source: http://kapenta.org.uk
// licence: LGPL - http://creativecommons.org/licenses/LGPL/2.1/

function formCheckShowAll() {
	var checkStr = '';
	for (var i = 0; i < formChecks.length; i++) {	// go through all formCheck entries
		pair = formChecks[i];
		parts = pair.split('|');
		checkStr = checkStr + i + ' => ' + parts[0] + ' | ' + parts[1] + "\n";
	}		
	alert(checkStr);
}

//--------------------------------------------------------------------------------------------------
//  for debugging, can remove this in production
//--------------------------------------------------------------------------------------------------

function logDebug(msg) {
	var divDebug = document.getElementById('debugger');
	divDebug.innerHTML = divDebug.innerHTML + msg + "<br/>\n";
}
