/* GENERIC */
function $(v, o) {
	if (!v) return;
	return ((typeof(o) == 'object' ? o : document).getElementById(v));
}
function $2D(o) {
	return ($(o).getContext('2d'));
}
function $S(o) {
	return ($(o).style);
}
function $SS(n) {
	var o = document.styleSheets[0];
	if (o.cssRules) o = o.cssRules;
	else o = o.rules;
	return (o[n].style);
}
function $T(v, o) {
	return ((typeof(o) == 'object' ? o : $(o)).getElementsByTagName(v));
}
function abPos(o) {
	var o = (typeof(o) == 'object' ? o : $(o)),
	i = {
		X: 0,
		Y: 0
	};
	while (o != null) {
		i.X += o.offsetLeft;
		i.Y += o.offsetTop;
		o = o.offsetParent;
	};
	return (i);
}
function agent(v) {
	return (Math.max(navigator.userAgent.toLowerCase().indexOf(v), 0));
}
function cookieStab(f, v) {
	document.cookie = f + "=" + v + "; path=/";
}
function cookieGrab(f) {
	var b = f + "=",
	d = document.cookie.split(';');
	for (var i = 0; i < d.length; i++) {
		var c = d[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(b) == 0) return (c.substring(b.length, c.length));
	}
}
function getTime() {
	return ((new Date()).getTime());
}
function isset(v) {
	return ((typeof(v) == 'undefined' || v.length == 0) ? v : '');
}
function noMove() {
	log('noMove');
	if (stop) {
		stop = 0;
		document.onmouseup = function () {
			document.onmouseup = null;
			document.ontouchend = null;
			stop = 1;
		};
		document.ontouchend = function () {
			document.onmouseup = null;
			document.ontouchend = null;
			stop = 1;
		};
	}
	return false;
}
function trim(s) {
	return (s.replace(/^\s+|\s+$/g, ''));
}
function ucletter(v) {
	return (v.substr(0,1).toUpperCase());
}
function ucword(v) {
	return (v.substr(0,1).toUpperCase() + v.substr(1));
}
function XYwin(v) {
	var o = agent('msie') ? {
		'X': document.body.clientWidth,
		'Y': document.body.clientHeight
	} : {
		'X': window.innerWidth,
		'Y': window.innerHeight
	};
	return (v ? o[v] : o);
}
function XY(e, v) {
	var o = { 'X': 0, 'Y': 0 };

	if (e.touches) {
		o = { 'X': e.touches[0].clientX, 'Y': e.touches[0].clientY };
		//log('touch event: ' + o.X + ', ' + o.Y);

	} else {

		if (agent('msie')) {
			o = {
				'X': event.clientX + document.body.scrollLeft,
				'Y': event.clientY + document.body.scrollTop
			};

			//log('mouse event: ' + o.X + ', ' + o.Y);

		} else {
			o = {
			'X': e.pageX,
			'Y': e.pageY
			};

			//log('mouse event: ' + o.X + ', ' + o.Y);
		}
	}

	return (v ? o[v] : o);
}
function zindex(d) {
	d.style.zIndex = win.zindex++;
}
/* CLASSNAME */
function $C(v, o) { // GET CLASS
	var o = (typeof(o) == 'object' ? o : $(o)).getElementsByTagName("*"),
	rx = new RegExp('\\b' + v + '\\b'),
	z = [];
	for (var i = 0; i < o.length; i++) {
		if (rx.test(o[i].className)) z.push(o[i]);
	}
	return (z);
};
function C$(v, o) { // SET CLASS
	if (!$(v)) {
		return false;
	}
	var d = $(v),
	c = d.className;
	if (o['+']) {
		d.className = c + ' ' + o['+'];
	}
	if (o['-']) {
		var ob = o['-'].split(' '),
		r = {};
		for (var i in ob) {
			r[ob[i]] = 1;
		}
		var c = c.split(' '),
		z = '';
		for (var i in c) {
			i = c[i];
			if (!r[i]) z += i + ' ';
		};
		d.className = z;
	}
};
/* EVENT */
Event = {
	'add': function (o, v, fu) {
		if (typeof(v) != 'object') v = {
			el: v,
			e: v
		};
		if (o.addEventListener) o.addEventListener(v.el, fu, false);
		else if (o.attachEvent) {
			v = v.e;
			o["e" + v + fu] = fu;
			o[v + fu] = function () {
				o["e" + v + fu](window.event);
			};
			o.attachEvent("on" + v, o[v + fu]);
		}
	},
	'rm': function (o, v, fu) {
		if (typeof(v) != 'object') v = {
			el: v,
			e: v
		};
		if (o.removeEventListener) o.removeEventListener(v.el, fu, false);
		else if (o.detachEvent) {
			v = v.e;
			o.detachEvent("on" + v, o[v + fu]);
			o[v + fu] = null;
			o["e" + v + fu] = null;
		}
	}
};
/* MOVEMENT */
var aXY = {};
var bXY = {};
var oXY = {};
var cXY = {};
var mXY = '';
var moXY = {};
var mcXY = {};
var stop = 1;

core = {
	'X': function (o, m, a, x) {
		a.X = Math.max(x.X1, x.X2 ? Math.min(x.X2, a.X + x.X1) : a.X + x.X1);
		return (a);
	},
	'Y': function (o, m, a, x) {
		a.Y = Math.max(x.Y1, x.Y2 ? Math.min(x.Y2, a.Y + x.Y1) : a.Y + x.Y1);
		return (a);
	},
	'XY': function (o, m, a, x) {
		a.X = x.X2 ? Math.min(a.X + x.X1, x.X2 - x.X1) : a.X + x.X1;
		a.Y = x.Y2 ? Math.min(a.Y + x.Y1, x.Y2 - x.Y1) : a.Y + x.Y1;
		return (a);
	},
	'fu': function (o, e, C, F) {
		if (stop) {
			var oX = abPos($(o)).X,
				oY = abPos($(o)).Y,
				r = XY(e);
			function c(e, m) {
				r = XY(e);
				if (C) r = C.fu(o, m, {
					'X': r.X - oX,
					'Y': r.Y - oY
				},
				C);
				return (r);
			}
			function f(e, m) {
				log("f: " + m + ' ' + XY(e, 'X') + ', ' + XY(e, 'Y'));
				c(e, m);
				if (F) F(oXY, r, m, e);
				return (r);
			}
			function p(e, m) {
				r = XY(e);
				return ((m == 'down' ? 'P' : ' ') + (r.X - oX) + ' ' + (r.Y - oY) + (m == 'up' ? 'z' : ''));
			}
			if (isNaN(C.oX)) {
				oX = r.X - oX;
			} else {
				oX = oX - C.oX - zero($S(o).left);
			}
			if (isNaN(C.oY)) {
				oY = r.Y - oY;
			} else {
				oY = oY - C.oY - zero($S(o).top);
			}
			stop = 0;
			oXY = c(e);
			cXY = f(e, mXY = 'down');
			core.time = getTime();
			var tool = 'T' + vars.type + ' rgba(' + vars['fillCO'].join(",") + ') rgba(' + vars['strokeCO'].join(",") + ')';
			var i = '',
			t = [],
			v = {};
			if ((vars.type in gui_options.modules) && ('vars' in gui_options.modules[vars.type])) {
				v = gui_options.modules[vars.type].vars;
				for (i in v) if (i in vars) t.push(i + '(' + vars[i] + ')');
				if (t.length) tool += ' ' + t.join(' ');
			}
			document.onmousemove = function (e) {
				if(typeof(e) == 'undefined') var e = event;
				if (!stop) {
					cXY = f(e, 'move');
				}
			}

			document.ontouchmove = document.onmousemove;

			document.onmouseup = function (e) {
				if(typeof(e) == 'undefined') var e = event;
				stop = 1;
				document.onmousemove = null;
				document.onmouseup = null;
				document.ontouchmove = null;
				document.ontouchend = null;
				cXY = f(e, mXY = 'up');
			};

			document.ontouchend = document.onmouseup;

			document.onselectstart = function () {
				return false;
			}
		}
	},
	'win': function (o, m, a, x) {
		a.X = (!isNaN(x.X1) ? Math.max(a.X, x.X1) : a.X);
		a.Y = (!isNaN(x.Y1) ? Math.max(a.Y, x.Y1) : a.Y);
		var d = $S(o);
		if (m == 'down') {
			if (x.z) {
				zindex($(o));
			}
			core.win.stop = 0;
			setTimeout("if(!core.win.stop) { core.win_visible('" + o + "','hidden'); }", 500);
		} else if (m == 'move') {
			if (!core.win.stop) core.win_visible(o, 'hidden');
			core.win.stop = 1;
		}

		if (a.Y > 1000) { a.Y = 1000; }
		if (oXY.X > 1000) { oXY.X = 1000; }

		if (E.sh && oXY) { // SNAP TO XY
			if (Math.abs(a.X - oXY.X) < Math.abs(a.Y - oXY.Y)) {
				d.top = a.Y + 'px';
				d.left = oXY.X + 'px';
			} else {
				d.left = a.X + 'px';
				d.top = oXY.Y + 'px';
			}
		} else {
			d.left = a.X + 'px';
			d.top = a.Y + 'px';
		}

		log('set div: ' + d.left + ', ' + d.top);

		if (m == 'up') {
			if (win.r[o]) {
				win.cp(o, ['block', zero(d.left), zero(d.top), parseInt(win.r[o][3])]);
			}
			core.win.stop = 1;
			core.win_visible(o, 'visible');
		}
		return (a);
	},
	'win_visible': function (o, s) {
		var op = (s == 'visible') ? 1 : 0.5;
		if ($C('TMM', o)[0]) {
			$C('TMM', o)[0].style.opacity = op;
		} else if ($C('west', $C('TM', o)[0])[0]) {
			$C('west', o)[0].style.opacity = op;
			$C('east', o)[0].style.opacity = op;
		} else if ($C('TRx', o)[0]) {
			$C('TRx', o)[0].style.opacity = op;
			$C('TML', o)[0].style.opacity = op;
		} //		$S(o).overflow=s; 
	}
};
/* MATH */
N = {
	'format': function (n) {
		n = String(n);
		var x = n.split('.'),
		x1 = x[0],
		x2 = x.length > 1 ? '.' + x[1] : '',
		rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return (x1 + x2);
	},
	'between': function (n, a, z) {
		return ((n >= a && n <= z) ? true : false);
	},
	'rand': function (n) {
		return (Math.floor(Math.random() * n));
	}
};
function exp(a, z, n, d) {
	if (n <= 1) n *= 100;
	if (d == 'low') var y = Math.pow(2, n / 15.019);
	else var y = -105.78 * Math.pow(2, -1 * n / 15) + 102;
	return ((y / 100) * (z - a) + a - (.01 * z))
};
function zero(n) {
	return (!isNaN(n = parseFloat(n)) ? n : 0);
}
/* JUNK */
function deObject(o) {
	var z = [];
	for (var i in o) {
		if (typeof(o[i]) == 'object') {
			z[i] = [];
			for (var ii in o[i]) {
				if (!isNaN(o[i][ii])) z[i][ii] = Number(o[i][ii]); // NUMBERS
				else if (typeof(o[i][ii]) == 'object') z[i][ii] = String(o[i][ii]).split(','); // ARRAYS
				else if (typeof(o[i][ii]) == 'string') z[i][ii] = o[i][ii]; // STRINGS
				//- should accept objects, and null values
			}
		} else z = '';
	}
	return (z ? z : String(o).split(','));
};
function typeOf(v) {
	var z = typeof(v);
	if (z === 'object') {
		if (v) {
			if (v instanceof Array) {
				z = 'array';
			}
		} else {
			z = 'null';
		}
	}
	return (z);
};

/* DEBUG */

function log(msg) {
	var theTitle = document.getElementById('cTitle');
	theTitle.innerHTML = msg;
}
