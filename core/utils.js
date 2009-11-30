//=================================================================================================
//	common javascript utilities which are widely used by this site
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

//-------------------------------------------------------------------------------------------------
//	base64 encode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------
//	credit: Kevin van Zonneveld
//	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_base64_encode/


function base64_encode (data) {
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
//	base64 decode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------
//	credit: Kevin van Zonneveld
//	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_base64_decode/

function base64_decode (data) {
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
 
    if (!data) {
        return data;
    }
 
    data += '';
 
    do {  // unpack four hexets into three octets using index points in b64
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
//	utf8 encode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------
//	credit: Kevin van Zonneveld
//	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_utf8_encode/

function utf8_encode ( argString ) {
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
 
    var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
 
    var utftext = "";
    var start, end;
    var stringl = 0;
 
    start = end = 0;
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
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }
 
    if (end > start) {
        utftext += string.substring(start, string.length);
    }
 
    return utftext;
}

//-------------------------------------------------------------------------------------------------
//	utf8 decode (PHP.Js implementation)
//-------------------------------------------------------------------------------------------------
//	credit: Kevin van Zonneveld
//	http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_utf8_decode/

function utf8_decode ( str_data ) {
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

//-------------------------------------------------------------------------------------------------
//	functions to trim whitespace from a string
//-------------------------------------------------------------------------------------------------
//	source: http://www.webtoolkit.info/javascript-trim.html
 
function trim(str, chars) { return ltrim(rtrim(str, chars), chars); }
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

//-------------------------------------------------------------------------------------------------
//	load a textarea with base64_encoded data
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function base64_loadTextArea(taId, b64data) {
	var theTa = document.getElementById(taId);
	if (null == theTa) { return false; }
	theTa.value = base64_decode(b64data);
	return true;
}

//-------------------------------------------------------------------------------------------------
//	toggle visiblity of a div (from navtitlebar);
//-------------------------------------------------------------------------------------------------
//	source: kapenta.org.uk

function toggleVisible(navImgId, divId) {
	var theDiv = document.getElementById(divId);
	var theImg = document.getElementById(navImgId);

	if (theDiv.style.visibility == 'hidden') { 
		theDiv.style.visibility = 'visible'; 
		theDiv.style.display = 'block'; 
		theImg.src = jsServerPath + 'themes/clockface/icons/btn-minus.png';
		theImg.onclick = "toggleVisible('" + navImgId + "', '" + divId + "');";

	} else {
		theDiv.style.visibility = 'hidden'; 
		theDiv.style.display = 'none'; 
		theImg.src = jsServerPath + 'themes/clockface/icons/btn-plus.png';
		theImg.onclick = "toggleVisible('" + navImgId + "', '" + divId + "');";

	}
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

