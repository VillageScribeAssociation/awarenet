(function() {

// Debugging

TEST = { };

TEST = (function() {
	var max,
		depth = 0,
		INDENT = "\t\t\t\t\t\t\t\t\t\t\t\t\t\t";
	function valueToStr(value, depth) {
		switch(typeof value) {
			case "object": 
				return objectToStr(value, depth + 1);
			case "function": 
				return "function";
			case "string":
				return "'"+value+"'";
			default:         
				return value;
		}
	};
	function objectToStr(object, depth) {
		if(depth > max)
			return false;
		var type = Object.prototype.toString.call(object),
			output = "\n",
			indent = INDENT.substr(0, depth);
		for(var key in object)
			output += indent + valueToStr(key) + ": " + valueToStr(object[key], depth) + ",\n";
		indent = INDENT.substr(0, depth - 1);
		switch(type) {
			case "[object Object]":
				return "{ " + output.substr(0, output.length - 2) + "\n" + indent + "}";  
			case "[object Array]":
				return "[ " + output.substr(0, output.length - 2) + "\n" + indent + "]";  
			default:
				return;
		}
	};
	return function(value, aggregate, MAX) {
		var d = document.getElementById("TEST");
		if(!d) { // element does not exist
			var d = document.createElement("div");
			d.id = "TEST";
			document.body.appendChild(d);
		}
		// recurse
		max = MAX || 2;
		if(typeof value != "string") 
			value = valueToStr(value, depth);
		var br = Object.prototype.toString.call(d) == "[object HTMLDivElement]" ? "<br>" : "\n";
		d.innerHTML = aggregate ? d.innerHTML + br + value : value;
	};
})();

TEST.element = function(type, style) {
	var d = document.getElementById("TEST");
	if(d) // remove element
		document.body.removeChild(d);
	var d = document.createElement(type);
	d.id = "TEST";
	if(style)
		d.setAttribute("style", style);
	document.body.appendChild(d);
	if(type == "textarea") {
		d.style.width = (window.innerWidth - d.offsetLeft - 6) + "px";
		d.style.height = (window.innerHeight - d.offsetTop - 6) + "px";
	}
	return d;
};

TEST.speed = function(a, b, n) { 
	var T1, T2, Z1, Z2;
	T1 = now(); 
	for(var i = 0; i<= n; i++) a();
	Z1 = now()-T1;
	T2 = now(); 
	for(var i = 0; i<= n; i++) b();
	Z2 = now()-T2;
	TEST('A: '+Z1+', B: '+Z2+' = '+(Math.round(Z2/Z1*1000)/1000), 1);
};

// Class: Dump
// Author: Shuns (www.netgrow.com.au/files)
// Last Updated: 10/10/06
// Version: 1.1

dump = function(object, aggregate, showTypes) {
  var dump = '';
  var st = typeof showTypes == 'undefined' ? true : showTypes;
  var winName = 'dumpWin';
  var browser = _dumpIdentifyBrowser();	
  var w = 760;
  var h = 500;
  var leftPos = screen.width ? (screen.width - w) / 2 : 0;
  var topPos = screen.height ? (screen.height - h) / 2 : 0;
  var settings = 'height=' + h + ',width=' + w + ',top=' + topPos + ',left=' + leftPos + ',scrollbars=yes,menubar=yes,status=yes,resizable=yes';
  var title = 'Dump';
  var script = 'function tRow(s) {t = s.parentNode.lastChild;tTarget(t, tSource(s)) ;}function tTable(s) {var switchToState = tSource(s) ;var table = s.parentNode.parentNode;for (var i = 1; i < table.childNodes.length; i++) {t = table.childNodes[i] ;if (t.style) {tTarget(t, switchToState);}}}function tSource(s) {if (s.style.fontStyle == "italic" || s.style.fontStyle == null) {s.style.fontStyle = "normal";s.title = "click to collapse";return "open";} else {s.style.fontStyle = "italic";s.title = "click to expand";return "closed" ;}}function tTarget (t, switchToState) {if (switchToState == "open") {t.style.display = "";} else {t.style.display = "none";}}';		

  dump += (/string|number|undefined|boolean/.test(typeof(object)) || object == null) ? object : recurse(object, typeof object);

  var d = document.getElementById("TEST");
  if(!d) { // TEST does not exist
	  var d = document.createElement("div");
	  d.id = "TEST";
	  document.body.appendChild(d);
  }
  d.innerHTML = aggregate ? d.innerHTML + dump : dump;
  
  function recurse(o, type) {
    var i;
	var j = 0;
	var r = '';
	type = _dumpType(o);
	switch (type) {		
	  case 'regexp':
	    var t = type;
	    r += '<table' + _dumpStyles(t,'table') + '><tr><th colspan="2"' + _dumpStyles(t,'th') + '>' + t + '</th></tr>';
	    r += '<tr><td colspan="2"' + _dumpStyles(t,'td-value') + '><table' + _dumpStyles('arguments','table') + '><tr><td' + _dumpStyles('arguments','td-key') + '><i>RegExp: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o + '</td></tr></table>';  
	    j++;
	    break;
	  case 'date':
	    var t = type;
	    r += '<table' + _dumpStyles(t,'table') + '><tr><th colspan="2"' + _dumpStyles(t,'th') + '>' + t + '</th></tr>';
	    r += '<tr><td colspan="2"' + _dumpStyles(t,'td-value') + '><table' + _dumpStyles('arguments','table') + '><tr><td' + _dumpStyles('arguments','td-key') + '><i>Date: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o + '</td></tr></table>';  
	    j++;
	    break;
	  case 'function':
	    var t = type;
	    var a = o.toString().match(/^.*function.*?\((.*?)\)/im); 
	    var args = (a == null || typeof a[1] == 'undefined' || a[1] == '') ? 'none' : a[1];
	    r += '<table' + _dumpStyles(t,'table') + '><tr><th colspan="2"' + _dumpStyles(t,'th') + '>' + t + '</th></tr>';
	    r += '<tr><td colspan="2"' + _dumpStyles(t,'td-value') + '><table' + _dumpStyles('arguments','table') + '><tr><td' + _dumpStyles('arguments','td-key') + '><i>Arguments: </i></td><td' + _dumpStyles(type,'td-value') + '>' + args + '</td></tr><tr><td' + _dumpStyles('arguments','td-key') + '><i>Function: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o + '</td></tr></table>';  
	    j++;
	    break;
	  case 'domelement':
	    var t = type;
	    r += '<table' + _dumpStyles(t,'table') + '><tr><th colspan="2"' + _dumpStyles(t,'th') + '>' + t + '</th></tr>';
	    r += '<tr><td' + _dumpStyles(t,'td-key') + '><i>Node Name: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o.nodeName.toLowerCase() + '</td></tr>';  
		r += '<tr><td' + _dumpStyles(t,'td-key') + '><i>Node Type: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o.nodeType + '</td></tr>'; 
		r += '<tr><td' + _dumpStyles(t,'td-key') + '><i>Node Value: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o.nodeValue + '</td></tr>'; 					
		r += '<tr><td' + _dumpStyles(t,'td-key') + '><i>innerHTML: </i></td><td' + _dumpStyles(type,'td-value') + '>' + o.innerHTML + '</td></tr>';  
	    j++;
	    break;		
	}
	if (/object|array/.test(type)) {
      for (i in o) {
	    var t = _dumpType(o[i]);
	    if (j < 1) {
	      r += '<table' + _dumpStyles(type,'table') + '><tr><th colspan="2"' + _dumpStyles(type,'th') + '>' + type + '</th></tr>';
		  j++;	  
	    }
	    if (typeof o[i] == 'object' && o[i] != null) { 
		  r += '<tr><td' + _dumpStyles(type,'td-key') + '>' + i + (st ? ' [' + t + ']' : '') + '</td><td' + _dumpStyles(type,'td-value') + '>' + recurse(o[i], t) + '</td></tr>';	
	    } else if (typeof o[i] == 'function') {
		  r += '<tr><td' + _dumpStyles(type ,'td-key') + '>' + i + (st ? ' [' + t + ']' : '') + '</td><td' + _dumpStyles(type,'td-value') + '>' + recurse(o[i], t) + '</td></tr>';  	
		} else {
		  r += '<tr><td' + _dumpStyles(type,'td-key') + '>' + i + (st ? ' [' + t + ']' : '') + '</td><td' + _dumpStyles(type,'td-value') + '>' + o[i] + '</td></tr>';  
	    }
	  }
	}
	if (j == 0) {
	  r += '<table' + _dumpStyles(type,'table') + '><tr><th colspan="2"' + _dumpStyles(type,'th') + '>' + type + ' [empty]</th></tr>'; 	
	}
	r += '</table>';
	return r;
  };	
};

dump.object = function(o) {
	doc = o;
};

var _dumpStyles = function(type, use) {
  var r = '';
  var table = 'font-size:xx-small;font-family:verdana,arial,helvetica,sans-serif;cell-spacing:2px;';
  var th = 'font-size:xx-small;font-family:verdana,arial,helvetica,sans-serif;text-align:left;color: white;padding: 5px;vertical-align :top;cursor:hand;cursor:pointer;';
  var td = 'font-size:xx-small;font-family:verdana,arial,helvetica,sans-serif;vertical-align:top;padding:3px;';
  var thScript = 'onClick="tTable(this);" title="click to collapse"';
  var tdScript = 'onClick="tRow(this);" title="click to collapse"';
  switch (type) {
	case 'string':
	case 'number':
	case 'boolean':
	case 'undefined':
	case 'object':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#0000cc;"';
		  break;
		case 'th':
		  r = ' style="' + th + 'background-color:#4444cc;"' + thScript;
		  break;
		case 'td-key':
		  r = ' style="' + td + 'background-color:#ccddff;cursor:hand;cursor:pointer;"' + tdScript;
		  break;
		case 'td-value':
		  r = ' style="' + td + 'background-color:#fff;"';
		  break;
	  }
	  break;
	case 'array':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#006600;"';
		  break;
		case 'th':
		  r = ' style="' + th + 'background-color:#009900;"' + thScript;
		  break;
		case 'td-key':
		  r = ' style="' + td + 'background-color:#ccffcc;cursor:hand;cursor:pointer;"' + tdScript;
		  break;
		case 'td-value':
		  r = ' style="' + td + 'background-color:#fff;"';
		  break;
	  }	
	  break;
	case 'function':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#aa4400;"';
		  break;
		case 'th':
		  r = ' style="' + th + 'background-color:#cc6600;"' + thScript;
		  break;
		case 'td-key':
		  r = ' style="' + td + 'background-color:#fff;cursor:hand;cursor:pointer;"' + tdScript;
		  break;
		case 'td-value':
		  r = ' style="' + td + 'background-color:#fff;"';
		  break;
	  }	
	  break;
	case 'arguments':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#dddddd;cell-spacing:3;"';
		  break;
		case 'td-key':
		  r = ' style="' + th + 'background-color:#eeeeee;color:#000000;cursor:hand;cursor:pointer;"' + tdScript;
		  break;	  
	  }	
	  break;
	case 'regexp':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#CC0000;cell-spacing:3;"';
		  break;
		case 'th':
		  r = ' style="' + th + 'background-color:#FF0000;"' + thScript;
		  break;
		case 'td-key':
		  r = ' style="' + th + 'background-color:#FF5757;color:#000000;cursor:hand;cursor:pointer;"' + tdScript;
		  break;
		case 'td-value':
		  r = ' style="' + td + 'background-color:#fff;"';
		  break;		  
	  }	
	  break;
	case 'date':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#663399;cell-spacing:3;"';
		  break;
		case 'th':
		  r = ' style="' + th + 'background-color:#9966CC;"' + thScript;
		  break;
		case 'td-key':
		  r = ' style="' + th + 'background-color:#B266FF;color:#000000;cursor:hand;cursor:pointer;"' + tdScript;
		  break;
		case 'td-value':
		  r = ' style="' + td + 'background-color:#fff;"';
		  break;		  
	  }	
	  break;
	case 'domelement':
	  switch (use) {
		case 'table':  
		  r = ' style="' + table + 'background-color:#FFCC33;cell-spacing:3;"';
		  break;
		case 'th':
		  r = ' style="' + th + 'background-color:#FFD966;"' + thScript;
		  break;
		case 'td-key':
		  r = ' style="' + th + 'background-color:#FFF2CC;color:#000000;cursor:hand;cursor:pointer;"' + tdScript;
		  break;
		case 'td-value':
		  r = ' style="' + td + 'background-color:#fff;"';
		  break;		  
	  }	
	  break;	  
  }
  return r;
};

var _dumpIdentifyBrowser = function() {
  var agent = navigator.userAgent.toLowerCase();
  if (typeof window.opera != 'undefined') {
    return 'opera';
  } else if (typeof document.all != 'undefined') {
    if (typeof document.getElementById != 'undefined') {
      var browser = agent.replace(/.*ms(ie[\/ ][^ $]+).*/, '$1').replace(/ /, '');
      if (typeof document.uniqueID != 'undefined') {
        if (browser.indexOf('5.5') != -1) {
          return browser.replace(/(.*5\.5).*/, '$1');
        } else {
          return browser.replace(/(.*)\..*/, '$1');
        }
      } else {
        return 'ie5mac';
      }
    }
  } else if (typeof document.getElementById != 'undefined') {
    if (navigator.vendor.indexOf('Apple Computer, Inc.') != -1) {
      return 'safari';
    } else if (agent.indexOf('gecko') != -1) {
      return 'mozilla';
    }
  }
  return false;
};

var _dumpType = function (obj) {
  var t = typeof(obj);
  if (t == 'function') {
    var f = obj.toString();
    if ( ( /^\/.*\/[gi]??[gi]??$/ ).test(f)) {
      return 'regexp';
    } else if ((/^\[object.*\]$/i ).test(f)) {
      t = 'object'
    }
  }
  if (t != 'object') {
    return t;
  }
  switch (obj) {
    case null:
      return 'null';
    case window:
      return 'window';
	case document:
	  return document;
    case window.event:
      return 'event';
  }
  if (window.event && (event.type == obj.type)) {
    return 'event';
  }
  var c = obj.constructor;
  if (c != null) {
    switch(c) {
      case Array:
        t = 'array';
        break;
      case Date:
        return 'date';
      case RegExp:
        return 'regexp';
      case Object:
        t = 'object';	
      break;
      case ReferenceError:
        return 'error';
      default:
        var sc = c.toString();
        var m = sc.match(/\s*function (.*)\(/);
        if(m != null) {
          return 'object';
        }
    }
  }
  var nt = obj.nodeType;
  if (nt != null) {
    switch(nt) {
      case 1:
        if(obj.item == null) {
          return 'domelement';
        }
        break;
      case 3:
        return 'string';
    }
  }
  if (obj.toString != null) {
    var ex = obj.toString();
    var am = ex.match(/^\[object (.*)\]$/i);
    if(am != null) {
      var am = am[1];
      switch(am.toLowerCase()) {
        case 'event':
          return 'event';
        case 'nodelist':
        case 'htmlcollection':
        case 'elementarray':
          return 'array';
        case 'htmldocument':
          return 'htmldocument';
      }
    }
  }
  return t;
};

})();