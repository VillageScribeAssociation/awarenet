/* GUI */
gui = {
	// Options
	'options': function () {
		var r = [],
		fu = N.format;
		cF = {
			'inner_radius_spirograph': {
				type: 'X',
				val: [1, 100, 10]
			},
			'outer_radius_spirograph': {
				type: 'X',
				val: [1, 100, 10]
			},
			'diameter_spirograph': {
				type: 'X',
				val: [1, 500, 10]
			},
			'speed_spirograph': {
				type: 'X',
				val: [2, 250, 10]
			},
			'resolution_spirograph': {
				type: 'X',
				val: [1, 1000, 10]
			},
			'stroke_text': {
				type: 'X',
				val: [0, 20, 2]
			},
			'fontSize': {
				type: 'X',
				val: [16, 256, 64]
			},
			'kerning': {
				type: 'X',
				val: [0, 2, 1, 'float']
			},
			'diameter_pencil': {
				type: 'X',
				val: [1, 15, 10]
			},
			'opacity_fill': {
				type: 'X',
				val: [1, 100, 100],
				fu: infc.opacity
			},
			'lineOpacity': {
				type: 'X',
				val: [2, 100, 1]
			},
			'sides_shape': {
				type: 'X',
				val: [2, 100, 1]
			},
			'slope_shape': {
				type: 'X',
				val: [0.2, 10, 1, 'float']
			},
			'sides_marquee': {
				type: 'X',
				val: [2, 100, 1]
			},
			'slope_marquee': {
				type: 'X',
				val: [0.2, 10, 1, 'float']
			},
			'stampSize': {
				type: 'X',
				val: [1, 200, 100]
			},
			'leading': {
				type: 'X',
				val: [0, 2, 1, 'float']
			},
			'rand': {
				type: 'X',
				val: {
					'_min': [0, 100, 50],
					'_max': [0, 100, 50]
				}
			},
			'fill': {
				type: 'menu',
				val: ['Color', 'Gradient', 'Pattern']
			},
			'marquee': {
				type: 'menu',
				val: ['Ellipses', 'Polygon', 'Star', 'Burst', 'Gear']
			},
			'spirograph': {
				type: 'menu',
				val: ['Hypotrochoid', 'Epitrochoid']
			},
			'shape': {
				type: 'menu',
				val: ['Ellipses', 'Polygon', 'Star', 'Burst', 'Gear']
			},
			'crop': {
				type: 'menu',
				val: ['Display (' + fu(screen.width) + 'x' + fu(screen.height) + ')', 'Original (' + fu(canvas.W) + 'x' + fu(canvas.H) + ')', '2x3', '3x5', '4x3 (DVD)', '4x3 (Book)', '4x6 (Postcard)', '5x7 (L, 2L)', '8x10', '16x9 (HD)', '16x20', '20x30 (Poster)', 'Square']
			},
			'draw': {
				type: 'menu',
				val: ['Pencil', 'Brush', 'Calligraphy']
			},
			'lineClose': {
				type: 'check',
				val: ["lineClose", 'true', 'false']
			},
			'constrain': {
				type: 'check',
				val: ["constrain", 'true', 'false']
			},
			'preview': {
				type: 'check',
				val: ["preview", 'true', 'false']
			},
			'marqType': {
				type: 'radio',
				val: ["marquee", 'lasso', 'ellipses', 'rectangle', 'star', 'burst', 'gear']
			},
			'aspect': {
				type: 'radio',
				val: ["aspect", 'landscape', 'portrait']
			},
			'lineCap': {
				type: 'radio',
				val: ["lineCap", 'butt', 'round', 'square']
			},
			'corner': {
				type: 'radio',
				val: ["lineJoin", 'round', 'miter', 'bevel']
			}
		};
		r = ["marquee", "text", "line", "ellipses", "polygon", "star", "burst", "gear", "brush", "calligraphy", "pencil", "stamp", "fill", "eraser"];
		for (var i in r) {
			cF["movement_" + r[i]] = {
				type: "radio",
				val: ["movement_" + r[i], "anchored", "freedraw", "active"]
			};
		}
		function z(r, v) {
			for (var i in r) {
				cF[v + "_" + r[i]] = {
					type: "X",
					val: [1, 100, 100]
				};
			}
		};
		z(["ellipses", "polygon", "star", "burst", "gear", "line"], "stroke");
		z(["brush", "calligraphy", "eraser", "pencil", "stamp"], "opacity");
		z(["brush", "calligraphy", "eraser"], "diameter");
		z(["brush", "calligraphy", "eraser", "stamp"], "flow");
		z(["brush", "eraser"], "hardness");
		var j = 0,
		r = [];
		for (var i in stamp.r) {
			r[j++] = i;
		}
		cF.stamp = {
			type: "menu",
			val: r
		};
		if (!gui.menu.cur) {
			if (!vars.crop) {
				vars.crop = "Original (" + fu(canvas.W) + "x" + fu(canvas.H) + ")";
			}
			gui.menu.cur = {};
			gui.menu.key = {};
			gui.menu.key.stamp = vars.stamp;
			gui.menu.key.font = vars.font;
			gui.menu.key.draw = vars.draw;
			gui.menu.key.shape = vars.shape;
			gui.menu.key.marquee = vars.marquee;
			gui.menu.key.crop = vars.crop;
			gui.menu.key.fill = vars.fill;
			gui.menu.key.spirograph = vars.spirograph;
			gui.menu.key['PT*'] = vars['PT*'];
			gui.menu.key['GD*'] = vars['GD*'];
			gui.menu.key['CO*'] = vars['CO*'];
			for (var i in gui.menu.key) {
				gui.menu.cur[i] = String(gui.menu.key[i]);
			}
			gui.menu.klean("crop", vars.crop);
		}
	},
	//* Check Box
	'check': {
		'build': function (v, c, r) {
			return ('<div id="' + c + '_check" class="check">' + ' <span>' + v + '</span><br>' + ' <div onclick="gui.check.click(this,\'' + r[0] + '\')"' + (vars[r[0]] == 'true' ? 'class="cur"' : '') + '>' + vars[r[0]] + '</div>' + '</div>');
		},
		'click': function (o, v) {
			function z(a, b, c, d) {
				vars[v] = c;
				o.innerHTML = c;
				o.className = d;
			}
			if (o.className == 'cur') z('block', 'none', 'false', '');
			else z('none', 'block', 'true', 'cur');
			crop.click();
		}
	},
	//* Radio Button
	'radio': {
		'build': function (v, c, r) {
			var b = '';
			for (var i = 1; i < r.length; i++) b += '<div class="' + (((vars[r[0]] == r[i] && vars.type != 'crop') || (vars[r[0]] == r[i] && vars.type == 'crop' && !crop.force(vars.crop))) ? ' cur' : '') + '" onclick="gui.radio.click(this,\'' + r[0] + '\')">' + r[i] + '</div><br>';
			return ('<div' + ((crop.force(vars.crop) && vars.type == 'crop') ? ' style="opacity: 0.6"' : '') + ' class="radio" id="' + c + '_radio"><span>' + v + '</span><br>' + b + '</div>');
		},
		'click': function (o, v) {
			if (!$C("cur", o.parentNode)[0]) {
				return;
			}
			var i = o.innerHTML,
			cur = vars[v];
			$C("cur", o.parentNode)[0].className = "";
			o.className = "cur";
			vars[v] = i;
			if (v == "marquee") {
				var b = $C("Marquee_" + cur, "tools")[0];
				if (i == "lasso" || cur == "lasso") {
					marquee.reset("", 1);
				}
				b.src = "media/gui/Marquee_" + i + "_2.png";
				b.className = "Marquee_" + i;
				gui_tools.prev = b.className;
				vars.tool = "Marquee_" + i;
			}
			vars.cache(1);
			crop.click();
		}
	},
	//* Select Menu
	'menu': {
		'build': function (c, r) {
			var z = '',
			length = 0,
			o = gui.menu;
			if (typeof(r) == 'object' && !r.length) for (var i in r) {
				length++;
			} else length = r.length;
			for (var i in r) {
				var style = (i == 0) ? 'style="border-top: none;"' : ((parseInt(i) + 1) == length ? 'style="border-bottom: none;"' : '');
				if (r[i].toLowerCase() == o.cur[c].toLowerCase() || (!o.cur[c] && i == 0)) {
					className = 'class="sel"';
					var position = 'style="top:-' + (o.cellHeight * i) + 'px"';
				} else {
					className = '';
				}
				z += '<li onmousedown="gui.menu.toggle(this)" onmouseup="gui.menu.select(this)" onmouseover="gui.menu.okClose(this.parentNode.parentNode)" ' + style + ' ' + className + '>' + r[i] + '</li>';
			}
			return ('<div class="menuWrap" id="' + c + '_opt">' + ' <div class="t"><div class="l"></div><div class="r"></div><div class="c"></div></div>' + ' <div class="menuBox">' + '  <ul ' + position + '>' + '   <li class="top"><div class="l"></div><div class="r"></div><div class="c"></div></li>' + z + '   <li class="bottom"><div class="l"></div><div class="c"></div><div class="r"></div></li>' + '  </ul>' + ' </div>' + ' <div class="b"><div class="l"></div><div class="r"></div><div class="c"></div></div>' + '</div>');
		},
		'fu': {
			'z': function (c, o) {
				gui.menu.klean(c, o.innerHTML);
			},
			'crop': function (c, o) {
				gui.menu.fu.z(c, o);
				$T('div', 'constrain_check')[0].innerHTML = 'true';
				$T('div', 'constrain_check')[0].className = 'cur';
				vars.constrain = 'true';
				crop.click();
				vars.cache(1);
			},
			'fill': function (c, o) {
				gui.menu.fu.z(c, o);
				gui_palette.click("fill");
				gui_swatch.cur({
					"Gradient": "GD",
					"Color": "CO",
					"Pattern": "PT"
				}[o.innerHTML]);
			},
			'stamp': function (c, o) {
				gui.menu.fu.z(c, o);
				stamp.fileNumber = 1;
				stop = 1;
				var o = gui.Y;
				o.cur.stamp = 1;
				o.prev.stamp = null;
				o.id = 'stamp';
				o.stamp();
				o.kontrol_update('stamp');
				vars.cache(1);
			},
			'draw': function (c, o) {
				gui.menu.fu.z(c, o);
				var b = o.innerHTML;
				vars.draw = b;
				$C(vars.draw, 'tools')[0].title = b;
				gui_tools.imageCurrent(b);
			},
			'spirograph': function (c, o) {
				gui.menu.fu.z(c, o);
				var b = o.innerHTML;
				vars.type_spirograph = b;
			},
			'sw': function (c, o, v1, v2) {
				gui.menu.fu.z(c, o);
				var i = vars.id + v1;
				vars[v1] = Q[v1][o.innerHTML];
				gui_swatch.n[i] = Math.min(gui_swatch.n[i], vars[v1].length);
				vars[i] = vars[v1][gui_swatch.n[i] - 1];
				var o = gui.Y;
				gui_swatch.cur(v1);
				o.prev[v1] = null;
				o.sw(1);
				o.kontrol_update(v1);
				vars.cache(1);
				$('author_'+gui_swatch.id).innerHTML = gui_swatch.author();
			},
			'CO*': function (c, o) {
				gui.menu.fu.sw(c, o, 'CO', 'solid');
			},
			'GD*': function (c, o) {
				gui.menu.fu.sw(c, o, 'GD', 'gradient');
			},
			'PT*': function (c, o) {
				gui.menu.fu.z(c, o);
				vars.PT = Q.PT[o.innerHTML];
				var i = vars.id + 'PT',
				n = vars.PT.length - (gui_swatch.n[i] = Math.min(gui_swatch.n[i], vars.PT.length));
				vars.PT[n + 1] = new Image();
				vars.PT[n + 1].src = gui_pattern.dir + vars['PT*'] + '/' + n + '-live.jpg';
				vars[vars.id + 'PT'] = vars.PT[n + 1];
				vars[vars.id + 'PT'].onload = function () {
					vars[vars.id + 'PT'].opacity = 1;
					var o = gui.Y;
					gui_swatch.cur('PT');
					o.prev.PT = null;
					o.sw(1);
					o.kontrol_update('PT');
					vars.cache(1);
				}
				$('author_'+gui_swatch.id).innerHTML = gui_swatch.author();
			},
			'shape': function (c, o) {
				gui.menu.fu.z(c, o);
				gui_tools.imageCurrent(vars.tool = 'Shape_' + (vars.shape = vars.shape.toLowerCase()));
			},
			'marquee': function (c, o) {
				gui.menu.fu.z(c, o);
				gui_tools.imageCurrent(vars.tool = 'Marquee_' + (vars.marquee = vars.marquee.toLowerCase()));
			}
		},
		'klean': function (v, o) {
			gui.menu.cur[v] = o;
			vars[v] = o;
			var a = o,
			b = a.substr(0, a.indexOf('(') != -1 ? a.indexOf('(') - 1 : a.length);
			gui.menu.key[v] = b;
		},
		// Data
		'cellHeight': 17,
		'parent': {},
		'prev': {},
		'toggle': function (n) {
			var p = n.parentNode.parentNode,
			o = gui.menu;
			if (p.opened != true) {
				if (o.parent.opened == true) o.close();
				stop = 0;
				o.prev = n;
				o.parent = p;
				var c = p.parentNode.id.replace('_opt', '');
				win.id = p.parentNode.parentNode.parentNode.parentNode.parentNode;
				if (c == 'CO*' || c == 'GD*' || c == 'PT*') {
					win.id = win.id.parentNode.parentNode;
				}
				zindex(win.id);
				var offset = parseInt(n.parentNode.style.top);
				if (!isNaN(offset)) {
					offset = (offset / o.cellHeight) * (o.cellHeight + 2);
					var d = -1 * offset - abPos(p).Y + 3;
					if (d > 0) {
						n.parentNode.style.top = (offset + d) + 'px';
					} else n.parentNode.style.top = (offset - 3) + 'px';
				}
				p.style.overflow = 'visible';
				n.parentNode.className += 'opened';
				window.setTimeout(function () {
					p.opened = true;
				},
				100);
				window.onmousedown = o.close;
			} else o.doSelect(n, p);
		},
		'doSelect': function (n, s) {
			var p = n.parentNode.parentNode;
			stop = 1;
			var o = $T('li', p);
			p.style.overflow = 'hidden';
			for (var x = 0; x < o.length; x++) {
				o[x].className = trim(o[x].className.replace('sel', ''));
				if (o[x] == n) {
					n.className += ' sel';
					n.parentNode.style.top = -((x - 1) * gui.menu.cellHeight) + 'px';
				}
			}
			n.parentNode.className = trim(n.parentNode.className.replace('opened', ''));
			p.opened = p.okClose = false;
			window.onmousedown = null;
			var c = p.parentNode.id.replace('_opt', '');
			gui.menu.fu[c](c, n, s);
		},
		'close': function () {
			if (gui.menu.parent.opened == true) gui.menu.doSelect(gui.menu.prev)
		},
		'select': function (n, s) {
			var p = n.parentNode.parentNode;
			if (p.okClose == true) gui.menu.doSelect(n, s);
		},
		'okClose': function (p) {
			if (p.opened) p.okClose = true;
		}
	},
	
	//* Y-Scroll
	'Y': {
		'id': 'stamp',
		'cur': {
			'stamp': 1,
			'hi': 1,
			'CO': 1,
			'GD': 1,
			'PT': 1
		},
		'prev': {
			'stamp': 10,
			'hi': 10,
			'CO': 10,
			'GD': 10,
			'PT': 10
		},
		'fu': {
			'stamp': 'gui.Y.stamp',
			'hi': 'gui.Y.hi',
			'CO': 'gui.Y.sw',
			'GD': 'gui.Y.sw',
			'PT': 'gui.Y.sw'
		},
		'r': {
			'stamp': {
				'Y': 110,
				'n': function () {
					return (stamp.r[vars.stamp]);
				},
				'display': 12,
				'col': 4,
				'row': 3
			},
			'hi': {
				'Y': 135,
				'n': function () {
					var r = canvas.history_r;
					return (1 + r.z - r.a);
				},
				'display': 7,
				'col': 1,
				'row': 7
			},
			'CO': {
				'Y': 106,
				'n': function () {
					return (vars['CO'].length);
				},
				'display': 28,
				'col': 7,
				'row': 4
			},
			'GD': {
				'Y': 106,
				'n': function () {
					return (vars['GD'].length);
				},
				'display': 28,
				'col': 7,
				'row': 4
			},
			'PT': {
				'Y': 106,
				'n': function () {
					return (vars['PT'].length);
				},
				'display': 28,
				'col': 7,
				'row': 4
			}
		},
		
		// WHEEL
		'wheel': function (event) {
			var o = gui.Y,
			r = o.r[o.id];
			if (event.wheelDelta) {
				var v = event.wheelDelta / 120;
				if (window.opera) {
					v = -v;
				}
				// OPERA + IE
			} else if (event.detail) {
				var v = -event.detail / 3;
				// MOZ
			}
			var n = Math.max(2.5, (r.n() / (r.col * r.row)) / r.row * 4),
			v = (v <= 0) ? Math.floor(v + 0.2) : Math.ceil(v + 0.5); //- JUNK
			o.cord(v * n);
			eval(o.fu[o.id] + "()");
		},
		'cord': function (v) {
			var o = gui.Y,
			r = o.r[o.id];
			var Y2 = r.Y - o.height(o.id),
			n = r.n() - r.display,
			cur = o.cur[o.id];
			if (o.id == 'hi') cur -= canvas.history_r.a + 1;
			v = Math.round(Math.max(0, Math.min(n, cur - v)));
			$S(o.id + 'Slide').top = Math.round(1 / n * Y2 * v) + 'px';
			if (o.id == 'hi') v += canvas.history_r.a + 1;
			o.cur[o.id] = v;
		},
		
		// SLIDE
		'kontrol': function (i, n) {
			var o = gui.Y,
			r = o.r[i],
			H = o.height(i);
			return ('<div id="' + i + 'Kontrol" onmousedown="gui.Y.slide_fu(event,\'' + i + '\')" class="slideY" style="top: ' + (!isNaN(n) ? n : 35) + 'px; height: ' + r.Y + 'px; display:' + (r.n() <= r.display ? 'none' : 'block') + '">' + ' <span class="rT"></span><span class="rB" style="top:' + r.Y + 'px;"></span>' + ' <div id="' + i + 'Slide" class="slider" style="position: relative; height:' + o.height(i) + 'px; top:' + o.top(o, r) + 'px;">' + '  <div class="rT"></div><div class="rB" style="top:' + H + 'px"></div>' + ' </div>' + '</div>');
		},
		'kontrol_update': function (v, s) {
			var o = gui.Y,
			r = o.r[v],
			i = $(v + 'Slide');
			var H = o.height(v),
			b = i.style;
			$S(v + 'Kontrol').display = 'none';
			b.height = H + 'px';
			i.childNodes[2].style.top = H + 'px';
			b.top = o.top(o, r) + 'px';
			$S(v + 'Kontrol').display = (r.n() <= r.display) ? 'none' : 'block';
		},
		'slide': function (a, b, m) {
			var o = gui.Y,
			r = o.r[o.id],
			n = r.n() - r.display;
			$S(o.id + 'Slide').top = b.Y + 'px';
			var a = Math.round(Math.max(0, Math.min(n, (b.Y / (r.Y - o.height(o.id))) * n)));
			if (o.id == 'hi') a += canvas.history_r.a + 1;
			o.cur[o.id] = a;
			eval(o.fu[o.id] + "()");
		},
		'slide_fu': function (e, i) {
			var o = gui.Y,
			H = o.height(i);
			o.id = i;
			core.fu(i + 'Slide', e, {
				fu: core.Y,
				oX: 0,
				oY: -(H / 2),
				Y1: 0,
				Y2: o.r[i].Y - H
			},
			o.slide);
		},
		'height': function (i) {
			var o = gui.Y.r[i];
			return (Math.round(o.row / Math.ceil(o.n() / o.col) * o.Y));
		},
		'top': function (o, r) {
			var cur = o.cur[o.id],
			n = r.row,
			H = o.height(o.id);
			if (o.id == 'hi') cur -= canvas.history_r.a - 6;
			return (cur <= 1 ? 0 : Math.round((cur / ((r.n() - r.display) + n)) * (r.Y - H)));
		},
		// ACTIVE SCROLL
		'active': function (o, n, n1, n2, fu, s) {
			var z = '',
			v = '',
			r = gui.Y;
			if (s) r.prev[r.id] = null;
			while (Math.round(r.cur[r.id] - 1) % n != 0) {
				r.cur[r.id]++;
				n1++;
				n2++;
			}
			if (r.cur[r.id] != r.prev[r.id]) {
				if (fu.change) fu.change();
				for (var i = n1; i <= n2; i++) {
					var v = fu.each(i, fu.vars);
					if (v) z += v;
				}
				o.innerHTML = z;
				r.prev[r.id] = r.cur[r.id];
				return true;
			}
		},
		'sw': function (s) {
			var r = gui.Y,
			c = r.cur;
			function fu(i, r) {
				if (i <= r.length) {
					if (gui_swatch.id == 'PT') {
						vars.PT[i - 1] = new Image();
						vars.PT[i - 1].id = i;
						vars.PT[i - 1].src = gui_pattern.dir + vars['PT*'] + '/' + String(vars.PT.length - i) + '-live.jpg';
					}
					return ('<canvas id="' + vars.id + gui_swatch.id + i + '" height="16" width="16"' + (r.n == i ? 'class="cur"' : '') + ' onmousedown="gui_swatch.click(this)" title="' + String(vars.PT.length - i) + '"></canvas>');
				}
			}
			if (r.active($(gui_swatch.id), 7, c[gui_swatch.id], c[gui_swatch.id] + 27, {
				'each': fu,
				'vars': {
					'length': vars[gui_swatch.id].length,
					'n': gui_swatch.n[vars.id + gui_swatch.id]
				}
			},
			s)) {
				if (gui_swatch.id == 'PT') {
					for (var i = c[gui_swatch.id]; i <= c[gui_swatch.id] + 27; i++) if (i <= vars[gui_swatch.id].length && vars.PT[i - 1]) vars.PT[i - 1].onload = function () {
						gui_swatch.update(this.id);
					}
				} else gui_swatch.update(this.id);
			}
		},
		'stamp': function (s) {
			var r = gui.Y,
			c = r.cur;
			function fu(i, r) {
				if (i <= r.length) {
					stamp.src[i] = new Image();
					stamp.src[i].src = 'media/glyph/' + vars.stamp + '/' + (i - 1) + '-thumb.png';
					stamp.src[i].id = i;
					return ('<canvas width="34" height="34" onmousedown="if(this.id.substr(5)!=stamp.fileNumber) { stamp.current(this); co.glyph(stamp.uri(\'live\'),this.id); }" id="stamp' + i + '"' + (stamp.fileNumber == i ? ' class="cur"' : '') + '></canvas>');
				}
			}
			if($('brush_author')) {
				var stampSet = Resources.Brushes[vars.stamp];
				if(!stampSet) {
					$('brush_author').innerHTML = '';				
				} else {
					$('brush_author').innerHTML = '<i style="-moz-user-select: none; -khtml-user-select: none; user-select: none; ">by:&nbsp; <a href="'+stampSet.url+'" target="_blank">'+stampSet.name+'</a></i><div style="background: #555; height: 1px; margin: 6px 0 2px; "></div>';
				}
			}
			if (r.active($('stamp'), 4, c[r.id], c[r.id] + 11, {
				'change': function () {
					stamp.src = [];
				},
				'each': fu,
				'vars': {
					'length': stamp.r[vars.stamp]
				}
			},
			s)) {
				for (var i in stamp.src) stamp.src[i].onload = function () {
					stamp.preview(this.id);
				};
			}
		},
		'hi': function (s) {
			var r = gui.Y,
			c = r.cur;
			r.id = 'hi';
			function fu(i) {
				var r = canvas.history_r;
				i--;
				if (i <= r.z && i >= r.a) {
					var v = r.data[r.r[i]];
					if (v) {
						v = v[3] ? {
							'img': v[3],
							'type': v[4] ? v[4] : v[2],
							'n': 1
						} : {
							'img': 'Original',
							'type': 'original'
						};
						return ('<div onmousedown="gui.Y.prev[gui.Y.id]=null; canvas.history_set(canvas.history_r.n=' + i + ',1)" class="' + (r.n == i ? 'cur' : (i <= r.n ? 'keep' : 'discard')) + '">' + ' <div style="float: left">' + '  <div style="background: url(media/gui/tools/' + v.img + '.png); opacity: ' + (i <= r.n ? 1.00 : 0.65) + '"></div>' + ' </div>' + ' <div style="float: left; margin-left: 3px;">' + v.type + '</div>' + ' <span>' + (v.n ? '#' + i : '') + '</span>' + '</div>');
					}
				}
			}
			r.active($C('z', 'history')[0], 1, c[r.id], c[r.id] + 7, {
				'each': fu
			}, s);
		}
	},
	
	//* X-Slide
	'X': {
		'html': function (o, r, n, m) {
			if (r[3]) vars[o] = Math.round(Math.max(r[0], n * r[1]) * 100) / 100;
			else vars[o] = Math.round(Math.max(r[0], n * r[1]));
			$(o + 'CurV').innerHTML = vars[o];
			if (m == 'up') co.glyph();
		},
		'left': function (o, n) {
			$S(o + 'Cur').left = Math.max(0, n) + 'px';
		},
		'build': function (v, o, r) {
			var a = '',
			b = '',
			n = 0;
			if (!isNaN(r[0])) {
				r = {
					'': r
				};
				d = 'slideX';
			} else {
				d = 'slideXX';
			}
			for (var i in r) {
				n++;
				a = '<div id="' + o + i + 'Cur" class="' + i + '" style="left: ' + Math.round(vars[o + i] / (i ? cF[o].val[i] : cF[o].val)[1] * 112) + 'px"></div>' + a;
				b = '<div id="' + o + i + 'CurV" class="v">' + Math.round(vars[o + i]) + '</div>' + (b ? '<div class="u">/</div>' : '') + b;
			}
			return ('<div class="' + d + '"><span>' + v + '</span><br><div onmousedown="gui.X.' + (n > 1 ? 'xxSwitch' : 'xRun') + '(\'' + o + '\',event,this)" class="slide_div">' + (n > 1 ? '<div id="' + o + 'cCur" class="slide_center"></div>' : '') + a + '</div>' + b + '<br></div>' + (d == 'slideXX' ? '<br style="margin-top: 26px">' : ''));
		},
		'xRun': function (o, e) {
			core.fu(o + 'Cur', e, {
				fu: core.X,
				oX: -6,
				X1: 0,
				X2: 110,
				oY: 0
			},
			function (a, b, m) {
				var i = cF[o];
				if (i.fu) {
					i.fu(o, i.val, b.X, m);
				} else {
					gui.X.html(o, i.val, b.X / 110, m);
				}
				gui.X.left(o, b.X);
				if (m == 'up') vars.cache(1);
			});
		},
		'xxSwitch': function (o, e, v) {
			var n = (XY(e, 'X') - abPos(v).X - 7);
			gui.X.xxID = o;
			function z(v1, v2) {
				$S(o + v1 + 'Cur').zIndex = 1;
				$S(o + v2 + 'Cur').zIndex = 0;
				core.fu(o + v1 + 'Cur', e, {
					fu: core.X,
					oX: v1 == '_min' ? 0 : -7,
					X1: 0,
					X2: 115,
					oY: 0
				},
				function (a, b, m) {
					gui.X.xxRun(a, b, m, v1);
					if (m == 'up') vars.cache(1);
				});
			}
			var a = zero($S(o + '_maxCur').left),
			b = zero($S(o + '_minCur').left);
			if (Math.abs(n - a) < Math.abs(n - b) || (Math.abs(n - a) == Math.abs(n - b) && n <= a)) z('_max', '_min');
			else z('_min', '_max');
		},
		'range': function (v, i, n) {
			var a = (i == '_min') ? n : parseInt($S(v + '_minCur').left),
				z = (i == '_max') ? n : parseInt($S(v + '_maxCur').left);
			$S(v + 'cCur').left = (z + 7) + 'px';
			$S(v + 'cCur').width = Math.max(0, a - z - 7) + 'px';
		},
		'xxRun': function (a, b, m, i) {
			var o = gui.X,
			v = o.xxID;
			function z(i, b) {
				o.html(v + i, cF[v].val[i], b / 115, m);
				o.left(v + i, b);
				o.range(v, i, b);
			}
			if (i == '_max' && zero($S(v + '_minCur').left) < (b.X + 1)) z('_min', Math.min(115, b.X + 1));
			else if (i == '_min' && zero($S(v + '_maxCur').left) > (b.X - 1)) z('_max', Math.max(0, b.X - 1));
			z(i, b.X);
		},
		'xxID': 0
	}
};

// WINDOW

win = {

	// Mouse
	'close': function (v, s) {
		win.tog(v, {
			display: 'none',
			effect: true
		});
		if (s) {
			setTimeout("var v=$('i" + v + "'); v.className=(v.className=='cur')?'':'cur'; v.opened=false;", 190);
		}
	},
	'tab': function (o, v) {
		var d = $(o.id.substr(1));
		if (o.opened == false) {
			var i = o.id.substr(0, 2);
			i = gui_swatch.L2S[i];
			if (i) gui_swatch.cur(i);
		}
		if (o.opened == false && !d.interval) {
			o.opened = true;
			o.className = '';
			win.tog(v, {
				display: 'block',
				effect: true
			});
		} else if (!d.interval) {
			o.opened = false;
			o.className = 'cur';
			win.tog(v, {
				display: 'none',
				effect: true
			});
		}
	},
	'tog': function (v, s, r) {
		if ($(v)) {
			var o = $(v),
			d = $S(v),
			n = win.getCenter();
			if (typeof(s) != 'object') {
				s = {
					display: s,
					effect: false
				};
			}
			if (win && isNaN(win)) {
				d.left = (win.r[v][1] + n) + 'px';
				d.top = win.r[v][2] + 'px';
			}
			if ($('i' + v)) {
				var a, b;
				if (!o.start) o.start = {
					'top': 20,
					'left': $('i' + v).offsetLeft + (String($('i' + v).innerHTML).length * 2.5) + 8 - n,
					'width': 1,
					'height': 1,
					'opacity': 10
				};
				o.end = {
					'top': parseInt(win.r[v][2]),
					'left': parseInt(win.r[v][1]),
					'width': 216,
					'height': parseInt(win.r[v][3]),
					'opacity': 100
				};
				if (s.display == 'block' && s.display != d.display) {
					if (s.effect) {
						a = o.start;
						b = o.end;
					} else {
						a = o.end;
					}
				} else if (s.display == 'none' && d.display == 'block') {
					if (s.effect) {
						a = o.end;
						b = o.start;
					} else {
						a = o.start;
					}
				}
				if (b) {
					win.apply(o, a);
					win.go(o, b, .4);
				} else {
					win.apply(o, a);
				}
			} else {
				o.start = o.end = {
					'opacity': 100
				};
				win.apply(o, o.start);
			}
			win.r[v][0] = s.display;
			win.cp(1);
		}
	},
	
	// Create
	'feed': function () {
		win.r = {
			'canvas': ['block', -10, 19, ],
			'solid': ['block', 588, 49, 256],
			'gradient': ['none', 338, 382, 230],
			'pattern': ['none', 338, 152, 219],
			'swatch': ['none', 589, 306, 217],
			'tools': ['block', -102, 19, 279],
			'options': ['block', -103, 296, 200],
			'history': ['none', 138, 416, 187]
		};
		if (r = cookieGrab('windows')) win.mk(r);
		else win.mk(win.cp());
	},
	'mk': function (r) {
		var n = win.getCenter(),
		r = r.split(':');
		for (var i in r) {
			var v = r[i].split(','),
			o = '';
			if(!v[0]) continue; //- there's a missing name being passed somewhere...
			win.r[v[0]] = [v[1], parseInt(v[2]), v[3], v[4]];
			win.tog(v[0], v[1], [n + parseInt(v[2]), v[3], v[4]]);
			if (o = $('i' + v[0])) {
				o.opened = (v[1] == 'none') ? false : true;
				o.className = (v[1] == 'none') ? 'cur' : '';
			} //			TEST("'"+v[0]+"':['"+v[1]+"',"+v[2]+","+v[3]+","+v[4]+"],",1);
		}
	},
	'cp': function (o, r) {
		var z = '';
		if (r) {
			r[1] -= win.getCenter();
			win.r[o] = deObject(r);
		}
		for (var i in win.r) {
			z += i + ',' + win.r[i] + ':';
		}
		if (o) {
			cookieStab('windows', z);
		} else {
			return (z);
		}
	},
	
	// Visualizer
	'apply': function (o, r) {
		var d = o.style,
		n = win.getCenter();
		for (i in r) {
			if (i == 'opacity') {
				var b = r[i] / 100;
				d.filter = 'alpha(opacity=' + r[i] + ')';
				d.MozOpacity = b;
				d.opacity = b;
				d.KhtmlOpacity = b;
				if (r[i] <= o.start.opacity && r[i] != o.end.opacity) d.display = 'none';
				else d.display = 'block';
			} else if (i == 'left') {
				d[i] = (r[i] + n) + 'px';
			} else {
				d[i] = r[i] + 'px';
			}
			o[i] = r[i];
		}
	},
	'go': function (o, r, n) {
		stop = 0;
		zindex(o);
		if (o.interval) {
			window.clearInterval(o.interval);
		} // .01 (super slow) to 1 (instant)
		win.overflow(o, 'hidden');
		o.style.cursor = 'pointer';
		o.interval = window.setInterval(function () {
			var done = true,
			A = [],
			Z = [];
			for (i in r) {
				A[i] = Math.round((r[i] - o[i]) * n);
			}
			for (i in A) {
				Z[i] = o[i] + A[i];
				if (A[i] == 0 || (A[i] > 0 && Z[i] > r[i]) || (A[i] < 0 && Z[i] < r[i])) {
					Z[i] = r[i];
				} else {
					done = false;
				}
			}
			win.apply(o, Z);
			if (done) {
				window.clearInterval(o.interval);
				o.interval = null;
				stop = 1;
				o.style.cursor = 'move';
				win.overflow(o, 'visible');
			}
		},
		35);
	},
	'overflow': function (o, v) {
		o.style.overflow = v;
		function fu(d) {
			d.style.overflow = v;
		}
		fu($C('z', o)[0]);
		var d = $C('TML', o)[0];
		if (d) fu(d);
		var d = $C('TRx', o)[0];
		if (d) fu(d);
	},
	
	// Data
	'zindex': 3,
	'getCenter': function () {
		return (zero((XYwin('X') - 710) / 2));
	}
};

// WINDOW RESIZE

win_size = {

	// Mouse
	'core': function (e) {
		function fu(o, m, a) {
			var Y = Math.max(100, (a.Y - r.Y) + r.H),
			X = Math.max(212, (a.X - r.X) + r.W);
			var o = {
				W: X - 32,
				H: Y - 40
			};
			win_size.fu(o, r);
			if (m == 'up') {
				vars.cache(1);
			}
		}
		var r = win_size.construct({
			'X': $('canvas').offsetLeft,
			'Y': $('canvas').offsetTop,
			'W': parseInt($S('canvas').width),
			'H': parseInt($S('canvas').height)
		});
		core.fu('canvas', e, {
			fu: fu
		});
	},
	'tog': function (v) {
		var o = $S('canvas');
		win_size[v]();
		vars.winMax = (v == 'max') ? 1 : 0;
		vars.cache(1);
		win.cp('canvas', ['block', zero(o.left), zero(o.top), parseInt(win.r['canvas'][3])]);
	},
	'max': function () {
		var o = win_size;
		o.fu({
			cT: 19,
			cL: -8,
			W: XYwin('X') - 16,
			H: XYwin('Y') - 56
		},
		o.construct({}));
	},
	'min': function () {
		var o = win_size;
		o.fu({
			cT: 19,
			cL: (document.body.scrollWidth - (canvas.W + 30)) / 2,
			W: canvas.W,
			H: canvas.H
		},
		o.construct({}));
	},
	
	// Create
	'fu': function (o, c) {
		var d = $S('canvas');
		vars.winW = o.W;
		vars.winH = o.H;
		if (o.cT) {
			d.top = o.cT + 'px';
			d.left = o.cL + 'px';
		}
		d.width = (o.W + 32) + 'px';
		d.height = (o.H + 40) + 'px';
		var d = $S('cBound');
		d.height = o.H + 'px';
		d.width = o.W + 'px';
		c['ML'].height = o.H + 'px';
		c['MM'].height = o.H + 'px';
		c['MR'].height = o.H + 'px';
		c['TM'].width = o.W + 'px';
		c['MM'].width = o.W + 'px';
		c['BM'].width = o.W + 'px';
		var o = win_size.LT(),
		r = ['ctx_box', 'ctx_temp', 'ctx_marquee', 'ctx_active', 'ctx_mouse'];
		for (var i in r) {
			var d = $S(r[i]);
			d.left = o.L + 'px';
			d.top = o.T + 'px';
		};
	},
	'LT': function () {
		return ({
			L: Math.round((vars.winW - canvas.W) / 2),
			T: Math.round((vars.winH - canvas.H) / 2)
		});
	},
	'construct': function (o) {
		var d = $('canvas'),
		r = ['ML', 'MM', 'MR', 'TM', 'BM'];
		for (var i in r) {
			i = r[i];
			o[i] = $C(i, d)[0].style;
		}
		return (o);
	}
};/*

	// Canvas2D : v0.3 : 2009.01.16 
	--------------------------------
	1) Retrofits older browsers that don't support HTML 5.0
		* IECanvas, an ActiveX module which emulates <canvas>, retrofits IE: code.google.com/p/iecanvas/
		* Retrofits prototyping up to Safari 3.  IE is fixed externally in /lib/HTMLElement.htc
		* Retrofits arcTo up to (which browsers don't work???)
		* Retrofits fillText / strokeText up to Safari 4, Firefox 3.1, and Opera
		* Retrofits transform / setTransform up to Safari 4, Firefox 3.1 (+?)

	2) Parses and Implements SVG:
		* Parses <font> attributes and paths then converts to FOB for fast access.  In the process of emulating more attributes in <canvas>.
		* Parses <path> [ M, m, L, l, C, c, Q, q, Z, H, h, V, v, A, a ] and converts to VOB [ M, L, C, Q, Z ] for fast access
		* Implements Shapes: circle, ellipse, line, path, polygon, polyline, rect

	3) Adds non-standardized features:
		* Pixel: colorMatrix, colorTransform (browser must support getImageData / putImageData)
		* Path: arc, roundRect, star, polygon, burst, arrow, wedge, gear, spiral, superformula, drawVector, drawVOB
		* Typeface: drawText


	* Breaking down the libraries:
	-------------------------------
	Canvas2D.js // add, remove, fix (browser fixes), ect
	Canvas2D.Pixel.js // ColorMatrix + ConvolutionMatrix + CustomFilter mixing and applying (maybe a few other default filters, such as DisplacementMap)
	Canvas2D.Raster.js // reflect, straighten, flip, rotate, resize 
	Canvas2D.Record.js // sets up functions for recording native functions
	Canvas2D.StyleSheet.js // importing, exporting, converting, and storing:  patterns, gradients, colors, and native styles (i.e. miterLimit, font, ect)
	Canvas2D.SVG.js // parse SVG into VOB (fonts and paths)
	Canvas2D.Transform.js // geometric TransformationMatrix + Decompose
	Canvas2D.Typeface.js // text building and styles
	Canvas2D.Vector.js // shapes and broken native path functions (optionally caches in VOB)
	Canvas2D.VOB.js // v2.0 is pretty complex, ties in layers + groups w/ vector objects, so gets it's own .js

    
    * Listing functions supported by the library:
    ----------------------------------------------
	Canvas2D.add // create new <canvas> element
	Canvas2D.remove // remove specific <canvas> element 


	HTMLCanvasElement (HTML 5.0)
	-----------------------------
	attribute unsigned long width;
	attribute unsigned long height;
	
	DOMString toDataURL([Optional] in DOMString type); // converts <canvas> in a base64 string of image type
	DOMObject getContext(in DOMString contextId); // returns CanvasRenderingContext2D
	

	State Stack (HTML 5.0)
	--------------------------
	void save(); // push state on state stack
	void restore(); // pop state stack and restore state
	
	void getPath(); // returns current path *


	Style API
	-----------------------
	Composition (HTML 5.0)
	-----------------------
	attribute float globalAlpha; // (default 1.0)
	attribute DOMString globalCompositeOperation; // (default source-over)
	
	Colors & Styles (HTML 5.0)
	--------------------------
	attribute any strokeStyle; // (default black)
	attribute any fillStyle; // (default black)
	
	Line Caps & Joins (HTML 5.0)
	----------------------------
	attribute float lineWidth; // (default 1)
	attribute DOMString lineCap; // "butt", "round", "square" (default "butt")
	attribute DOMString lineJoin; // "round", "bevel", "miter" (default "miter")
	attribute float miterLimit; // (default 10)
	
	Shadows: supported in Firefox 3.1, Safari 3, Opera 10
	-----------------------------------------
	attribute float shadowOffsetX; // (default 0)
	attribute float shadowOffsetY; // (default 0)
	attribute float shadowBlur; // (default 0)
	attribute DOMString shadowColor; // (default transparent black)

	CanvasGradient (HTML 5.0)
	-------------------------
	createLinearGradient(float x0, float y0, float x1, float y1);
	createRadialGradient(float x0, float y0, float r0, float x1, float y1, float r1);
	
	interface { void addColorStop(float offset, DOMString color); };
	

	Patterns (HTML 5.0)
	-------------------
	CanvasPattern createPattern(HTMLImageElement image, DOMString repetition);
	CanvasPattern createPattern(HTMLCanvasElement image, DOMString repetition);
	
	
	Transformation API (HTML 5.0)
	------------------------------
	void scale(float x, float y);
	void rotate(float angle);
	void translate(float x, float y);
	void transform(float m11, float m12, float m21, float m22, float dx, float dy);
	void setTransform(float m11, float m12, float m21, float m22, float dx, float dy);

	
	Pixel API (HTML 5.0)
	------------------------
	ImageData createImageData(float sw, float sh);
	ImageData getImageData(float sx, float sy, float sw, float sh); // Opera 9.5+, Firefox 2.0+, Safari 4.0+

	void putImageData(ImageData imagedata, float dx, float dy);
	void putImageData(ImageData imagedata, float dx, float dy, float dirtyX, float dirtyY, float dirtyWidth, float dirtyHeight);

	Addon: Filters (seperate file contains filters, cause this is a lot of information)

	void getPixel();
	void setPixel();
	
	
	Raster API (HTML 5.0)
	------------------------
	void drawImage(HTMLImageElement image, float dx, float dy, [Optional] float dw, float dh);
	void drawImage(HTMLImageElement image, float sx, float sy, float sw, float sh, float dx, float dy, float dw, float dh);
	void drawImage(HTMLCanvasElement image, float dx, float dy, [Optional] float dw, float dh);
	void drawImage(HTMLCanvasElement image, float sx, float sy, float sw, float sh, float dx, float dy, float dw, float dh);
	

	Vector API (HTML 5.0)
	----------------------
	boolean isPointInPath(float x, float y);

	void beginPath();
	void closePath();
	void moveTo(float x, float y);
	void lineTo(float x, float y);
	void arcTo(float x1, float y1, float x2, float y2, float radius); // don't punish for not having arcTo native
	void bezierCurveTo(float cp1x, float cp1y, float cp2x, float cp2y, float x, float y);
	void quadraticCurveTo(float cpx, float cpy, float x, float y);
	void arc(float x, float y, float radius, float startAngle, float endAngle, boolean anticlockwise); // openlaszlo.org/jira/browse/LPP-3491
	void rect(float x, float y, float w, float h);
	void clearRect(float x, float y, float w, float h);
	void fillRect(float x, float y, float w, float h);
	void strokeRect(float x, float y, float w, float h);
	void fill();
	void stroke();
	void clip();
	
	// Addon: SVG Shapes @ w3.org/TR/SVG11/shapes.html

	void circle(float cx, float cy, float r) // defines a circle based on a center point and a radius
	void ellipse(float cx, float cy, float rx, float ry) // defines an ellipse based on a center point and two radii
	void line(float x1, float y1, float x2, float y2) // defines a line segment that starts at one point and ends at another
	void path(string d) // SVG instructions, contains the moveto, line, cubic, quadratic, arc, and closepath: "M5,5 C5,45 45,45 45,5"

	// Addon: Path Shapes

	void roundRect(float x, float y, float width, float height, float rad) // circular rounded-rectangle
	void roundRect(float x, float y, float width, float height, float rx, float ry) // elliptical rounded-rectangle (two radii)
	void wedge(float x, float y, float radius, float startAngle, float endAngle, boolean anticlockwise); // en.wikipedia.org/wiki/Wedge_%28geometry%29
	void polygon(x, y, radius, sides) // en.wikipedia.org/wiki/Regular_polygon
	void gear(float x, float y, float radius, int sides, float slope); // en.wikipedia.org/wiki/Gear
	void star(float x, float y, float radius, int sides, float slope);
	void burst(float x, float y, float radius, int sides, float slope);
	void spiral(x, y, radius, sides, coils) // en.wikipedia.org/wiki/Archimedean_spiral
	void superformula(x, y, radius, points, m, n1, n2, n3) // en.wikipedia.org/wiki/Superformula


	Typeface API (HTML 5.0)  -- Firefox 3.1+ and Safari 4.0+
	------------------------
	attribute DOMString font; // (default 10px sans-serif)
	attribute DOMString textAlign; // "start", "end", "left", "right", "center" (default: "start")
	attribute DOMString textBaseline; // "top", "hanging", "middle", "alphabetic", "ideographic", "bottom" (default: "alphabetic")

	void fillText(DOMString text, float x, float y, [Optional] float maxWidth);
	void strokeText(DOMString text, float x, float y, [Optional] float maxWidth);
	void measureText(DOMString text); // returns { width: 100 }
	
	// Addon: Mozilla
	
	void drawText(DOMString text, float x, float y, [Optional] float maxWidth); // rhino-canvas.sourceforge.net/www/drawstring.html

*/
if (typeof Canvas2D == 'undefined') {
	var Canvas2D = {};
}
(function () {
var surface = {}; // canvas array
Canvas2D = mixin(Canvas2D, { // Global functions
	add: function (attr, height, id, style) { // add <canvas> element
		if (typeof attr == "number") { // accepts Canvas2D.add(width, height, id, style)
			attr = {
				width: attr,
				height: height,
				id: id,
				style: style
			};
		}
		if (attr) { // check whether surface exists
			if (typeof attr == "string") {
				attr = { // convert to object
					id: attr
				};
			}
			if (attr && attr.id && surface[attr.id]) {
				return surface[attr.id];
			}
		}
		var d = document.createElement('canvas'),
		ctx = d.getContext('2d');
		if (typeof attr == "object") {
			for (var key in attr) {
				switch (key) { // type of attribute
					case "style":
						if (typeof attr[key] == "object") {
							for (var type in attr[key]) // style object
							d.style[type] = attr[key][type];
							break;
						}
						else { // string
							d.setAttribute(key, attr[key]);
							break;
						}
					default:
						//-
						d[key] = attr[key];
						break;
				}
			}
		}
		document.body.appendChild(d);
		if (attr.id) { // cache surface
			surface[attr.id] = ctx;
		}
		return ctx;
	},
	remove: function (id) { // remove <canvas> element
		if (!surface[id]) {
			return;
		}
		delete surface[id]; // delete reference
		document.body.removeChild($("#" + id)); // remove DOM
	}
});
})();(function() {

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

})();/* GENERIC */
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
	if (stop) {
		stop = 0;
		document.onmouseup = function () {
			document.onmouseup = '';
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
	var o = agent('msie') ? {
		'X': event.clientX + document.body.scrollLeft,
		'Y': event.clientY + document.body.scrollTop
	} : {
		'X': e.pageX,
		'Y': e.pageY
	};
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
var aXY = {},
bXY = {},
oXY = {},
cXY = {},
mXY = '',
moXY = {},
mcXY = {},
stop = 1;
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
			document.onmouseup = function (e) {
				if(typeof(e) == 'undefined') var e = event;
				stop = 1;
				document.onmousemove = '';
				document.onmouseup = '';
				cXY = f(e, mXY = 'up');
			};
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
/* SETTINGS */

var co = { },
	vars = { };

(function() {

vars = {
	'id': 'fill',
	'draw': 'Brush',
	'font': 'Arial',
	'gradient': 'fixed',
	'marquee': 'polygon',
	'shape': 'ellipses',
	'stamp': 'Default',
// Splatter',
	'PT*': 'Squidfingers',
	'GD*': 'Web v2.0',
	'CO*': 'Oxygen',
	'constrain': 'false',
	'fillPT': new Image(),
	'fill': 'solid',
	'lineCap': 'round',
	'lineClose': 'false',
	'lineJoin': 'miter',
	'strokePT': new Image(),
	'stroke': 'solid',
	'type': 'Brush',
	'zoom': 100
};

vars.updateTool = function () {
	var b = vars.type;
	vars.tool = ucword(b) + (b == 'shape' || b == 'marquee' ? '_' + vars[b] : '');
};

(vars.cache = function (s) {
	var c = cookieGrab('settings');
	if (!vars.tool) {
		vars.updateTool();
	}
	if (c && !s) {
		var z = c.split(','); // OPEN
		for (var i in z) {
			var v = z[i].split(':');
			if (!isNaN(parseInt(v[1]))) v[1] = parseInt(v[1]);
			if (v[0] != 'zoom') vars[v[0]] = v[1];
		}
	} else {
		var z = '',
		r = {
			'object': 1,
			'function': 1
		}; // SAVE
		for (var i in vars) {
			if (!r[typeof(vars[i])]) {
				z += i + ':' + vars[i] + ',';
			}
		}
		z = z.substr(0, z.length - 1);
		cookieStab('settings', z);
	}
})();

/* SHAPES */

co.polygon=function(a,b,c,s) {
	var T=!isNaN(s)?'marquee':'shape', o=co.constrain(a,b,s), scale=co.scale(a,b,s);

	var rad=Math.sqrt(Math.pow(a.X-o.X,2)+Math.pow(a.Y-o.Y,2))/2, sides=vars['sides_'+T];

	var x=o.X-((o.X-a.X)/2), y=o.Y-((o.Y-a.Y)/2), step=(Math.PI*2)/sides;

	var angle=E.ctrl?Math.atan2(o.X-a.X,o.Y-a.Y):(Math.PI/180*45), j=0;

	var check=(isNaN(s) && mXY=='up' && Math.abs(a.X-b.X)>0 && Math.abs(a.Y-b.Y)>0);
	
	function fu_x() { 
		return x+Math.cos(angle+(step*n))*rad*scale.X;
	}
	function fu_y() { return(y-Math.sin(angle+(step*n))*rad*scale.Y); }

	var n=0; 
	c.moveTo(fu_x(), fu_y());

	for(var n=1; n<=sides; n++) {

		c.lineTo(fu_x(), fu_y());
	
	}
	
	c.closePath();

};

co.star=function(a,b,c,s) { var T=!isNaN(s)?'marquee':'shape', o=co.constrain(a,b,s), scale=co.scale(a,b,s);
	var rad=Math.sqrt(Math.pow(a.X-o.X,2)+Math.pow(a.Y-o.Y,2))/2, rad2=rad/vars['slope_'+T], sides=vars['sides_'+T];

	var x=o.X-((o.X-a.X)/2), y=o.Y-((o.Y-a.Y)/2), step=(Math.PI*2)/sides, halfStep=step/2;

	var r=(Math.PI/180), angle=E.ctrl?Math.atan2(o.X-a.X,o.Y-a.Y):r*45, j=0;

	var check=(isNaN(s) && mXY=='up' && Math.abs(a.X-b.X)>0 && Math.abs(a.Y-b.Y)>0);
	
	function fu1(v) { return((v=='X')?(x+Math.cos(angle+(step*n)-halfStep)*rad*scale.X):(y-Math.sin(angle+(step*n)-halfStep)*rad*scale.Y)); }
	function fu2(v) { return((v=='X')?(x+Math.cos(angle+(step*n))*rad2*scale.X):(y-Math.sin(angle+(step*n))*rad2*scale.Y)); }

	var n=0; c.moveTo(fu2('X'), fu2('Y'));

	for(var n=1; n<=sides; n++) {
	
		c.lineTo(fu1('X'), fu1('Y')); c.lineTo(fu2('X'), fu2('Y'));

	}
	
	c.closePath();

};

co.burst=function(a,b,c,s) { var T=!isNaN(s)?'marquee':'shape', o=co.constrain(a,b,s), scale=co.scale(a,b,s);

	var rad=Math.sqrt(Math.pow(a.X-o.X,2)+Math.pow(a.Y-o.Y,2))/2, rad2=rad/vars['slope_'+T], sides=vars['sides_'+T];

	var x=o.X-((o.X-a.X)/2), y=o.Y-((o.Y-a.Y)/2), step=(Math.PI*2)/sides, halfStep=step/2, qtrStep=step/4;

	var angle=E.ctrl?Math.atan2(o.X-a.X,o.Y-a.Y):(Math.PI/180*45), j=0;

	var check=(isNaN(s) && mXY=='up' && Math.abs(a.X-b.X)>0 && Math.abs(a.Y-b.Y)>0);

	c.moveTo(x+(Math.cos(angle)*rad)*scale.X, y-(Math.sin(angle)*rad)*scale.Y);

	for(var n=1; n<=sides; n++) {

		c.quadraticCurveTo(x+Math.cos(angle+(step*n)-(qtrStep*3))*(rad2/Math.cos(qtrStep))*scale.X, y-Math.sin(angle+(step*n)-(qtrStep*3))*(rad2/Math.cos(qtrStep))*scale.Y, x+Math.cos(angle+(step*n)-halfStep)*rad2*scale.X, y-Math.sin(angle+(step*n)-halfStep)*rad2*scale.Y);
		c.quadraticCurveTo(x+Math.cos(angle+(step*n)-qtrStep)*(rad2/Math.cos(qtrStep))*scale.X, y-Math.sin(angle+(step*n)-qtrStep)*(rad2/Math.cos(qtrStep))*scale.Y, x+Math.cos(angle+(step*n))*rad*scale.X, y-Math.sin(angle+(step*n))*rad*scale.Y);
	}

	c.closePath();

};

co.gear=function(a,b,c,s) { var T=!isNaN(s)?'marquee':'shape', o=co.constrain(a,b,s), scale=co.scale(a,b,s);

	var rad=Math.sqrt(Math.pow(a.X-o.X,2)+Math.pow(a.Y-o.Y,2))/2, rad2=rad/vars['slope_'+T], sides=vars['sides_'+T];

 	var x=o.X-((o.X-a.X)/2), y=o.Y-((o.Y-a.Y)/2), step=(Math.PI*2)/sides, halfStep=step/2, qtrStep=step/4;

	var angle=E.ctrl?Math.atan2(o.X-a.X,o.Y-a.Y):(Math.PI/180*45), j=0;

	var check=(isNaN(s) && mXY=='up' && Math.abs(a.X-b.X)>0 && Math.abs(a.Y-b.Y)>0);
	
	c.moveTo(x+(Math.cos(angle)*rad2)*scale.X, y-(Math.sin(angle)*rad2)*scale.Y);

	for(var n=1; n<=sides; n++) {
	
		c.lineTo(x+Math.cos(angle+(step*n)-(qtrStep*3))*rad*scale.X, y-Math.sin(angle+(step*n)-(qtrStep*3))*rad*scale.Y);
		c.lineTo(x+Math.cos(angle+(step*n)-(qtrStep*2))*rad*scale.X, y-Math.sin(angle+(step*n)-(qtrStep*2))*rad*scale.Y);
		c.lineTo(x+Math.cos(angle+(step*n)-qtrStep)*rad2*scale.X, y-Math.sin(angle+(step*n)-qtrStep)*rad2*scale.Y);
		c.lineTo(x+Math.cos(angle+(step*n))*rad2*scale.X, y-Math.sin(angle+(step*n))*rad2*scale.Y);

	}
	
	c.closePath();

};

co.ellipses=function(a,b,c,s) { 
	var T=!isNaN(s)?'marquee':'shape', o=co.constrain(a,b,s), scale=co.scale(a,b,s);
	var rad=Math.sqrt(Math.pow(a.X-o.X,2)+Math.pow(a.Y-o.Y,2))/2, sides=150;
	var x=o.X-((o.X-a.X)/2), y=o.Y-((o.Y-a.Y)/2), step=(Math.PI*2)/sides;
	var angle=E.ctrl?Math.atan2(o.X-a.X,o.Y-a.Y):(Math.PI/180*45), j=0;
	var check=(isNaN(s) && mXY=='up' && Math.abs(a.X-b.X)>0 && Math.abs(a.Y-b.Y)>0);
	function fu_x() { 
		return x+Math.cos(angle+(step*n))*rad*scale.X;
	}
	function fu_y() { return(y-Math.sin(angle+(step*n))*rad*scale.Y); }
	var n=0; 
	c.moveTo(fu_x(), fu_y());
	for(var n=1; n<=sides; n++) {
		c.lineTo(fu_x(), fu_y());
	}
	c.closePath();
};

co.circle = function (a, b, c) {
	var d = {
		'X': Math.max(a.X, b.X) - Math.abs(a.X - b.X) / 2,
		'Y': Math.max(a.Y, b.Y) - Math.abs(a.Y - b.Y) / 2
	};
	var r = Math.sqrt(Math.pow(a.X - b.X, 2) + Math.pow(a.Y - b.Y, 2)) / 2;
	c.arc(d.X, d.Y, r, 0, Math.PI * 2, true);
	c.closePath();
};

co.rectangle = function (a, b, c, s) {
	var o = co.constrain(a, b, s);
	if ((a.X - o.X <= 0) != (a.Y - o.Y <= 0)) {
		c.moveTo(a.X, a.Y);
		c.lineTo(o.X, a.Y);
		c.lineTo(o.X, o.Y);
		c.lineTo(a.X, o.Y);
	} else {
		c.moveTo(a.X, a.Y);
		c.lineTo(a.X, o.Y);
		c.lineTo(o.X, o.Y);
		c.lineTo(o.X, a.Y);
	}
	c.closePath();
};

/* CACHE */

co.brush = function () {
	if (vars.type == 'brush' || vars.type == 'eraser' || vars.type == 'pencil') {
		var c = $2D('ctx_brush'),
			D = vars['diameter_' + vars.type],
			D2 = D * 2;
		$('ctx_brush').width = D2;
		$('ctx_brush').height = D2;
		co.del(c);
		c.globalCompositeOperation = 'source-over';
		var r = c.createRadialGradient(D, D, (vars.type == 'pencil') ? D - 1 : vars['hardness_' + vars.type] / 100 * D - 1, D, D, D);
		r.addColorStop(0, 'rgba(0,0,0,' + (vars['opacity_' + vars.type] / 100) + ')');
		r.addColorStop(0.95, 'rgba(0,0,0,0)'); // prevent aggregation of semi-opaque pixels
		r.addColorStop(1, 'rgba(0,0,0,0)');
		c.fillStyle = r;
		c.fillRect(0, 0, D2, D2);
		c.globalCompositeOperation = 'source-in';
		c.rect(0, 0, D2, D2);
		co.style[vars.fill]({
			'X': 0,
			'Y': 0
		},
		{
			'X': D2,
			'Y': D2
		},
		c, 'fill');
	}
};

co.glyph = function (v, o) {
	co.brush();
	if(!v) {
		co.stamp();
		return;
	}
	co.stamp.image.src = v;
	if ($(o)) {
		if ($(stamp.o)) {
			$(stamp.o).className = '';
		}
		co.del(o);
		C$(o, {
			'+': 'load'
		});
		stamp.o = o;
	}
	co.stamp.image.onload = function () {
		var o = co.stamp.image;
		stamp.loaded = true;
		var id = stamp.fileNumber;
		C$('stamp' + id, {
			'-': 'load'
		});
		stamp.preview(id, 'move');
		co.stamp.r = {
			W: o.width,
			H: o.height
		};
		co.stamp();
	}
};

co.stamp = function () {
	if ((vars.type == 'calligraphy' || vars.type == 'stamp') && co.stamp.image.src) {
		var c = $2D('ctx_stamp'),
			i = co.stamp.image, 
			n = vars.type == 'stamp' ? vars.rand_min / 100 : 1,
			o = co.stamp.r = {
				W: Math.round(i.width * n),
				H: Math.round(i.height * n)
			};
		$('ctx_stamp').width = o.W;
		$('ctx_stamp').height = o.H;
		c.save();
		c.clearRect(0, 0, canvas.W, canvas.H);
		c.globalAlpha = vars['opacity_' + vars.type] / 100;
		c.globalCompositeOperation = 'source-over';
		c.drawImage(i, 0, 0, o.W, o.H);
		c.globalCompositeOperation = 'source-in';
		c.rect(0,0,o.W,o.H);
		style(c, "fillStyle", "fill", { X: 0, Y: 0 }, { X: o.W, Y: o.H });
		c.fill();
		c.restore();
	}
};
co.stamp.image=new Image();
co.stamp.r={};

/* EXTENDED */

co.core = function (e, fu) {
	var d = win_size.LT();
	core.fu('cBound', e, {
		fu: core.XY,
		X1: 0,
		Y1: 0,
		oX: -1 - d.L,
		oY: -1 - d.T
	},
	fu);
};

co.copy = function (a, b) {
	$2D(b).drawImage($(a), 0, 0, canvas.W, canvas.H);
};

co.del = function (c) {
	if (c.length) c = $2D(c);
	c.clearRect(0, 0, canvas.W, canvas.H);
};

co.path = function (c, r) {
	c.moveTo(r[0].X, r[0].Y);
	for (var i in r) {
		c.lineTo(r[i].X, r[i].Y);
	}
};

/* TRANSFORMATION */

co.constrain = function (a, b, s) {
	if (((E.sh || !isNaN(s)) && s == 1) || (E.sh && isNaN(s)) && !E.ctrl) {
		var X = (b.X - a.X) ? (b.X - a.X) : 1,
			Y = (b.Y - a.Y) ? (b.Y - a.Y) : 1,
		Z = {
			X: Math.abs(X),
			Y: Math.abs(Y)
		};
		if (Z.X < Z.Y) return ({
			X: a.X + (X / Z.X * Z.Y),
			Y: b.Y
		});
		else return ({
			Y: a.Y + (Y / Z.Y * Z.X),
			X: b.X
		});
	} else {
		return (b);
	}
};

co.scale = function (a, b, s) {
	if ((((E.sh || !isNaN(s)) && s == 1) || (E.sh && isNaN(s)))) {
		return ({
			X: 1,
			Y: 1
		});
	} else if (Math.abs(a.X - b.X) > Math.abs(a.Y - b.Y)) {
		return ({
			X: 1,
			Y: zero(Math.abs(a.Y - b.Y) / Math.abs(a.X - b.X))
		});
	} else {
		return ({
			X: zero(Math.abs(a.X - b.X) / Math.abs(a.Y - b.Y)),
			Y: 1
		});
	}
};

/* STYLE */

co.style = { };
co.style.fill = function (c, s) {
	c.fillStyle = s;
	c.fill();
};
co.style.stroke = function (c, s) {
	c.strokeStyle = s;
	c.stroke();
};
co.style.solid = function (a, b, c, v, s) {
	co.style[v](c, 'rgba(' + vars[((s && isNaN(s)) ? s : v) + 'CO'].join(",") + ')');
};
co.style.pattern = function (a, b, c, v, s) {
	co.style[v](c, vars[((s && isNaN(s)) ? s : v)+'PT~']);
};
co.style.gradient = {};
co.style.gradient = function (a, b, c, v, s) {
	co.style.gradient[vars.gradient](a, b, c, v, s);
};
co.style.gradient.fixed = function (a, b, c, v, s) {
	co.gradient(a, b, c, vars[((s && isNaN(s)) ? s : v) + 'GD'], v, s);
};
co.style.gradient.relative = function (a, b, c, v, s) {
	co.gradient(cXY, b, c, vars[((s && isNaN(s)) ? s : v) + 'GD'], v, s);
};
co.style.gradient.absolute = function (a, b, c, v, s) {
	co.gradient({
		X: 0,
		Y: 0
	},
	{
		X: canvas.W,
		Y: canvas.H
	},
	c, vars[((s && isNaN(s)) ? s : v) + 'GD'], v, s);
};
co.gradient = function (a, b, c, r, v, s) {
	var g;
	if (!s) { // ROTATE
		var W = Math.sqrt(Math.pow(b.X - a.X, 2)),
		W2 = W / 2,
		rad = gui_gradient.rotate,
		mX = Math.min(b.X, a.X),
		mY = Math.min(b.Y, a.Y);
		var s1 = Math.abs((Math.sin(rad) * W2) + W2),
		s2 = Math.abs((Math.cos(rad) * W2) - W2);
		var x1 = Math.abs((Math.sin(rad + Math.PI) * W2) + W2),
		y1 = Math.abs((Math.cos(rad + Math.PI) * W2) - W2);
		g = c.createLinearGradient(s1 + mX, s2 + mY, x1 + mX, y1 + mY);
	} else g = c.createLinearGradient(a.X, a.Y, b.X, b.Y);
	for (var i in r) {
		if (!r[i][2]) {
			g.addColorStop(r[i][0], 'rgba(' + r[i][1].join(",") + ')');
		}
	}
	co.style[v](c, g);
};

})();
/*
	GUI Interface / 0.1 / 
	-------------------------
	gui_tools
	gui_palette
	gui_color
	gui_gradient
	gui_pattern
	gui_swatch

*/

// TOOLS window

gui_tools = {
	imagePos: {}, // y-pos of backgroundPosition (all tools are in media/GUI/Tools.png
	imageMap: function () { // create image mapping
		var elements = $T('div', $C('tools', 'tools')[0]),
			icons = [ 
				'Brush',
				'Calligraphy',
				'Crop',
				'Eraser',
				'Fill',
				'Marquee_burst',
				'Marquee_ellipses',
				'Marquee_gear',
				'Marquee_lasso',
				'Marquee_polygon',
				'Marquee_rectangle',
				'Marquee_star',
				'Original',
				'Pencil',
				'Picker',
				'Select',
				'Shape_burst',
				'Shape_ellipses',
				'Shape_gear',
				'Shape_polygon',
				'Shape_rectangle',
				'Shape_star',
				'Stamp',
				'Text',
				'Zoom',
				'Spirograph'
			];
		for (var n in icons) {
			gui_tools.imagePos[icons[n]] = n * 26 - 5;
		}
		for (var image in elements) {
			image = elements[image];
			if (typeof(image) == 'object') {
				var o = $T('img', image);
				if (o[0]) {
					o = (o[1] && o[1].className != 'plus') ? o[1] : o[0];
					var o = o.className.split(' ')[0];
					var u = o.split('_')[0];
					if (u == 'Marquee' || u == 'Shape') {
						o = u + '_' + vars[u.toLowerCase()];
					} else if (u == 'Brush') {
						o = ucword(vars['draw']);
					}
					image.style.backgroundImage = 'url(media/gui/tools/Tools.png)';
					image.style.backgroundPosition = '5px ' + (-gui_tools.imagePos[o]) + 'px';
					image.title = o.split('_')[0];
					image.onmousedown = function (event) {
						event.preventDefault();
					};
				}
			}
		}
	},
	imageSize: function (v, i, end) { // image animation (switching tools)
		var o = $C(v, 'tools')[0],
		d = o.style;
		d.width = (15 + i) + 'px';
		d.height = (15 + i) + 'px';
		d.top = (-(i / 2)) + 'px';
		d.left = (-(i / 2)) + 'px';
		if (i == end) {
			var d = o.parentNode.style;
			d.backgroundPosition = '5px ' + (-gui_tools.imagePos[v]) + 'px';
			if (i == 25) {
				o.style.display = "block";
				o.src = 'media/gui/tools/' + v + '_2.png';
				d.backgroundImage = '';
			} else if (i == 0) {
				o.src = '';
				o.style.display = "none";
				d.backgroundImage = 'url(media/gui/tools/Tools.png)';
			}
		} else if ((i == 0 && end == 25) || (i == 25 && end == 0)) {
			var o = $T('img', o.parentNode);
			if (o[1]) {
				o[0].style.display = (i == 0) ? 'none' : 'block';
			}
		}
	},
	imageCurrent: function (v) {
		function getRoot(v) {
			return (v.indexOf('_') != -1 ? v.substr(0, v.indexOf('_')) : v).toLowerCase();
		};
		var prev = gui_tools.prev,
			p = getRoot(prev ? prev : vars.type),
			c = getRoot(v);
		vars.type = c;
		vars.typeImg = v;
		if (prev != v) { // tool has switched
			function zoom(o, n) {
				setTimeout("gui_tools.imageSize('" + o + "'," + i + "," + n + ")", (timer++) * 4);
			};
			if (c == 'marquee' || c == 'crop') {
				marquee.reset();
			}
			if (p == 'crop') {
				crop.reset();
			}
			if (prev) {
				var timer = 0,
				img1 = new Image();
				img1.src = 'media/gui/tools/' + prev + '.png';
				for (var i = 25; i >= 0; i--) {
					zoom(prev, 0);
				}
			}
			var timer = 0,
			img2 = new Image();
			img2.src = 'media/gui/tools/' + v + '_2.png';
			for (var i = 0; i <= 25; i++) {
				zoom(v, 25);
			}
			gui_tools.prev = v;
			gui_options.forge(vars.type); // build "OPTIONS" window
			vars.updateTool(); // 
			vars.cache(1);
		}
	}
};

// "OPTIONS" window

gui_options = {
	modulesObject: function () {
		gui_options.modules = {
			'marquee': {
				build: ['sides_marquee', 'slope_marquee'],
				vars: {
					'movement_marquee': 'anchored',
					'slope_marquee': 2,
					'sides_marquee': 7
				},
				head: 'marquee',
				size: {
					'ellipses': 15,
					'polygon': 40,
					'': 85
				}
			},
			'crop': {
				build: ['constrain', 'aspect'],
				vars: {
					'constrain': 'false',
					'aspect': 'landscape'
				},
				head: 'crop',
				size: 58
			},
			'text': {
				build: ['fontSize', 'stroke_text'],//'kerning', 'leading', 
				vars: {
					'movement_text': 'active',
					'fontSize': 90,
					'kerning': 1,
					'leading': 1.2,
					'stroke_text': 3
				},
				size: 140
			},
			'shape': {
				build: ['movement_' + vars.shape, 'corner', 'sides_shape', 'slope_shape', 'stroke_' + vars.shape],
				vars: {
					'movement_shape': 'anchored',
					'stroke_shape': 7,
					'gradient': 'fixed',
					'slope_shape': 2,
					'sides_shape': 7
				},
				head: 'shape',
				size: {
					'ellipses': 130,
					'polygon': 170,
					'': 210
				}
			},
			'pencil': {
				build: ['diameter_pencil', 'opacity_pencil'],
				vars: {
					'movement_pencil': 'freedraw',
					'lineJoin': 'round',
					'gradient': 'absolute',
					'diameter_pencil': 10,
					'flow_pencil': 100,
					'opacity_pencil': 80
				},
				head: 'draw',
				size: 85,
				glyph: 1
			},
			'brush': {
				build: ['diameter_brush', 'hardness_brush', 'flow_brush', 'opacity_brush'],
				vars: {
					'movement_brush': 'freedraw',
					'diameter_brush': 25,
					'hardness_brush': 60,
					'flow_brush': 92,
					'opacity_brush': 70
				},
				head: 'draw',
				size: 165,
				glyph: 1
			},
			'calligraphy': {
				build: ['diameter_calligraphy', 'opacity_calligraphy'],
				vars: {
					'movement_calligraphy': 'freedraw',
					'lineJoin': 'round',
					'gradient': 'absolute',
					'diameter_calligraphy': 50,
					'flow_calligraphy': 100,
					'opacity_calligraphy': 90
				},
				head: 'draw',
				size: 80,
				glyph: 1
			},
			'spirograph': {
				build: ['inner_radius_spirograph', 'outer_radius_spirograph', 'diameter_spirograph','speed_spirograph','resolution_spirograph'],
				vars: {
					'inner_radius_spirograph': 29,
					'outer_radius_spirograph': 79,
					'diameter_spirograph': 30,
					'speed_spirograph': 100,
					'resolution_spirograph': 364,
					'type_spirograph': 'Hypotrochoid'
				},
				head: 'spirograph',
				size: 205,
				glyph: 1
			},
			'stamp': {
				build: ['rand', 'flow_stamp', 'opacity_stamp'],
				vars: {
					'movement_stamp': 'freedraw',
					'flow_stamp': 10,
					'opacity_stamp': 100,
					'rand_min': 70,
					'rand_max': 25
				},
				size: 275,
				head: 'stamp'
			},
			'fill': {
				build: ['opacity_fill'],
				vars: {
					'movement_fill': 'anchored',
					'opacity_fill': 70
				},
				size: 40,
				head: 'fill'
			},
			'eraser': {
				build: ['diameter_eraser', 'hardness_eraser', 'flow_eraser', 'opacity_eraser'],
				vars: {
					'movement_eraser': 'freedraw',
					'diameter_eraser': 25,
					'flow_eraser': 90,
					'hardness_eraser': 60,
					'opacity_eraser': 30
				},
				size: 165,
				glyph: 1
			},
			'picker': {
				size: 75
			}
		}
	},
	forge: function (v) { // build "OPTIONS" window
		if (!gui_options.modules || v == 'shape') {
			gui_options.modulesObject();
		};
		var r = gui_options.modules[v],
			z = '';
		for (var i in r.vars) { // BUILD CONTENT
			if (!vars[i]) var b = r.vars[i];
			if (i == 'movement_shape') {
				i = 'movement_' + vars.shape;
			} else if (i == 'stroke_shape') {
				i = 'stroke_' + vars.shape;
			}
			if (!vars[i]) {
				vars[i] = b;
			}
		}
		if (v == 'shape' || v == 'marquee') {
			var d = vars[v];
			gui_options.resize(r.size[d] ? r.size[d] : r.size['']);
		} else {
			gui_options.resize(r.size);
		};
		for (var i in r.build) {
			i = r.build[i];
			var o = i.indexOf('_') != -1 ? i.substr(0, i.indexOf('_')) : i,
				title = o.replace('_', ' ').replace('line', '').replace('rand', 'diameter &middot; min, max').replace('font', '').replace('marq', '').replace('shape', '').toLowerCase(),
				fu = (i == 'br') ? '<br>' : gui[cF[i].type].build(title, i, cF[i].val);
			if (v == 'shape') {
				if ((d != 'ellipses' && d != 'polygon') || (d == 'ellipses' && i != 'corner' && i != 'sides_shape' && i != 'slope_shape') || (d == 'polygon' && i != 'slope_shape')) {
					z += (i == 'stroke_ellipses' ? '<br>' : '') + fu;
				}
			} else if (v == 'marquee') {
				if ((d != 'ellipses' && d != 'polygon') || (d == 'polygon' && i != 'slope_marquee')) {
					z += fu;
				}
			} else {
				z += fu;
			}
		}
		if(v == 'fill' || v =='brush' || v == 'pencil' || v == 'calligraphy' || v == 'stamp') { // these tools use only the "fill" property
			gui_palette.click('fill')		
		} else if(v == 'spirograph') { // these ones use only the "stroke" property
			gui_palette.click('stroke')		
		} 
		switch(v) { // extra stuff
			case 'picker':
				z = '<img src="media/gui/loupe.png" onmousedown="return(noMove())" class="loupe" alt="...">'+
					'<canvas id="picker" height="106" width="149" style="height: 106px; width: 149px"></canvas>'+
					'<div class="picker"><div>R<br>G<br>B<br>A</div><div id="picker_hex">0<br>0<br>0<br>0</div></div>'+
					'<canvas id="picker1x1" height="1" width="1"></canvas>';
				break;
			case 'stamp':
				z = '<div id="stamp"></div>' + gui.Y.kontrol('stamp') + 
					"<span style=\"position: relative; top: -6px; border: 0\" id=\"brush_author\"></span>" + z;
				break;
			case 'text': 
				if(!vars.textMessage) {
					vars.textMessage = "hello!";
				}
				z = '<div id="text" style="padding-bottom: 13px">' +
					' <span style="font-size: 11px; -moz-user-select: none; -khtml-user-select: none; user-select: none; ">MESSAGE</span><br>' +
					' <input type="text" onmousedown="preventDefault = false;" onkeydown="vars.textMessage=this.value;" onkeyup="vars.textMessage=this.value;" style="width: 90%; font-size: 16px" value="'+vars.textMessage+'">' +
					'</div>' + z;
				break;
		} 
		var d = $('options');
		$C('TML', d)[0].innerHTML = '<span style="-moz-user-select: none; -khtml-user-select: none; user-select: none; ">'+(r.head ? gui.menu.build(r.head, cF[r.head].val) : v)+'</div>';
		$C('MM', d)[0].innerHTML = '<div class="z" style="display: none; margin-top:1px;">' + z + '</div>';
		if (v == 'stamp') {
			stamp.reset();
		} else if (v == 'calligraphy') {
			co.glyph('media/glyph/Calligraphy/0-live.png');
		}
		if (r.glyph) {
			co.glyph();
		}
		if (!$('ioptions').opened) {
			win.tab($('ioptions'), 'options');
		}
		$C('z', d)[0].style.display = 'block';
	},
	resize: function (n) { // resize "OPTIONS" window
		function fu(o) {
			o.style.height = n + "px";
		};
		var o = $('options'),
			l = $C('ML', o)[0],
			m = $C('MM', o)[0],
			r = $C('MR', o)[0];
		fu(l);
		fu(r);
		fu(m);
		$S('options').height = (n + 40) + 'px';
		win.r['options'][3] = n + 40;
	}
};

// PALETTE

gui_palette = {

	click: function (v) {
		if(vars.id == v) {
			return;
		}
		gui_palette.zindex(v);
		gui_palette.run(gui_swatch.L2S[vars[vars.id = v]]);
	},
	zindex: function (v) {
		var r = {
			'fill': 'stroke',
			'stroke': 'fill'
		};
		function fu(v, c, i) {
			var o = $(v).parentNode.style;
			o.zIndex = i;
			o.cursor = c;
			$S(v).cursor = c;
		}
		fu(v, 'default', 2);
		fu(r[v], 'pointer', 1);
		$('swap').innerHTML = v.substr(0,1).toUpperCase();
	},
	current: function () {
		var q = gui_swatch.L2S,
			r = {
				'CO': 1,
				'GD': 1,
				'PT': 1
			},
			b;
		for (var i in r) {
			b = gui_swatch.n['fill' + i];
			gui_swatch.n['fill' + i] = gui_swatch.n['stroke' + i];
			gui_swatch.n['stroke' + i] = b;
		}
		var z = (vars.id == 'fill'),
			a = z ? 'stroke' : 'fill',
			b = z ? 'fill' : 'stroke',
		z = '';
		if (vars.fill == 'pattern' || vars.stroke == 'pattern') {
			gui_pattern.o[a] = vars[b + 'PT'];
			gui_pattern.o[b] = vars[a + 'PT'];
			gui_pattern.create();
		}
		z = vars[a];
		vars[a] = vars[b];
		vars[b] = z;
		z = vars[a + 'CO'];
		vars[a + 'CO'] = vars[b + 'CO'];
		vars[b + 'CO'] = z;
		z = vars[a + 'GD'];
		vars[a + 'GD'] = vars[b + 'GD'];
		vars[b + 'GD'] = z;
		z = vars[a + 'PT'];
		vars[a + 'PT'] = vars[b + 'PT'];
		vars[b + 'PT'] = z;
		gui_palette.update(a);
		gui_palette.run(q[vars[vars.id]]);
	},
	// Visualizer
	run: function (n) {
		if (n != 'GD') gui_gradient.mk(1);
		gui_swatch.cur(n);
		co.glyph();
		vars.cache(1);
	},
	create: function (v) {
		if (v) vars[vars.id] = v;
		gui_palette.update();
		co.glyph();
	},
	update: function (b, m) {
		stamp.preview(stamp.fileNumber, m);
		var id = b || vars.id,
			d = $(id),
		c = d.getContext('2d');
		c.clearRect(0, 0, d.width = 34, d.height = 23);
		style(c, "fillStyle", id, { X: 0, Y: 0 }, { X: 34, Y: 23 });
		c.fillRect(0, 0, 34, 23);
	}
};

// EDIT COLOR

gui_color = {
	// Mouse
	'core': function (o, e, fu) {
		if (gui_swatch.id == 'PT') gui_swatch.cur('CO');
		core.fu(o, e, {
			fu: core.X,
			oX: -13.5,
			X1: 0,
			X2: 121,
			oY: 0
		},
		fu);
	},
	'cur': function (n, v, i, m) {
		var b = gui_color[v+"_"][i],
			n = Math.max(0, n) / 121;
		gui_color[v][i[0]] = (i == 'Alpha') ? n : Math.round((1 - n) * b);
		gui_color.run(i);
		if (m == 'up') gui_palette.create();
		else gui_palette.update('', m);
	},
	'pos': function (r, v, i) {
		var s = (i == 'Alpha'),
		n = r[i[0]];
		n = s ? (1 - n) * 100 : n;
		$S(i + 'Cur').left = parseInt((121 - (n / gui_color[v+"_"][i]) * 121) + 10) + 'px';
		$(i + 'Me').innerHTML = Math.round(s ? 100 - n : n);
	},
	// Create
	'mk': function () {
		var z = '',
		o = $C('MM', 'solid')[0];
		var R = {
			'Hue': 'HSV',
			'Saturation': 'HSV',
			'Value': 'HSV',
			'Red': 'RGB',
			'Green': 'RGB',
			'Blue': 'RGB',
			'Alpha': 'RGB'
		};
		for (var i in R) {
			var v = 'gui_color.core(\'' + i + 'Cur\',event,function(a,b,m) { gui_color.cur(b.X,\'' + R[i] + '\',\'' + i + '\',m); })';
			z += '<div title="' + i.substr(0,1).toUpperCase() + i.substr(1) + '">' + ' <div class="west" id="' + i + 'T">' + i.substr(0,1).toUpperCase() + '</div>' + 
				 ' <span class="east" id="' + i + 'Me"></span>' + ' <div onmousedown="' + v + '; return false;" id="' + i + 'Cur" class="cur"></div>' + 
				 ' <canvas id="' + i + '" onmousedown="' + v + '" height="18" width="120"></canvas><br>' + 
				 '</div>';
		}
		o.innerHTML = '<div class="z">' + z + '</div>';
	},
	// Visualizer
	'run': function (m, r) {
		if (m == 'set') {
			m = 'Red';
		}
		if (r) {
			var r = gui_color.RGB = {
				R: r[0],
				G: r[1],
				B: r[2],
				A: r[3]
			},
			h = gui_color.HSV = Color.RGB_HSV(gui_color.RGB);
		} else {
			var r = gui_color.RGB,
				h = gui_color.HSV;
		}
		if (gui_color.HSV_[m]) {
			var t = Color.HSV_RGB(h);
				t.A = r.A;
				r = gui_color.RGB = t;
		} else if (gui_color.RGB_[m]) {
			h = gui_color.HSV = Color.RGB_HSV(r);
		}
		var R = {
			'Hue': [
				[0, { H: 0, S: h.S, V: h.V }],
				[0.15, { H: 300, S: h.S, V: h.V}],
				[0.30, { H: 240, S: h.S, V: h.V}],
				[0.50, { H: 180, S: h.S, V: h.V}],
				[0.65, { H: 120, S: h.S, V: h.V}],
				[0.85, { H: 60, S: h.S, V: h.V}],
				[1, { H: 0, S: h.S, V: h.V}]],
			'Saturation': [
				[0, { H: h.H, S: 100, V: h.V}],
				[1, { H: h.H, S: 0, V: h.V}]],
			'Value': [
				[0, { H: h.H, S: h.S, V: 100}],
				[1, { H: h.H, S: h.S, V: 0}]],
			'Red': [
				[0, { R: 255, G: r.G, B: r.B, A: r.A }],
				[1, { R: 0, G: r.G, B: r.B, A: r.A }]],
			'Green': [
				[0, { R: r.R, G: 255, B: r.B, A: r.A }],
				[1, { R: r.R, G: 0, B: r.B, A: r.A }]],
			'Blue': [
				[0, { R: r.R, G: r.G, B: 255, A: r.A }],
				[1, { R: r.R, G: r.G, B: 0, A: r.A }]],
			'Alpha': [
				[0, { R: r.R, G: r.G, B: r.B, A: 0 }],
				[1, { R: r.R, G: r.G, B: r.B, A: 1 }]]
		};
		for (var i in R) {
			var c = $2D(i),
				g = c.createLinearGradient(0, 0, 120, 18);
			c.globalCompositeOperation = 'copy';
			for (var j in R[i]) {
				var j = R[i][j],
					k = j[1];
				if (gui_color.HSV_[i]) {
					k = Color.HSV_RGB(k);
					k.A = r.A;
				}
				var rgb = gui_color.mode(k, isNaN(k.A) ? 1 : 0);
				rgb = rgb.R + ',' + rgb.G + ',' + rgb.B + ',' + rgb.A;
				g.addColorStop(j[0], 'rgba(' + rgb + ')');
			}
			c.rect(0,0,120,18);
			co.style.fill(c, g);
			if (gui_color.HSV_[i]) {
				gui_color.pos(h, 'HSV', i);
			} else {
				gui_color.pos(r, 'RGB', i);
			}
		}
		var r = gui_color.mode(r);
		$('HEX').innerHTML = Color.HEX_STRING(r.R << 16 | r.G << 8 | r.B);
		if (vars[vars.id] == 'solid') {
			vars[vars.id + 'CO'] = [ r.R, r.G, r.B, r.A ];
			vars.opacity_fill = r.A * 100;
			if($('opacity_fillCur') && vars.id == "fill") {
				$('opacity_fillCur').style.left = parseInt(r.A * 110) + 'px';
				$('opacity_fillCurV').innerHTML = parseInt(r.A * 100);			
			}
		} else if (vars[vars.id] == 'gradient') {
			var b = gui_gradient.o.id.substr(2);
			vars[vars.id + 'GD'][b][1] = [ r.R, r.G, r.B, r.A ];
			gui_gradient.run(b, 'move');
		}
	},
	// Data
	'mode': function (r, s) {
		return (s ? r : { R: r.R, G: r.G, B: r.B, A: r.A });
	},
	'RGB_': {
		'Red': 255,
		'Green': 255,
		'Blue': 255,
		'Alpha': 100
	},
	'HSV_': {
		'Hue': 360,
		'Saturation': 100,
		'Value': 100
	},
	'RGB': {
		'R': 255,
		'G': 0,
		'B': 0,
		'A': 1
	},
	'HSV': {
		'H': 0,
		'S': 100,
		'V': 100
	}
};

// EDIT GRADIENT

gui_gradient = {
	// Mouse
	'slide_x': function (a, b, m) {
		var g = vars[vars.id + 'GD'],
		r = g[gui_gradient.o.id.substr(2)];
		if (N.between(b.Y, 0, 40)) {
			if (gui_gradient.del) {
				gui_gradient.o.style.display = 'block';
				gui_gradient.del = '';
				r[2] = 0;
			}
			r[0] = Math.round(Math.min(1, Math.max(0, b.X / 169)) * 1000) / 1000;
			$('gPos').innerHTML = Math.round(r[0] * 100) + '<span class="small">%</span>';
			gui_gradient.o.style.left = (Math.max(0, b.X) - 5) + 'px';
		} else if (g.length > 2) {
			gui_gradient.o.style.display = 'none';
			gui_gradient.del = gui_gradient.o;
			r[2] = 1;
		}
		if (m == 'up' && gui_gradient.del && g.length > 2) gui_gradient.remove();
		infc.info(m, 'cGD2', 'gPos');
		gui_gradient.fill(m);
	},
	'slide_y': function (a, b, m) {
		var g = vars[vars.id + 'GD'],
		op = Math.round((1 - (b.Y + 5) / 91) * 1000) / 1000;
		$S('gOpacity').top = b.Y + 'px';
		$('gPos').innerHTML = Math.round(op * 100) + '<span class="small">%</span>';
		vars.opacity_fill = op * 100;
		if($('opacity_fillCur') && vars.id == "fill") {
			$('opacity_fillCur').style.left = parseInt(op * 110) + 'px';
			$('opacity_fillCurV').innerHTML = parseInt(op * 100);			
		}
		for (var i in g) {
			g[i][1][3] = op;
		}
		infc.info(m, 'cGD2', 'gPos');
		gui_gradient.run('false', m);
		gui_gradient.cs();
	},
	'slide_o': function (o, m, a, x) {
		a.X += x.oX;
		a.Y += x.oY;
		var o = $S('gAngle'),
		W = 132,
		W2 = W / 2,
		R = Math.atan2(a.X - W2 - 3, W - a.Y - W2 - 6);
		gui_gradient.rotate = R;
		o.left = (Math.abs((Math.sin(R) * W2) + W2) + 30) + 'px';
		o.top = (Math.abs((Math.cos(R) * W2) - W2) + 40) + 'px';
		$('gPos').innerHTML = Math.round((gui_gradient.rotate + (Math.PI * 2.5)) * (180 / Math.PI) % 360) + '&deg;';
		infc.info(m, 'cGD2', 'gPos');
		gui_gradient.fill(m);
	},
	'cur': function (o) {
		if (gui_gradient.o) gui_gradient.o.className = '';
		o.className = 'cur';
		gui_gradient.o = o;
	},
	'add': function (e) {
		if (stop) {
			var g = vars[vars.id + 'GD'],
			n = g.length;
			g[n] = [0, deObject(vars[vars.id + 'GD'][gui_gradient.o.id.substr(2)][1])];
			gui_gradient.mk_x('gd' + n);
			core.fu('gd' + n, e, {
				fu: core.X,
				oX: 0,
				oY: 15,
				X1: 0,
				X2: 169
			},
			gui_gradient.slide_x);
		}
	},
	'remove': function (o) {
		vars[vars.id + 'GD'].splice(gui_gradient.del.id.substr(2), 1);
		gui_gradient.mk_x();
	},
	// Create
	'mk': function () {
		var o = $C('MM', 'gradient')[0];
		o.innerHTML = '<div class="z" onmousedown="gui_swatch.toType(\'GD\')">' + ' <div onmousedown="core.fu(\'cGD1\',event, {fu:gui_gradient.slide_o, oX:-7, oY:-13}); return false;" id="gAngle" class="blue_dot" title="Angle"></div>' + ' <div onmousedown="core.fu(\'cGD1\',event, {fu:core.Y, oX:0, oY:-41, Y1:-5, Y2:86}, gui_gradient.slide_y); return false;" class="blue_slide"><div id="gOpacity" class="blue_dot" title="Opacity"></div></div>' + ' <div id="gPos"></div>' + ' <canvas id="cGD1" height="169" width="169"></canvas>' + ' <canvas id="cGD2" onmousedown="core.fu(\'cGD1\',event, {fu:gui_gradient.slide_o, oX:-7, oY:-13})" height="169" width="169"></canvas>' + ' <div class="slide_x" onmousedown="gui_gradient.add(event)"></div>' + '</div>';
		gui_gradient.mk_x();
		infc.info_draw('cGD2');
		if (vars[vars.id] == 'gradient') gui_gradient.cs();
	},
	'mk_x': function (o) {
		var g = vars[vars.id + 'GD'],
		z = '';
		for (var i in g) z += '<div onmousedown="gui_gradient.cur(this); gui_gradient.cs(); core.fu(\'gd' + i + '\',event,{fu:core.X, oX:0, oY:15, X1:0, X2:169}, gui_gradient.slide_x)" id="gd' + i + '" style="left: ' + ((g[i][0] * 169) - 5) + 'px"><canvas height="7" width="7"></canvas></div>';
		$C('slide_x', 'gradient')[0].innerHTML = z;
		gui_gradient.cur($(o ? o : 'gd' + i));
		gui_gradient.run('false', 'move');
	},
	// Visualizer
	'run': function (v, m) {
		var g = vars[vars.id + 'GD'];
		function z(i) {
			var c = $T('canvas', 'gd' + i)[0].getContext('2d');
			c.clearRect(0, 0, 7, 7);
			c.rect(0,0,7,7);
			co.style.fill(c, 'rgba(' + g[i][1].join(',') + ')');
		}
		if (isNaN(v)) {
			for (var i in g) {
				z(i);
			}
		} else {
			z(v);
		}
		gui_gradient.fill(m);
	},
	'fill': function (m) {
		var c = $2D('cGD1'),
		g = vars[vars.id + 'GD'];
		co.gradient({
			'X': 0,
			'Y': 0
		},
		{
			'X': 169,
			'Y': 169
		},
		c, g, 'fill');
		c.clearRect(0, 0, 169, 169);
		c.fillRect(0, 0, 169, 169);
		if (m == 'up') gui_palette.create();
		else gui_palette.update('', m);
	},
	// Data
	'cs': function (v) {
		v = vars[vars.id + 'GD'][gui_gradient.o.id.substr(2)][1];
		gui_color.run('set', v);
	},
	'rotate': (Math.PI / 2) + Math.PI
};

// EDIT PATTERN

var gui_pattern = {
	// Mouse
	'slide_y': function (a, b, m) {
		var op = (gui_pattern.op[vars.id] = Math.round((1 - (b.Y + 5) / 91) * 1000) / 1000);
		$('pPos').innerHTML = Math.round(op * 100) + '<span class="small">%</span>';
		vars.opacity_fill = op * 100;
		if($('opacity_fillCur') && vars.id == "fill") {
			$('opacity_fillCur').style.left = parseInt(op * 110) + 'px';
			$('opacity_fillCurV').innerHTML = parseInt(op * 100);			
		}
		$S('pOpacity').top = b.Y + 'px';
		infc.info(m, 'cPT2', 'pPos');
		gui_pattern.create(op, m);
		if (m == 'up') gui_palette.create();
		else gui_palette.update('', m);
	},
	// Create
	'mk': function (s) {
		var j = 0,
		r = vars[vars.id + 'GD'],
		o = $C('MM', 'pattern')[0],
		z = '';
		o.innerHTML = '<div class="z" onmousedown="gui_swatch.toType(\'PT\')">' + ' <div onmousedown="core.fu(\'cPT1\',event, {fu:core.Y, oX:0, oY:-41, Y1:-5, Y2:86}, gui_pattern.slide_y); return false;" class="blue_slide"><div id="pOpacity" class="blue_dot" title="Opacity"></div></div>' + ' <div id="pPos"></div>' + ' <canvas id="cPT"></canvas>' + ' <canvas id="cPT1" height="169" width="169"></canvas>' + ' <canvas id="cPT2" height="169" width="169"></canvas>' + '</div>';
		gui_pattern.o[vars.id] = vars[vars.id + 'PT'];
		gui_pattern.fill();
		infc.info_draw('cPT2', 1);
		gui_palette.update();
		if (vars[vars.id] == 'pattern') {
			gui_gradient.cs();
		}
		gui_pattern.cache("fill");
		gui_pattern.cache("stroke");
	},
	// Visualizer
	'cache': function (type) { // cache image w/ createPattern()
		var pattern = vars[type+'PT'];
		dtx2D.width = pattern.width;
		dtx2D.height = pattern.height;
		ctx2D.save();
		ctx2D.globalAlpha = isNaN(pattern.opacity) ? 1 : pattern.opacity;
		ctx2D.drawImage(pattern, 0, 0);
		ctx2D.restore();
		vars[type+'PT~'] = ctx2D.createPattern(dtx2D, "repeat");
	},
	'create': function (opacity, m) {
		var image = vars[vars.id + 'PT'],
			b = { X: image.width, Y: image.height };
		if (!opacity) {
			opacity = gui_pattern.op[vars.id];
		}
		image.opacity = opacity;
		$('cPT').width = b.X;
		$('cPT').height = b.Y;
		gui_pattern.cache(vars.id);
		gui_pattern.o[vars.id] = $('cPT'); // GENERATE
		gui_pattern.fill(opacity, 0, m); // UPDATE
	},
	'fill': function (op, b, m, i) {
		if (!op) op = gui_pattern.op[vars.id];
		var a = {
			'X': 0,
			'Y': 0
		},
		b = b ? b : {
			'X': 169,
			'Y': 169
		},
		c = $2D(i ? i : 'cPT1');
		co.del(c);
		c.globalCompositeOperation = 'source-over';
		c.rect(a.X,a.Y,b.X,b.Y);
		co.style.fill(c, c.createPattern(vars[vars.id + 'PT'], 'repeat'));
		c.globalCompositeOperation = 'destination-in';
		c.rect(a.X,a.Y,b.X,b.Y);
		c.fillStyle = 'rgba(255,255,255,' + op + ')';
		c.fill();
		if (m == 'up') gui_palette.create();
		else gui_palette.update('', m);
	},
	// Data
	'o': {
		'stroke': new Image(),
		'fill': new Image()
	},
	'op': {
		'stroke': 1,
		'fill': 1
	},
	'dir': 'media/patterns/'
};

// EDIT SWATCH

gui_swatch = {
	// Mouse
	'click': function (o) {
		var n = parseInt(o.id.replace(/[a-zA-Z]*/, ''));
		if (gui_swatch.n[vars.id + gui_swatch.id] != n) {
			if (gui_swatch.id == 'PT') {
				vars[vars.id + gui_swatch.id] = new Image();
				vars[vars.id + gui_swatch.id].src = vars[gui_swatch.id][n - 1].src;
			} else {
				vars[vars.id + gui_swatch.id] = deObject(vars[gui_swatch.id][n - 1]);
			}
			gui_swatch.cur_switch(n);
			co.glyph();
		}
	},
	'cur': function (type, s) {
		gui.Y.id = type = type || "CO";
		vars[vars.id] = gui_swatch.r[type][0];
		function z(a, b, c) {
			$C(a, 'swatch')[0].style[b] = c;
		}
		if(gui_swatch.id) {
			z(gui_swatch.id, 'display', 'none');
			z(gui_swatch.id + 'menu', 'cursor', 'pointer');
		}
		z(type, 'display', 'block');
		z(type + 'menu', 'cursor', 'default');
		gui_swatch.pos(gui_swatch.n[vars.id + (gui_swatch.id = type)]);
		gui_swatch.cur_switch(gui_swatch.n[vars.id + gui_swatch.id], s);
		co.glyph();
	},
	'cur_switch': function (n, s) {
		var b = gui_swatch.n[vars.id + gui_swatch.id];
		gui_swatch.n[vars.id + gui_swatch.id] = n;
		if (!s) {
			gui_swatch.run();
		}
		if ($(gui_swatch.o)) {
			$(gui_swatch.o).className = '';
			gui_swatch.update(b);
		}
		$(gui_swatch.o = vars.id + gui_swatch.id + n).className = 'cur';
		gui_swatch.update(n);
	},
	'pos': function (n) {
		var o = gui.Y,
		a = Math.ceil(n / 7) * 7,
		b = (a - 7) - (o.cur[gui_swatch.id] - 1);
		if (b > 21 || b < 0) o.cur[gui_swatch.id] = (a - (b < 0 ? 7 : 28));
		o.prev[gui_swatch.id] = 0;
		o.cord(0);
		o.sw(gui_swatch.n[vars.id + gui_swatch.id] = n);
	},
	'add': function () {
		var r = vars[gui_swatch.id],
		n = gui_swatch.n[vars.id + gui_swatch.id];
		var fu = {
			'CO': function (r) {
				r = deObject(vars[vars.id + gui_swatch.id]);
				return (r);
			},
			'GD': function (r) {
				r = deObject(vars[vars.id + gui_swatch.id]);
				return (r);
			},
			'PT': function (r) {
				r = new Image();
				r[r.length - 1].src = vars[vars.id + gui_swatch.id].src;
				return (r);
			}
		};
		r.splice(n, 0, fu[gui_swatch.id](r));
		vars[vars.id + gui_swatch.id] = r[n];
		gui.Y.kontrol_update(gui_swatch.id);
		gui_swatch.pos(n + 1);
	},
	'remove': function () {
		if (vars[gui_swatch.id].length > 1) {
			var r = vars[gui_swatch.id],
			n = gui_swatch.n[vars.id + gui_swatch.id];
			r.splice(n - 1, 1);
			vars[vars.id + gui_swatch.id] = r[(n = Math.min(n, r.length)) - 1];
			gui_swatch.pos(n);
			gui_swatch.run();
			gui.Y.kontrol_update(gui_swatch.id);
		}
	},
	// Create
	author: function(id) {
		var id = id || gui_swatch.id,
			res = Resources[id][vars[id+'*']] || "";
		if(res) { // resource
			return '<i>by:&nbsp; <a href="'+res.url+'" target="_blank">'+res.name+'</a></i>';
		} else {
			return '';
		}
	},
	'mk': function () {
		var z = '';
		gui_swatch.set = {};
		for (var i in gui_swatch.r) {
			var k = 0;
			gui_swatch.set[i] = {};
			for (var j in Q[i]) gui_swatch.set[i][k++] = j;
		}
		for (var i in gui_swatch.r) {
			var res = '<div id="author_'+i+'" class="author">'+gui_swatch.author(i)+'</div>';
			z += '<div class="menu ' + i + 'menu" onmousedown="gui_swatch.cur(\'' + i + '\'); vars.cache(1);">' + ' <div>' + gui_swatch.r[i][0].toUpperCase() + 'S</div>' + 
				 ' <span class="east">' + 
				 ' <div style="padding: 2px 0 0; font-size: 12px" id="'+i+'_author"></div>' +
//				 '  <img src="media/gui/sw_remove.png" onmousedown="if(this.parentNode.parentNode.style.cursor==\'default\') gui_swatch.remove(); return false;" alt="..." class="remove">' + 
//				 '  <img src="media/gui/sw_add.png" onmousedown="if(this.parentNode.parentNode.style.cursor==\'default\') gui_swatch.add(); return false;" alt="...">' + 
				 ' </span>' + 
				 '</div>' + 
				 '<div class="' + i + '" style="display: none">' + 
				 ' <div style="position: absolute; left: 19px; top: ' + (42 + gui_swatch.r[i][2]) + 'px;">' + gui.menu.build(i + '*', gui_swatch.set[i]) + '</div>' + 
				 ' <div id="' + i + '" class="squares"></div>' + gui.Y.kontrol(i, gui_swatch.r[i][2] + 49) + '<br>' +res+ 
				 '</div>';
		}
		$C('MM', 'swatch')[0].innerHTML = '<div class="z">' + z + '</div>';
		gui_swatch.cur(gui_swatch.id = gui_swatch.L2S[vars[vars.id]]);
	},
	// Visualizer
	'run': function () {
		({
			'CO': function () {
				gui_color.run('set', vars[vars.id + gui_swatch.id]);
				gui_palette.update();
			},
			'GD': function () {
				gui_gradient.mk_x();
				gui_gradient.cs();
				$S('gOpacity').top = '-5px';
			},
			'PT': function () {
				gui_pattern.o[vars.id] = vars[vars.id + 'PT'];
				gui_pattern.create();
			}
		})[gui_swatch.id]();
	},
	'update': function (i, s) {
		var r = vars[gui_swatch.id];
		var fu = {
			'CO': function (r, c) {
				c.fillStyle = 'rgba(' + r[i - 1].join(',') + ')';
				c.fill();
			},
			'GD': function (r, c) {
				co.gradient({
					'X': 3,
					'Y': 3
				},
				{
					'X': 13,
					'Y': 13
				},
				c, r[i - 1], 'fill', 1);
			},
			'PT': function (r, c) {
				c.fillStyle = c.createPattern(r[i - 1], 'repeat');
				c.fill();
			}
		};
		function z(i) {
			var d = $(vars.id + gui_swatch.id + i);
			if (d) {
				var c = $2D(vars.id + gui_swatch.id + i);
				c.clearRect(0, 0, 16, 16);
				if (gui_swatch.n[vars.id + gui_swatch.id] == i && !s) {
					var a = {
						'X': 3,
						'Y': 3
					},
					b = {
						'X': 13,
						'Y': 13
					};
					c.beginPath();
					co.circle(a, b, c);
					c.strokeStyle = 'rgba(255,255,255,1)';
					c.lineWidth = 1.5;
					c.stroke();
					fu[gui_swatch.id](r, c);
					c.beginPath();
				} else {
					c.rect(0, 0, 16,16);
					fu[gui_swatch.id](r, c);
				}
			}
		}
		if (isNaN(i)) for (var i = gui.Y.cur[gui_swatch.id], ii = 1; i <= r.length && ii <= 28; i++, ii++) z(i);
		else z(i);
	},
	toType: function (type) {
		if (gui_swatch.id != type) {
			gui_swatch.cur(type, 1);
			gui_palette.create();
		}
	},
	// Data
	'L2S': { 
		'solid': 'CO',
		'gradient': 'GD',
		'pattern': 'PT'
	},
	'S2L': {
		'CO': 'solid',
		'GD': 'gradient',
		'PT': 'pattern'
	},
	'S2N': {
		'CO': 1,
		'GD': 2,
		'PT': 3
	},
	'r': {
		'CO': ['solid', -13, 6],
		'GD': ['gradient', 6, 25],
		'PT': ['pattern', 25, 44]
	},
	'n': {
		'fillCO': 1,
		'fillGD': 1,
		'fillPT': 1,
		'strokeCO': 2,
		'strokeGD': 2,
		'strokePT': 2
	},
	'o': ''
};

// INTERFACE

infc = {
	// INFORMATION
	'info': function (m, o, i) {
		if (m == 'down' || m == 'up') {
			var c = $2D(o),
			r = {
				'down': ['source-over', 0.4, 0.2, 0],
				'up': ['destination-out', 1, 1, 1]
			} [m];
			c.beginPath();
			c.globalCompositeOperation = r[0];
			co.circle({
				'X': 35 + r[3],
				'Y': 20 + r[3]
			},
			{
				'X': -15 - r[3],
				'Y': -30 - r[3]
			},
			c);
			c.lineWidth = 1;
			co.style.fill(c, 'rgba(0,0,0,' + r[1] + ')');
			co.style.stroke(c, 'rgba(0,0,0,' + r[2] + ')');
			$S(i).display = (m == 'down') ? 'block' : 'none';
		}
	},
	'info_draw': function (v, s) {
		var c = $2D(v),
		a = {
			'X': 37,
			'Y': 37
		},
		b = {
			'X': 131,
			'Y': 131
		}
		function ellip(a, b, r, w) {
			c.beginPath();
			c.lineWidth = w;
			co.circle(a, b, c);
			co.style.stroke(c, 'rgba(' + r + ')');
		}
		function line(a, b, r, w) {
			c.beginPath();
			c.lineWidth = w;
			c.moveTo(a.X, a.Y);
			c.lineTo(b.X, b.Y);
			co.style.stroke(c, 'rgba(' + r + ')');
		}
		if (!s) { // DRAW ROTATE
			ellip(a, b, [0, 0, 0, 1], 8);
			c.globalCompositeOperation = 'source-out';
			ellip({
				'X': a.X + 3,
				'Y': a.Y + 3
			},
			{
				'X': b.X + 3,
				'Y': b.Y + 3
			},
			[0, 0, 0, 0.3], 2);
			c.globalCompositeOperation = 'source-over';
			ellip(a, b, [128, 128, 128, 0.4], 8);
			ellip({
				'X': a.X,
				'Y': a.Y + 1
			},
			{
				'X': b.X,
				'Y': b.Y + 1
			},
			[255, 255, 255, 0.2], 1);
			ellip(a, b, [33, 33, 33, 1], 1);
			line({
				X: 84,
				Y: 14
			},
			{
				X: 84,
				Y: 7
			},
			[0, 0, 0, 0.4], 2);
			line({
				X: 85,
				Y: 14
			},
			{
				X: 85,
				Y: 7
			},
			[255, 255, 255, 0.4], 1); // N
			line({
				X: 85,
				Y: 154
			},
			{
				X: 85,
				Y: 161
			},
			[0, 0, 0, 0.4], 2);
			line({
				X: 85,
				Y: 154
			},
			{
				X: 84,
				Y: 161
			},
			[255, 255, 255, 0.4], 1); // S
			line({
				X: 154,
				Y: 85
			},
			{
				X: 161,
				Y: 85
			},
			[0, 0, 0, 0.4], 2);
			line({
				X: 154,
				Y: 86
			},
			{
				X: 161,
				Y: 86
			},
			[255, 255, 255, 0.4], 1); // E
			line({
				X: 14,
				Y: 86
			},
			{
				X: 7,
				Y: 86
			},
			[0, 0, 0, 0.4], 2);
			line({
				X: 14,
				Y: 85
			},
			{
				X: 7,
				Y: 85
			},
			[255, 255, 255, 0.4], 1); // W
		}
		line({
			X: 87,
			Y: 35
		},
		{
			X: 87,
			Y: 135
		},
		[0, 0, 0, 0.7], 1);
		line({
			X: 84,
			Y: 35
		},
		{
			X: 84,
			Y: 135
		},
		[128, 128, 128, 0.4], 8);
		line({
			X: 84,
			Y: 35
		},
		{
			X: 84,
			Y: 135
		},
		[0, 0, 0, 1], 1);
		line({
			X: 85,
			Y: 35
		},
		{
			X: 85,
			Y: 135
		},
		[255, 255, 255, 0.2], 1);
		c.closePath();
	},
	// GLOBAL OPACITY
	'opacity': function (o, v, n) {
		gui_palette.click("fill");
		var op = Math.round((vars[o] = Math.min(v[1], (Math.max(0, n) / 110) * 100)));
		if ($(o + 'CurV')) {
			$(o + 'CurV').innerHTML = Math.round(op);
		}
		switch(vars["fill"]) {
			case 'pattern':
				gui_pattern.create(op / 100);
				$S('pOpacity').top = ((1 - (op / 100)) * 91 - 5) + 'px'
				gui_pattern.op["fill"] = Math.round((op / 100) * 1000) / 1000;
				break;
			case 'gradient':
				$S('gOpacity').top = Math.round((1 - (op / 100)) * 91 - 5) + 'px'
				var g = vars["fill" + 'GD'];
				for (var i in g) {
					g[i][1][3] = op / 100;
				}
				gui_gradient.run('false', 'move');
				break;
			case 'solid':
				var c = vars["fill" + gui_swatch.id];
				c[3] = op / 100;
				gui_color.run('set', c);
				gui_palette.update();
				break;
		}
	}
};
/* 
	KEYPRESS 
*/

(function() {

E = {
	'k': 0,
	'run': 0,
	'sh': 0,
	'sh_var': 0
};

key = {
	'k': function (e) {
		if(typeof(e) == 'undefined') var e = event;
		if(typeof(e.shiftKey) == 'undefined') e.shiftKey = 0;
		E.sh = E.sh_var = e.shiftKey ? 1 : 0;
		E.ctrl = e.ctrlKey ? 1 : 0;
		return (key.code(e));
	},
	'active': function () {
		if (vars.type == 'marquee') {
			marquee.core(oXY, cXY, 'move');
		} else if (vars.type == 'shape') {
			draw.shape(oXY, cXY, 'move');
		}
	},
	'block': function (e, k) {
		var o = {
			8: 'x',
			13: '\n',
			32: 'space',
			46: 'x',
			191: '/',
			222: "'",
			37: 'left',
			38: 'up',
			39: 'right',
			40: 'down'
		};
		if (k in o) {
			e.preventDefault();
			return false;
		}
	},
	'code': function (e) {
		return ! (agent('msie') || agent('opera')) ? e.keyCode : e.which;
	},
	'press': function (k, move, down, up) {
		function mv(x) {
			return function (n) {
				move({
					X: x.X * n,
					Y: x.Y * n
				});
			};
		};
		window.blur();
		setTimeout(window.focus, 0);
		E.k = k;
		key.up = up;
		key.down = down;
		key.move = {
			37: mv({
				X: -1,
				Y: 0
			}),
			38: mv({
				X: 0,
				Y: -1
			}),
			39: mv({
				X: 1,
				Y: 0
			}),
			40: mv({
				X: 0,
				Y: 1
			})
		} [k];
		setTimeout('key.run(' + (E.run = getTime()) + ')', 0);
	},
	'run': function (t) {
		if (E.k && E.run == t) {
			if (key.down) {
				key.down();
				key.down = '';
			};
			key.move(E.sh_var ? 10 : 1);
			setTimeout('key.run(' + t + ')', 100);
		} else if (!E.k && key.up) {
			key.up();
		}
	}
};

document.onkeydown = function (e) {
	if(typeof(e) == 'undefined') var e = event;
	var k = key.k(e),
		r = {};
	if (vars.type == 'marquee' && E.ctrl && k == 65) { // SELECT ALL 
		marquee.core({
			'X': 1,
			'Y': 1
		},
		{
			'X': canvas.W - 1,
			'Y': canvas.H - 1
		},
		'up');
	}
	else if (e.ctrlKey && E.sh && k == 90 && (r = canvas.history_r) && r.r[r.n + 1] <= canvas.history_n && (r.n + 1) <= r.z) { // REDO 
		canvas.history_set(r.n++);
	} else if (e.ctrlKey && !E.sh && k == 90 && (r = canvas.history_r) && r.r[r.n - 1] >= 0 && (r.n - 1) >= r.a) { // UNDO 
		canvas.history_set(r.n--);
	} else if (mXY != 'up' && cXY && cXY.X) { // TOGGLE CONSTRAIN 
		key.active();
	} else if (marquee.on || marquee.mv) { // MARQUEE 
		if (k == 8 || k == 46) { // DELETE SELECTION 
			marquee.del();
		} else if (k == 27) { // DESELECT 
			marquee.reset();
		} else if (k >= 37 & k <= 40) { // MOVE
			marquee.on = 0;
			window.clearInterval(marqueeID);
			marquee.mv = 1;
			key.press(k, marquee.move, null, function () {
				marquee.run();
				marquee.mv = 0;
			});
		}
	} else if (vars.type == 'crop') { // CROP 
		if (bXY.X && k == 13) { // CROP SELECTION
			crop.apply(aXY, bXY);
		} else if (E.ctrl && k == 65) { // SELECT ALL 
			crop.core({
				'X': 0,
				'Y': 0
			},
			{
				'X': canvas.W,
				'Y': canvas.H
			},
			'up');
		} else if (k == 27) { // DESELECT 
			crop.core({
				'X': 0,
				'Y': 0
			},
			{
				'X': 0,
				'Y': 0
			},
			'up');
		} else if (k >= 37 && k <= 40 && bXY.X) { // MOVE
			key.press(k, crop.move);
		}
	}
	if(vars.type != 'text')
		key.block(e, k);
};

document.onkeypress = function (e) {
	if(typeof(e) == 'undefined') var e = event;
	var k = key.k(e);
	if(vars.type != 'text')
		key.block(e, k);
};

document.onkeyup = function (e) {
	if(typeof(e) == 'undefined') var e = event;
	var k = key.k(e);
	if (k == E.k) E.k = 0;
	if (mXY != 'up' && oXY.X && cXY.X) key.active();
};

})();
/* MARQUEE */

marquee = {

	// Keypress
	'move': function (x) {
		var r = marquee.shapes;
		for (var i in r) {
			i = r[i];
			if (i.T == 'lasso') {
				for (var j in i.R) {
					i.R[j].X += x.X;
					i.R[j].Y += x.Y;
				}
			} else {
				i.A.X += x.X;
				i.A.Y += x.Y;
				i.Z.X += x.X;
				i.Z.Y += x.Y;
			}
		}
		marquee.dash(ants[ants_n]);
	},
	// Mouse
	'del': function (c) {
		c = $2D('ctx_temp');
		c.globalCompositeOperation = 'source-over';
		marquee.draw(c);
		co.style.fill(c, 'rgba(0,0,0,1)');
		c = $2D('ctx_box');
		c.globalCompositeOperation = 'destination-out';
		c.drawImage($('ctx_temp'), 0, 0, canvas.W, canvas.H);
		c.globalCompositeOperation = 'source-over';
		co.del('ctx_temp');
		canvas.history_set('delete selection');
	},
	'core': function (a, b, m, e) {
		var c = $2D('ctx_marquee');
		c.beginPath();
		c.globalCompositeOperation = 'source-over';
		if (!E.sh_var && mouse.id == 'move') {
			var r = marquee.shapes,
				o = marquee.prev;
			if (!o) o = a;
			var v = {
				X: o.X - b.X,
				Y: o.Y - b.Y
			};
			if (m == 'down') {
				marquee.on = 0;
				window.clearInterval(marqueeID);
			}
			for (var i in r) {
				i = r[i];
				if (i.T == 'lasso') {
					for (var j in i.R) {
						i.R[j].X -= v.X;
						i.R[j].Y -= v.Y;
					}
				} else {
					i.A.X -= v.X;
					i.A.Y -= v.Y;
					i.Z.X -= v.X;
					i.Z.Y -= v.Y;
				}
			}
			marquee.dash(ants[ants_n]);
			marquee.prev = b;
			if (m == 'up') {
				marquee.run();
				marquee.prev = '';
				marquee.cursor();
			}
		} else if (m != 'down' && (!e || mouse.id || mouse.moved == 1 || (getTime() - core.time) <= 1250)) {
			if (!mouse.down && e) {
				marquee.reset(b);
				mouse.down = 1;
				if (marquee.shape > 0) {
					E.sh = 0;
				}
			} else {
				moXY = a;
				mcXY = b;
				mouse.moved = 1;
			}
			if (Math.abs(a.X - b.X) > 1 && Math.abs(a.Y - b.Y) > 1) {
				if (vars.marquee == 'lasso') {
					marquee.r[marquee.i++] = b;
				}
				marquee.cache(marquee.shape);
				if (m == 'up') {
					mouse.reset();
					if (mouse.moveCheck(a, b)) {
						marquee.reset();
					} else {
						marquee.cache(marquee.shape++);
						marquee.run();
						marquee.cursor();
					}
				}
			}
			marquee.dash(ants[ants_n], c, 1);
		} else if(m == "down" && E.sh_var && marquee.shapes.length) {
			marquee.dash(ants[ants_n]);
		} else {
			marquee.reset();
		}
	},
	/* Visualize */
	'draw': function (c, s1, s2) {
		var r = marquee.shapes,
			o = {
				sides: vars.sides_marquee,
				slope: vars.slope_marquee
			},
			hasPath = false;
		for (var i in r) {
			var i = r[i];
			if (Math.abs(i.A.X - i.Z.X) > 1 && Math.abs(i.A.Y - i.Z.Y) > 1) { //... silly this has to be here
				if (i.sides) {
					vars.sides_marquee = i.sides;
				}
				if (i.slope) {
					vars.slope_marquee = i.slope;
				}
				if (i.T == 'lasso') {
					co.path(c, i.R);
					if (!s1) c.closePath();
				} else if (i.T == 'ellipses') {
					co.ellipses(i.A, i.Z, c, i.sh);
				} else if (i.T == 'polygon') {
					co.polygon(i.A, i.Z, c, i.sh);
				} else if (i.T == 'star') {
					co.star(i.A, i.Z, c, i.sh);
				} else if (i.T == 'burst') {
					co.burst(i.A, i.Z, c, i.sh);
				} else if (i.T == 'gear') {
					co.gear(i.A, i.Z, c, i.sh);
				}
				hasPath = true;
			}
		}
		if (s2 && hasPath) {
			c.strokeStyle = s2.v;
			c.stroke();
		}
		vars.sides_marquee = o.sides;
		vars.slope_marquee = o.slope;
	},
	'cursor': function () {
		var c = $2D('ctx_mouse');
		co.del(c);
		c.beginPath();
		marquee.draw(c);
		c.lineWidth = 2;
		c.strokeStyle = 'rgba(0,0,0,1)';
		c.stroke();
		c.fillStyle = 'rgba(0,0,0,1)';
		c.fill();
		if (!mouse.area) mouse.area = {};
		mouse.area['000000FF'] = {
			'id': 'move',
			'cursor': 'move'
		};
		mouse.fu = function () {
			return (E.sh_var ? false : true);
		};
	},
	'cache': function (n) {
		var o = marquee.shapes,
			sh = E.sh ? 1 : 0,
			ctrl = E.ctrl ? 1 : 0;
		if (vars.marquee == 'lasso') {
			o[marquee.shape = 0] = {
				'T': vars.marquee,
				'R': marquee.r,
				'sh': sh,
				'ctrl': ctrl
			};
		} else {
			o[n] = {
				'T': vars.marquee,
				'A': moXY,
				'Z': mcXY,
				'sides': vars['sides_marquee'],
				'slope': vars['slope_marquee'],
				'sh': sh,
				'ctrl': ctrl
			};
		}
	},
	'dash': function (p, c, s) {
		var c = c ? c : $2D('ctx_marquee'),
			p = p ? p : ants[ants_n = (ants_n + 1) % 4],
			lasso = (s && vars.marquee == 'lasso');
		c.globalCompositeOperation = 'source-over';
		c.clearRect(0, 0, canvas.W, canvas.H);
		c.lineWidth = lasso ? 0.5 : 1;
		marquee.draw(c, s, {
			'o': 'stroke',
			'v': p
		});
		if (!lasso) { //- replace this with actually combining the paths
			c.globalCompositeOperation = 'destination-out';
			co.style.fill(c, 'rgba(0,0,0,1)');
		}
	},
	'run': function (t, s) {
		if (!s) {
			E.run = (t = getTime());
			marquee.on = 1;
			marquee.ghost = 1;
		}
	//	marqueeID = setInterval(function() { marquee.dash(); }, 300);  
	},
	'reset': function (v, s) {
		v = v ? v : '';
		E.run = 0;
		marquee.r = '';
		moXY = v;
		mcXY = v;
		marquee.on = 0;
		window.clearInterval(marqueeID);
		marquee.ghost = 0;
		marquee.r = [];
		marquee.i = 0;
		if (!E.sh_var || !marquee.shapes || s) {
			marquee.shapes = [];
			marquee.shape = 0;
		}
		$2D('ctx_marquee').clearRect(0, 0, canvas.W, canvas.H);
		$2D('ctx_temp').clearRect(0, 0, canvas.W, canvas.H);
		marquee.cursor();
	},
	/* Data */
	'shapes': []
};

/* PICKER */

picker={
	'core':function (a, b, m) {
		var c = $2D('picker'),
			w = 129,
			h = 96,
			data = $2D('ctx_box').getImageData(Math.max(0, a.X - 1), Math.max(0, a.Y - 1), 1, 1),
			r = data.data,
			ctx = $2D('picker1x1');
		ctx.clearRect(0, 0, 1, 1);
		ctx.putImageData(data, 0, 0);
		r[3] = parseInt(r[3] / 255 * 100);
		$('picker_hex').innerHTML = r[0] + '<br>' + r[1] + '<br>' + r[2] + '<br>' + r[3];
		c.clearRect(0, 0, w + 20, h);
		c.globalCompositeOperation = 'source-over';
		var x = a.X - 7,
			y = a.Y - 3,
			xx = (x < 0 ? Math.abs(x + 1) : 0) * 10,
			yy = (y < 0 ? Math.abs(y + 1) : 0) * 10;
		//- should grab image data for area, then draw rectangles (pixels) manually... Then run the c.arc() to mask it out
		c.drawImage($('ctx_box'), Math.max(0, x), Math.max(0, y), w / 10, h / 10, 20 + xx, yy, w, h);
		c.globalCompositeOperation = 'destination-in';
		co.circle({
			'X': 45,
			'Y': -7
		},
		{
			'X': w,
			'Y': w - 58
		},
		c);
		c.fill();
		if (m == 'down') {
			r[3] = r[3] / 100;
			gui_color.run('set', r);
			gui_palette.update();
		}
	}
};


/* MOUSE */

mouse = {
    'cursor': function (e, o) {
        var r = mouse.area;
        if (r) {
            var a = XY(e);
            var d = win_size.LT();
            a.X -= abPos(o).X + d.L;
            a.Y -= abPos(o).Y + d.T;
            var getID = function () {
                var v = mouse.id.split('.'),
                n = parseInt(v[0]);
                return ({
                    'n': n,
                    'id': v[1],
                    'num': !isNaN(n)
                });
            }
            function z(v1, v2) {
                $S('cBound').cursor = v1;
                $('cZoom').innerHTML = mouse.id = v2;
                var d = $2D('ctx_active'),
                q = getID();
                co.del(d);
                if (q.id && q.id != 'A') {
                    var v = path.r[q.n + 1],
                    o = path.O2R,
                    n = o[v[0]][q.id];
                    if (n) {
                        d.beginPath();
                        d.drawImage(path[q.id == 'P' ? 'point_select' : 'node_select'], 0, 0, 7, 7, Math.round(v[n] - 4), Math.round(v[n + 1] - 4), 7, 7);
                        d.closePath();
                    }
                }
            };
            var o = $2D('ctx_mouse').getImageData(a.X,a.Y,1,1).data,
            	hex = (o[0] << 24 | o[1] << 16 | o[2] << 8 | o[3]) >>> 0,
				i = '';
            if (hex != '00000000' && mouse.fu()) {
                if ((i = r[hex]) && mouse.id != i.id) {
                    z(i.cursor, i.id);
                }
            } else if (mouse.id) {
                z('crosshair', '');
            }
        }
    },
    'draw': function (r) {
        var c = $2D('ctx_mouse'),
        fu = N.rand;
        co.del(c);
        mouse.area = {};
        for (var i in r) {
            var o = [fu(255), fu(255), fu(255), 255],
            k = r[i];
            c.beginPath();
            if (!k[2]) {
                co.rectangle(k[0], k[1], c);
            } else if (k[2] == 'ellipses') {
                co.ellipses(k[0], k[1], c);
            } else if (k[2] == 'star') {
                co.star(k[0], k[1], c);
            } else if (k[2] == 'burst') {
                co.burst(k[0], k[1], c);
            } else if (k[2] == 'gear') {
                co.gear(k[0], k[1], c);
            } else if (k[2] == 'path') {
                path.draw(c);
            }
            c.lineWidth = 2.5;
            c.strokeStyle = 'rgba(' + o + ')';
            c.stroke();
            c.fillStyle = 'rgba(' + o + ')';
            c.fill();
            mouse.area[(o[0] << 24 | o[1] << 16 | o[2] << 8 | o[3]) >>> 0] = {
                'id': i,
                'cursor': k[3] ? r[i][3] : i
            };
        }
    },
    'moveCheck': function (a, b) {
        var t = getTime() - core.time;
        return (Math.abs(a.X - b.X) <= 5 || Math.abs(a.Y - b.Y) <= 5 || (mouse.area && !mouse.id && t <= 125));
    },
    'reset': function () {
        mouse.down = 0;
        mouse.moved = 0;
    }
};

/* CROP */

crop={

	/* Keypress */
	
	'move':function(x) { mouse.id='move';
	
		var value = function(a,v) { return({X:a.X+(!isNaN(v)?v:v.X), Y:a.Y+(!isNaN(v)?v:v.Y)}); };
	
		aXY=value(aXY,x); bXY=value(bXY,x);
		
		var x=Math.abs(aXY.X-bXY.X), y=Math.abs(aXY.Y-bXY.Y), w=canvas.W, h=canvas.H;

		if(aXY.X<0) { aXY.X=0; bXY.X=x; } else if(bXY.X>w) { aXY.X=w-x; bXY.X=w; }
		if(aXY.Y<0) { aXY.Y=0; bXY.Y=y; } else if(bXY.Y>h) { aXY.Y=h-y; bXY.Y=h; }

		crop.core(aXY,bXY,'up');
	
	},
	
	/* Mouse */

	'click':function() { if(vars.type=='crop') {

		var v=$T('div',$('constrain_check'))[0].className;

		if(crop.force(vars.crop) || !v) {
		
			var d=$C('cur','aspect_radio'); if(d.length>0) { d[0].className=''; }
			
			$S('aspect_radio').opacity=0.6;
			
			gui.menu.constrain=String(vars.aspect); vars.aspect='landscape';
			
		}
		else if(v) { var o=gui.menu;
		
			if(o.constrain) { vars.aspect=String(o.constrain); o.constrain=''; }
			
			$T('div',$('aspect_radio'))[vars.aspect=='portrait'?1:0].className='cur'; $S('aspect_radio').opacity=1;

		}
		
		if(!isNaN(aXY.X) && !isNaN(bXY.X)) {

			if(vars.constrain=='true') { crop.aspect(aXY,bXY); crop.transform(aXY,bXY); }

			crop.core(aXY,bXY,'up');

		}
	} },
	'core': function (a, b, m, e) {
		if (m != 'down' && (!e || mouse.id || mouse.moved == 1 || (getTime() - core.time) <= 1250)) {
			if (!mouse.down && e) {
				if (!mouse.id) crop.reset();
				mouse.down = 1;
			} else {
				mouse.moved = 1;
			}
			var c = $2D('ctx_temp');
			x = { X: a.X, Y: a.Y };
			a = transform.xy(a);
			transform(a, b, m, e);
			var x1 = a.X + 0.5,
			x2 = b.X - 0.5,
			y1 = a.Y + 0.5,
			y2 = b.Y - 0.5;
			c.lineWidth = 1;
			c.beginPath();
			c.globalCompositeOperation = 'copy';
			c.rect(0,0,canvas.W,canvas.H);
			c.fillStyle = 'rgba(0,0,0,0.5)';
			c.fill(); // EVERYTHING
			c.beginPath();
			c.globalCompositeOperation = 'destination-out';
			c.rect(a.X, a.Y, b.X-a.X, b.Y-a.Y);
			c.fillStyle = 'rgba(0,0,0,1)';
			c.fill(); // AREA OUT
			c.beginPath();
			c.globalCompositeOperation = 'source-over';
			c.rect(x1 - 1, y1 - 1, x2 - x1 + 2, y2 - y1 + 2)
			c.strokeStyle = 'rgba(0,0,0,0.5)';
			c.stroke();
			c.beginPath();
			c.rect(x1, y1, x2 - x1, y2 - y1);
			if (m != 'up') { // DIVIDING LINES
				c.moveTo(((x2 - x1) / 3) + x1, y1);
				c.lineTo(((x2 - x1) / 3) + x1, y2); // Y1
				c.moveTo(((x2 - x1) / 3 * 2) + x1, y1);
				c.lineTo(((x2 - x1) / 3 * 2) + x1, y2); // Y2
				c.moveTo(x1, ((y2 - y1) / 3) + y1);
				c.lineTo(x2, ((y2 - y1) / 3) + y1); // X1
				c.moveTo(x1, ((y2 - y1) / 3 * 2) + y1);
				c.lineTo(x2, ((y2 - y1) / 3 * 2) + y1); // X2
			}
			c.strokeStyle = 'rgba(255,255,255,0.5)';
			c.stroke();
			c.beginPath();
			var NW = [{
				X: x1,
				Y: y1
			},
			{
				X: x1 + 8,
				Y: y1 + 8
			}],
			NE = [{
				X: x2 - 8,
				Y: y1
			},
			{
				X: x2,
				Y: y1 + 8
			}],
			SW = [{
				X: x1,
				Y: y2 - 8
			},
			{
				X: x1 + 8,
				Y: y2
			}],
			SE = [{
				X: x2 - 8,
				Y: y2 - 8
			},
			{
				X: x2,
				Y: y2
			}];
			if (Math.abs(x1 - x2) > 33 && Math.abs(y1 - y2) > 23) {
				co.rectangle({
					X: (x2 + x1) / 2 - 4,
					Y: y1
				},
				{
					X: (x2 + x1) / 2 + 4,
					Y: y1 + 8
				},
				c);
				co.rectangle({
					X: (x2 + x1) / 2 - 4,
					Y: y2 - 8
				},
				{
					X: (x2 + x1) / 2 + 4,
					Y: y2
				},
				c);
			}
			if (Math.abs(y1 - y2) > 33 && Math.abs(x1 - x2) > 23) {
				co.rectangle({
					X: x2 - 8,
					Y: (y2 + y1) / 2 - 4
				},
				{
					X: x2,
					Y: (y2 + y1) / 2 + 4
				},
				c);
				co.rectangle({
					X: x1,
					Y: (y2 + y1) / 2 - 4
				},
				{
					X: x1 + 8,
					Y: (y2 + y1) / 2 + 4
				},
				c);
			}
			if (Math.abs(x1 - x2) > 23 && Math.abs(y1 - y2) > 23) {
				co.rectangle(NW[0], NW[1], c);
				co.rectangle(NE[0], NE[1], c);
				co.rectangle(SE[0], SE[1], c);
				co.rectangle(SW[0], SW[1], c);
			}
			c.fillStyle = 'rgba(100,100,100,0.8)';
			c.fill();
			c.strokeStyle = 'rgba(255,255,255,0.5)';
			c.stroke();
			if (m == 'up') {
				mouse.reset();
				if (mouse.moveCheck(a, b)) {
					crop.reset();
				} else {
					aXY = a;
					bXY = b;
					mouse.fu = function () {
						return (true);
					};
					mouse.draw({
						'move': [a, b],
						'n-resize': [{
							X: x1 + 8,
							Y: y1
						},
						{
							X: x2 - 8,
							Y: y1 + 8
						}],
						's-resize': [{
							X: x1 + 8,
							Y: y2 - 8
						},
						{
							X: x2 - 8,
							Y: y2
						}],
						'e-resize': [{
							X: x2 - 8,
							Y: y1 + 8
						},
						{
							X: x2,
							Y: y2 - 8
						}],
						'w-resize': [{
							X: x1,
							Y: y1 + 8
						},
						{
							X: x1 + 8,
							Y: y2 - 8
						}],
						'nw-resize': [NW[0], NW[1]],
						'ne-resize': [NE[0], NE[1]],
						'se-resize': [SE[0], SE[1]],
						'sw-resize': [SW[0], SW[1]]
					});
				}
			}
		}
	},
	/* Math */
	
	'aspect':function(a,b) {
	
		if((Math.abs(a.X-b.X)>Math.abs(a.Y-b.Y)?'landscape':'portrait')!=vars.aspect) {

			crop.XY(a,b, {X:Math.abs((b.Y-a.Y)*.5), Y:Math.abs((b.X-a.X)*.5)});
			
			crop.overflow(a,b);

		}
	},
	'constrain':function(a,b) { var r=crop.ratio[gui.menu.key.crop]; //- JUNK
	
		var n=(vars.aspect=='portrait')?{A:r[1]/r[0],B:r[0]/r[1]}:{A:r[0]/r[1],B:r[1]/r[0]};

		if(mouse.id=='n-resize' || mouse.id=='s-resize') {
		
			if((b.X-a.X>0 && b.Y-a.Y>0) || (b.X-a.X<0 && b.Y-a.Y<0)) b.X=a.X+(b.Y-a.Y)*n.B; else b.X=a.X-(b.Y-a.Y)*n.B;
			
			if(b.X>canvas.W) { b.X=canvas.W/b.X*b.X; if(b.Y<a.Y) { b.Y=a.Y-(b.X-a.X)*n.A; } else { b.Y=a.Y+(b.X-a.X)*n.A; } }

			else if(a.X<0) { a.X=0; if(b.Y>a.Y) { b.Y=a.Y+(b.X-a.X)*n.A; } else { b.Y=a.Y-(b.X-a.X)*n.A; } }
			
			b.X=Math.round(b.X); b.Y=Math.round(b.Y);

		}
		else {
		
			if((b.X-a.X>0 && b.Y-a.Y>0) || (b.X-a.X<0 && b.Y-a.Y<0)) b.Y=a.Y+(b.X-a.X)*n.A; else b.Y=a.Y-(b.X-a.X)*n.A;
			
			if(b.Y>canvas.H) { b.Y=canvas.H/b.Y*b.Y; if(b.X<a.X) { b.X=a.X-(b.Y-a.Y)*n.B; } else { b.X=a.X+(b.Y-a.Y)*n.B; } }

			else if(b.Y<0) { b.Y=0; if(b.X>a.X) { b.X=a.X-(b.Y-a.Y)*n.B; } else { b.X=a.X+(b.Y-a.Y)*n.B; } }
			
			b.X=Math.round(b.X); b.Y=Math.round(b.Y);

		}
	},
	'overflow':function(a,b) { var w=b.X-a.X, h=b.Y-a.Y, r=h/w;

		var N=a.Y<0, S=b.Y>canvas.H, E=b.X>canvas.W, W=a.X<0;

		if(N || S) { if(N) { h+=a.Y; a.Y=0; } if(S) { h+=canvas.H-b.Y-1; b.Y=canvas.H; } w=h/r; }

		if(E || W) { if(W) { w+=a.X; a.X=0; } if(E) { w+=canvas.W-b.X-1; b.X=canvas.W;} h=w*r; }

		crop.XY(a,b, {X:w/2, Y:h/2});

	},
	'transform':function(a,b) { var r=crop.ratio[gui.menu.key.crop], R2=r[0]/r[1];

		var w=Math.abs(b.X-a.X), h=Math.abs(b.Y-a.Y), R1=h/w;

		if((R1<1 && R2>1) || (R1>1 && R2<1)) R2=1/R2;

		w=Math.sqrt((1/R2)*(R1*w*w));

		crop.XY(a,b, {X:w/2, Y:(R2*w)/2});
		
		crop.overflow(a,b);

	},
	'XY':function(a,b,r) { var c={X:((b.X-a.X)*.5)+a.X, Y:((b.Y-a.Y)*.5)+a.Y};

		a.X=Math.round(c.X-r.X); a.Y=Math.round(c.Y-r.Y);
		b.X=Math.round(c.X+r.X); b.Y=Math.round(c.Y+r.Y);

	},
	
	/* Visualize */
	
	'apply':function(a,b) { var c=$2D('ctx_temp'), prev={W:canvas.W, H:canvas.H}; co.del(c);

		var m=[Math.max(a.X,b.X), Math.max(a.Y,b.Y), Math.min(a.X,b.X), Math.min(a.Y,b.Y)];
	
		c.drawImage($('ctx_box'), m[2],m[3], m[0]-m[2],m[1]-m[3], 0,0, m[0]-m[2],m[1]-m[3]);

		co.del('ctx_box'); co.del('ctx_marquee');
		
		canvas.W=m[0]-m[2]; canvas.H=m[1]-m[3];
	
		$('ctx_box').width=canvas.W; $('ctx_box').height=canvas.H;
		
		$2D('ctx_box').drawImage($('ctx_temp'),0,0,prev.W,prev.H);

		crop.resize(); canvas.history_set();
	
	},
	'resize':function() { co.del('ctx_temp');
	
		var w=canvas.W, h=canvas.H, o=$C('MM','canvas')[0].style;
		
		var l=(parseInt(o.width)-canvas.W)/2, t=(parseInt(o.height)-canvas.H)/2;
	
		function fu(o) { $(o).width=w; $(o).height=h; $S(o).left=l+'px'; $S(o).top=t+'px'; }
	
		$S('ctx_box').left=l+'px'; $S('ctx_box').top=t+'px';
	
		$S('cBound').cursor='crosshair'; mouse.id=''; aXY={}; bXY={}; mouse.area='';

		var r=['ctx_temp','ctx_marquee','ctx_active','ctx_mouse'];
		
		for(var i in r) { fu(r[i]); };
		
	},

	/* Data */
	
	'force':function(v) {
	
		eval("var r={'Display ("+screen.width+"x"+screen.height+")':1,'Original ("+canvas.W+"x"+canvas.H+")':1,'4x3 (DVD)':1,'16x9 (HD)':1}");
	
		return(r[v]);
	
	},
	'ratio_mk':function() {
	
		crop.ratio={'Display':[screen.height,screen.width], 'Original':[canvas.H,canvas.W], '2x3':[2,3], '3x5':[3,5], '4x3':[4,3], '4x6':[4,6], '5x7':[5,7], '8x10':[8,10], '16x9':[9,16], '16x20':[16,20], '20x30':[20,30], 'Square':[1,1]};

	},
	'reset':function() {
	
		$2D('ctx_temp').clearRect(0,0,canvas.W,canvas.H); $S('cBound').cursor='crosshair';

		mouse.id=''; aXY={}; bXY={}; mouse.area='';

	}
};


/* TRANSFORM */

transform={}; transXY={X:0,Y:0};

transform=function(a,b,m,e) {

	if(e) { var o=transXY;

		function zarea(v) { transXY={X:0,Y:0}; $S('cBound').cursor=mouse.id=v; co.core(e); }
	
		if(mouse.id=='n-resize') { a.X=aXY.X; a.Y=bXY.Y; b.X=bXY.X; b.Y=Math.max(0,aXY.Y+(b.Y-oXY.Y));

			if(a.Y<b.Y) { var v=bXY; bXY={X:bXY.X,Y:aXY.Y-o.Y}; aXY={X:aXY.X,Y:v.Y}; zarea('s-resize'); }
	
		}
		else if(mouse.id=='ne-resize') { a.X=aXY.X; a.Y=bXY.Y; b.X=Math.min(canvas.W,bXY.X+(b.X-oXY.X)); b.Y=Math.max(0,aXY.Y+(b.Y-oXY.Y));
		
			if(a.Y<b.Y) { var v=bXY; bXY={X:bXY.X-o.X,Y:aXY.Y-o.Y}; aXY={X:aXY.X,Y:v.Y}; zarea('se-resize'); }
			
			else if(a.X>b.X) { var v=bXY; bXY={X:aXY.X,Y:bXY.Y}; aXY={X:v.X-o.X,Y:aXY.Y-o.Y}; zarea('nw-resize'); }
	
		}
		else if(mouse.id=='e-resize') { a.X=aXY.X; a.Y=aXY.Y; b.X=Math.min(canvas.W,bXY.X+(b.X-oXY.X)); b.Y=bXY.Y;
		
			if(a.X>b.X) { var v=bXY; bXY={X:aXY.X,Y:bXY.Y}; aXY={X:v.X-o.X,Y:aXY.Y}; zarea('w-resize'); }
	
		}
		else if(mouse.id=='se-resize') { a.X=aXY.X; a.Y=aXY.Y; b.X=Math.min(canvas.W,bXY.X+(b.X-oXY.X)); b.Y=Math.min(canvas.H,bXY.Y+(b.Y-oXY.Y));
		
			if(a.Y>b.Y) { var v=bXY; bXY={X:bXY.X,Y:aXY.Y}; aXY={X:aXY.X,Y:v.Y-o.Y}; zarea('ne-resize'); }
			
			else if(a.X>b.X) { var v=bXY; bXY={X:aXY.X,Y:bXY.Y}; aXY={X:v.X-o.X,Y:aXY.Y}; zarea('sw-resize'); }
	
		}
		else if(mouse.id=='s-resize') { a.X=aXY.X; a.Y=aXY.Y; b.X=bXY.X; b.Y=Math.min(canvas.H,bXY.Y+(b.Y-oXY.Y));
	
			if(a.Y>b.Y) { var v=bXY; bXY={X:bXY.X,Y:aXY.Y}; aXY={X:aXY.X,Y:v.Y-o.Y}; zarea('n-resize'); }
	
		}
		else if(mouse.id=='sw-resize') { a.X=bXY.X; a.Y=aXY.Y; b.X=Math.max(0,aXY.X+(b.X-oXY.X)); b.Y=Math.min(canvas.H,bXY.Y+(b.Y-oXY.Y));
		
			if(a.Y>b.Y) { var v=bXY; bXY={X:bXY.X,Y:aXY.Y}; aXY={X:aXY.X-o.X,Y:v.Y-o.Y}; zarea('nw-resize'); }
			
			else if(a.X<b.X) { var v=bXY; bXY={X:aXY.X-o.X,Y:bXY.Y-o.Y}; aXY={X:v.X,Y:aXY.Y}; zarea('se-resize'); }
		
		}
		else if(mouse.id=='w-resize') { a.X=bXY.X; a.Y=aXY.Y; b.X=Math.max(0,aXY.X+(b.X-oXY.X)); b.Y=bXY.Y;
		
			if(a.X<b.X) { var v=bXY; bXY={X:aXY.X-o.X,Y:bXY.Y}; aXY={X:v.X,Y:aXY.Y}; zarea('e-resize'); }
		
		}
		else if(mouse.id=='nw-resize') { a.X=bXY.X; a.Y=bXY.Y; b.X=Math.max(0,aXY.X+(b.X-oXY.X)); b.Y=Math.max(0,aXY.Y+(b.Y-oXY.Y));
		
			if(a.Y<b.Y) { var v=bXY; bXY={X:bXY.X-o.X,Y:aXY.Y-o.Y}; aXY={X:aXY.X,Y:v.Y}; zarea('sw-resize'); }
			
			else if(a.X<b.X) { var v=bXY; bXY={X:aXY.X-o.X,Y:bXY.Y}; aXY={X:v.X,Y:aXY.Y}; zarea('ne-resize'); }
	
		}
		else if(mouse.id=='move') {
		
			var W=bXY.X-aXY.X, H=bXY.Y-aXY.Y;
	
			var aA=Math.max(0,aXY.X+(b.X-oXY.X)), bA=Math.max(0,aXY.Y+(b.Y-oXY.Y)); // MIN
			
			var aB=aA-Math.max(0,aA+W-canvas.W), bB=bA-Math.max(0,bA+H-canvas.H); // MAX
			
			a.X=aB; a.Y=bB; b.X=W+aB; b.Y=H+bB;
	
		}
		else {
	
			b.X=Math.min(canvas.W,Math.max(0,b.X)); b.Y=Math.min(canvas.H,Math.max(0,b.Y));
	
		}
	}

	if(vars.constrain=='true') { crop.constrain(a,b); }
		
	if(m=='down') { transXY={X:b.X-oXY.X,Y:b.Y-oXY.Y}; }

	if(a.X-b.X>0) { var t=parseInt(a.X); a.X=parseInt(b.X); b.X=parseInt(t); }
	if(a.Y-b.Y>0) { var t=parseInt(a.Y); a.Y=parseInt(b.Y); b.Y=parseInt(t); }
		
};

transform.xy=function(o,n) { n=n?n:0;
	
		return({X:Math.max(0+n,Math.min(canvas.W-n,o.X)), Y:Math.max(0+n,Math.min(canvas.H-n,o.Y))});
	
};// DRAW

style = function(c, style, type, a, b) {
	switch(vars[type]) {
		case "solid":
			c[style] = "rgba("+vars[type+'CO'].join(",")+")";
			break;
		case "gradient":
			var r = vars[type+'GD'],
				gradient = c.createLinearGradient(a.X, a.Y, b.X, b.Y);
			for (var key in r) {
				gradient.addColorStop(r[key][0], 'rgba(' + r[key][1].join(",") + ')');
			}
			c[style] = gradient;
			break;
		case "pattern":
			if(!vars[type+'PT~']) {
				gui_pattern.cache(type);
			}
			c[style] = vars[type+'PT~'];
			break;
	}
};

(function() {

// helper functions

function createFlow(b, type, callback) {
	var oX = b.X - cXY.X,
		oY = b.Y - cXY.Y,
		flow = Math.max(1, 100 - vars['flow_' + type]);
	function run(a, b, n) {
		var i = n / flow;
		if (n > 0) {
			for (; i > 0; i--) {
				callback(a * i, b * i);
			}
		} else {
			for (; i < 0; i++) {
				callback(a * i, b * i);
			}
		}
	};
	if (Math.abs(oX) > Math.abs(oY)) {
		run(flow, flow * (oY / oX), oX);
	} else {
		run(flow * (oX / oY), flow, oY);
	}
};

function mask_active(m) {
	var c = $2D('ctx_temp'),
		type = vars.type == 'shape' ? vars.shape : vars.type;
	c.globalCompositeOperation = canvas.mode;
	if (vars['movement_' + type] == 'anchored') {
		c.clearRect(0, 0, canvas.W, canvas.H);
	}
	if (m == 'down' && marquee.on) {
		c.save();
		c.beginPath();
		marquee.draw(c);
		c.clip();
		marquee.on = 0;
		window.clearInterval(marqueeID);
	}
	return c;
};

function mask_up(c) {
	var ctx_temp = document.getElementById('ctx_temp').getContext('2d');
	co.copy('ctx_temp', 'ctx_box');
	ctx_temp.clearRect(0, 0, canvas.W, canvas.H);
	canvas.history_set();
	if (marquee.ghost) {
		marquee.run();
		c.restore();
	}
	moved = false;
};

var moved = false;

// tools

draw = {	
	shape: function (a, b, m) {
		var c = mask_active(m);
		if (vars['movement_' + vars.shape] == 'freedraw') {
			oXY = b;
		}
		if (Math.abs(a.X - b.X) > 0 && Math.abs(a.Y - b.Y) > 0) {
			moved = true;
			c.beginPath();
			co[vars.shape](a, b, c);
			c.lineJoin = vars.lineJoin;
			c.lineWidth = vars['stroke_' + vars.shape];
			style(c, "fillStyle", "fill", a, b);
			style(c, "strokeStyle", "stroke", a, b);
			c.fill();
			c.stroke();
		}
		if (m == 'up' && moved) {
			mask_up(c);
		}
	},
	text: function(a, b, m) {
		var c = mask_active(m);
		// NOTES: Check into scale.
		if(m == "down" || m == "move") {
			c.beginPath();
			if(c.setFont) c.setFont(vars.fontSize + "px Liberation Sans");
			c.font = vars.fontSize + "px Liberation Sans, sans-serif";
			c.lineWidth = vars.stroke_text;
			style(c, "strokeStyle", "stroke", a, b);
			style(c, "fillStyle", "fill", a, b);
			c.fillText(vars.textMessage, b.X, b.Y);
			c.strokeText(vars.textMessage, b.X, b.Y);
		}
		if(m == "up") {
			mask_up(c);
		}
	},
	pencil: function (a, b, m) {
		var c = mask_active(m),
			d = $('ctx_brush'),
			D = vars.diameter_pencil,
			D2 = D * 2;
		function z(x, y) {
			c.drawImage(d, 0, 0, D2, D2, b.X - D - x, b.Y - D - y, D2, D2);
		};
		if (m == 'down') {
			z(0, 0);
		} else if (m == 'move') {
			createFlow(b, 'pencil', z);
		}
		if(m == "up") { 
			mask_up(c);
		}
	},
	brush: function (a, b, m) {
		var c = mask_active(m),
			d = $('ctx_brush'),
			D = vars.diameter_brush,
			D2 = D * 2;
		function z(x, y) {
			c.drawImage(d, 0, 0, D2, D2, b.X - D - x, b.Y - D - y, D2, D2);
		};
		if (m == 'down') {
			z(0, 0);
		} else if (m == 'move') {
			createFlow(b, 'brush', z);
		}
		if(m == "up") { 
			mask_up(c);
		}
	},
	calligraphy: function (a, b, m) {
		var c = mask_active(m),
			d = $('ctx_stamp'),
			r = co.stamp.r,
			D = vars.diameter_calligraphy / 100,
			D2 = D * 2;
		function z(x, y) {
			c.drawImage(d, 0, 0, r.W, r.H, b.X - ((r.W * D) / 2) - x, b.Y - ((r.H * D) / 2) - y, r.W * D, r.H * D);
		};
		if (m == 'down') {
			z(0, 0);
		} else if (m == 'move') {
			createFlow(b, 'calligraphy', z);
		}
		if(m == "up") { 
			mask_up(c);
		}
	},
	stamp: function (a, b, m) {
		var c = mask_active(m),
			d = $('ctx_stamp'),
			r = co.stamp.r,
			D = vars.diameter_stamp,
			D2 = D * 2;
		function z(x, y) {
			var n = Math.random(),
				zoom = Math.max(vars.rand_max / 100, Math.min(vars.rand_min / 100, n));
			c.drawImage(d, 0, 0, r.W, r.H, b.X - ((r.W * zoom) / 2) - x, b.Y - ((r.H * zoom) / 2) - y, r.W * zoom, r.H * zoom);
		};
		if (m == 'down') {
			z(0, 0);
		} else if (m == 'move') {
			createFlow(b, 'stamp', z);
		}
		if(m == "up") { 
			mask_up(c);
		}
	},
	eraser: function (a, b, m) {
		if(m == "down") {
			co.copy('ctx_box', 'ctx_temp');
			co.del('ctx_box');
		}
		var c = mask_active(m),
			D = vars.diameter_eraser,
			D2 = D * 2;
		function z(x, y) {
			c.drawImage($('ctx_brush'), 0, 0, D2, D2, b.X - D - x, b.Y - D - y, D2, D2);
		};
		if (m == 'down') {
			c.globalCompositeOperation = 'destination-out';
			z(0, 0);
		} else {
			c.globalCompositeOperation = 'destination-out';
			createFlow(b, 'eraser', z);
		}
		c.globalCompositeOperation = 'source-over';
		if(m == "up") { 
			mask_up(c);
		}
	},
	fill: function (a, b, m) {
		var c = mask_active(m),
			type = vars.fill;
		if (type == 'gradient') {
			c.beginPath();
			c.lineWidth = 0.5;
			c.lineCap = 'round';
			c.moveTo(a.X, a.Y);
			c.lineTo(b.X, b.Y);
			c.strokeStyle = 'rgba(127,127,127,1)';
			c.stroke();
			c.drawImage(path['point'], 0, 0, 7, 7, a.X - 4, a.Y - 4, 7, 7);
			c.drawImage(path['node_select'], 0, 0, 7, 7, b.X - 4, b.Y - 4, 7, 7);
			if(m == "up") {	
				co.del('ctx_temp');
				style(c, "fillStyle", "fill", a, b);
				c.rect(0, 0, canvas.W, canvas.H);
				c.fill();
				mask_up(c);
			}
		} else if(m == "down") {
			style(c, "fillStyle", "fill", a, b);
			c.rect(0, 0, canvas.W, canvas.H);
			c.fill();
			mask_up(c);
		}
	}
};

// STAMP

stamp = { // Mouse Events
	current: function (o) {
		var b = stamp.fileNumber;
		stamp.fileNumber = o.id.substr(5);
		if ($('stamp' + b)) {
			stamp.preview(b);
			$('stamp' + b).className = '';
		}
		$('stamp' + stamp.fileNumber).className = 'cur';
		vars.cache(1);
	},
	uri: function (v) {
		return 'media/glyph/' + vars.stamp + '/' + (stamp.fileNumber - 1) + '-' + v + '.png';
	},
	preview: function (i, m) {
		if ($('stamp' + i) && stamp.src[i].src) {
			function O(n) {
				return (n < 34 ? (34 - n) / 2 : 0);
			}
			function Z(n) {
				o = {
					W: (34 / n) * o.W,
					H: (34 / n) * o.H
				};
			}
			var c = $2D('stamp' + i),
				d = stamp.src[i],
				o = {
					W: d.width,
					H: d.height
				};
			if (o.W >= o.H) Z(o.W);
			else if (o.H > o.W) Z(o.H);
			c.clearRect(0, 0, 34, 34);
			c.drawImage(d, O(o.W), O(o.H), o.W, o.H);
			if (i == stamp.fileNumber) {
				c.globalCompositeOperation = 'source-in';
				c.rect(0, 0, 34, 34);
				co.style[vars[vars.id]]({
					'X': 0,
					'Y': 0
				},
				{
					'X': 34,
					'Y': 34
				},
				c, 'fill', vars.id);
				c.globalCompositeOperation = 'source-over';
				if (!m || m == 'up') {
					co.glyph(stamp.uri('live'));
				}
			}
		}
	},
	reset: function () {
		var o = gui.Y,
			r = o.r.stamp;
		if (!stamp.fileNumber) {
			stamp.fileNumber = 1;
		}
		var a = parseInt(stamp.fileNumber),
			b = stamp.r[vars.stamp],
			i = r.n() - r.display,
			n = (a / b <= 0.5) ? -2 : 2;
		o.prev.stamp = null;
		o.cur.stamp = Math.max(1, Math.min(i, Math.floor(((a + n) / b) * i)));
		o.id = 'stamp';
		o.stamp();
		o.kontrol_update(o.id);
		gui.X.range('rand');
	},
	r: {
		'Default': 1,
		'Butterflies': 27,
		'Doodles': 11,
		'Flowers': 28,
		'Footprints': 18,
		'Leafs': 50,
		'Light': 15,
		'Retro': 30,
		'Simple Smudges': 14,
//		'Splatter': 35,
		'Tizzape': 20,
		'Typogrunge': 24,
		'Water Colors': 17
	},
	src: []
};

// Spirograph

(function() {

var interval, 
	o = { };

draw.spirograph = function (a, b, m) {
	var c = mask_active(m);
	if (m == 'move') {
		o = b;
	} else if (m == 'up') {
		clearTimeout(interval);
		mask_up(c);
	} else if (m == 'down') {
		o = b;
		var	R = vars.inner_radius_spirograph, // Fixed circle
			r = vars.outer_radius_spirograph, // Moving circle
			d = vars.diameter_spirograph, // Distance
			speed = vars.speed_spirograph,
			resolution = vars.resolution_spirograph,
			stroke = 0.5,
			type = vars.type_spirograph,
			theta = 0,
			strokeStyle = vars["stroke" + gui_swatch.L2S[vars["stroke"]]]; 
		switch(vars["stroke"]) { // compile styles
			case "pattern": 
				c.strokeStyle = vars["stroke"+'PT~'];
				break;
			case "gradient": 
				var Additive = function(A, B) {
						var C = A + B;
						return (C > 0xFF) ? 0xFF : C;
					},
					gradient = vars["stroke" + "GD"],
					prev,
					cur,
					spectrum = [ ];
				for (var key in gradient) {
					cur = gradient[key][1];
					if (prev) {
						var len = 512 / (gradient.length - 1);
						for (var j = 0; j <= len; j++) {
							var n1 = (j / len),
								n2 = 1 - n1,
								color = [
									cur[0] + (Additive(cur[0], prev[0]) - cur[0]) * prev[3] / 0xFF,
									cur[1] + (Additive(cur[1], prev[1]) - cur[1]) * prev[3] / 0xFF,
									cur[2] + (Additive(cur[2], prev[2]) - cur[2]) * prev[3] / 0xFF,
									Math.min(cur[3] * 0xFF + prev[3] * 0xFF, 0xFF)
								];
							spectrum.push(cur.join(","));
						}
					}
					prev = cur;
				}
				break;
			default: // color
				c.strokeStyle = "rgba(" + strokeStyle.join(",") + ")";;
				break;
		}
		interval = setInterval(function() {
			var _x, // previous line
				_y,
				_oX = o.X, // previous mouse position
				_oY = o.Y;
			c.lineWidth = stroke;
			for (var n = 0; n < speed; n++) {
				if (type == 'Hypotrochoid') {
					x = (R - r) * Math.cos(theta) + d * Math.cos((R / r - 1) * theta) + (_oX + (o.X - _oX) * (n / speed)); // Hypotrochoid
					y = (R - r) * Math.sin(theta) - d * Math.sin((R / r - 1) * theta) + (_oY + (o.Y - _oY) * (n / speed));
				} else {
					x = (R + r) * Math.cos(theta) - d * Math.cos((R / r + 1) * theta) + (_oX + (o.X - _oX) * (n / speed)); // Epitrochoid
					y = (R + r) * Math.sin(theta) - d * Math.sin((R / r + 1) * theta) + (_oY + (o.Y - _oY) * (n / speed));
				}
				if (!isNaN(_x)) {
					c.beginPath();
					c.moveTo(_x, _y);
					c.lineTo(x, y);
					if (vars["stroke"] == 'gradient') {
						var current = (((theta * Math.abs(1 - R / r) / Math.PI / 2) % 1) * 512) >> 0;
						c.strokeStyle = 'rgba(' + spectrum[current] + ')';
					}
					c.stroke();
				}
				theta += resolution * Math.PI / 180 * 2;
				_x = x;
				_y = y;
			}
			_oX = o.X;
			_oY = o.Y;
		}, 5);
	}
};

})();

})();
/*
	Resources Database
*/

/*

Canvas2D.vars = {
	marquee: {
		movement: 10
	},
	stroke: {
		pattern:
		gradient:
		color:
	},
	fill: {
		pattern:
		gradient:
		color:
	}
};

Resource = {

	// default databases

	brush: { // source:  /media/brush/name.png
		'Butterflies': { //ps7_butterfly_brushes_by_dead_brushes.abr
			author: 'Dead-Brushes',
			url: 'http://dead-brushes.deviantart.com/art/ps7-butterfly-brushes-61623697',
			email: 'bulletproof.cupidx@gmail.com',
			type: "Library", // set of elements organized by the creator
			data: [ // can be array, or integer (see below)
				'green pea',
				'yellow squash'
			]
		},
		'Randomizer': { // modulate within a custom collection
			type: "Collection",  // set of elements organized by a user (contains parts of multiple libaries)
			data: [
				'Butterflies.greenpea',
				'Butterflies.yellow squash'
			]
		}
	},
	pattern: { // source:  /media/pattern/name.filetype
		'Ava7': { 
			author: 'Ava7',
			url: 'http://patterns.ava7.com/',
			email: 'domains.hr@gmail.com',
			type: "Library",
			data: 41 // there are 41 numbered patterns in /media/pattern/Ava7/
		}	
	},
	gradient: {  // source:  /media/gradient/name.ogr
		'Web v2.0': { 
			author: 'Navdeep & Navin',
			url: 'http://gimp-tutorials.net/30-Ultimate-Web-20%20Layer-Styles-for%20-Gimp/',
			email: 'domains.hr@gmail.com',
			type: "Library",
			data: { // can be object or array
			  'golden-yellow': [ 0, 0xFFFFD700, 0.5, 1, 0xFFFFFFFF ],
			  'green': [ 0, 0xFF008000, 0.5, 1, 0xFFFFFF ],
			  'indigo-orange': [ 0, 0xFF4B0082, 0.5, 1, 0xFFFFA500 ],
			  'indigo': [ 0, 0xFF4B0082, 0.5, 1, 0xFFFFFF ]
			}
		}	
	},
	color: {  // source:  /media/color/name.opl
		'Oxygen': { 
			author: 'KDE',
			url: 'http://techbase.kde.org/Development/Guidelines/CIG/Colors',
			email: 'domains.hr@gmail.com',
			type: "Library",
			data: { // can be object or array
				'Almond': 0xEED9C4, 
				'Antique Brass': 0xC88A65, 
				'Apricot': 0xFDD5B1, 
				'Aquamarine': 0x71D9E2, 
				'Asparagus': 0x7BA05B
			}
		}
	},

	// keep multiple versions in RAM for quick switching (think randomizer on active mode)

	__brush: {
		'Butterflies': { // is an object, so it's easy to do:  delete Resource.__brush.Butterflies['green pea'];
			'green pea': createPattern(),
			'yellow squash': createPattern()
		},
		'Randomizer': { // 
			'green pea': createPattern(),
			'yellow squash': createPattern()
		}
	},
	__pattern: {
		'Gaudi': { // would have at least two active (unless both stroke/fill were the same pattern)
			'0': createPattern(),
			'1': createPattern()
		}
	},
	__gradient: {
		'Nature': {
			'0': createLinearGradient() // or createRadialGradient() depending on what they have set
		}
	},
	__color: {
		'Fruity': {
			'0': 'rgba(123,45,67,1)'
		}
	}
};

*/

Resources = { 
	'Brushes': { 
		'Butterflies': { //ps7_butterfly_brushes_by_dead_brushes.abr
			'email': 'bulletproof.cupidx@gmail.com',
			'url': 'http://dead-brushes.deviantart.com/art/ps7-butterfly-brushes-61623697',
			'name': 'dead-brushes'
		},
		'Doodles': { //punksafetypin_set21_boneydoodles.abr
			'email': 'punksafetypin@gmail.com',
			'url': 'http://punksafetypin.deviantart.com/art/Brush-Set-21-Boney-Doodles-57365483',
			'name': 'punksafetypin'
		},
		'Flowers': { //flowers1_brushes_by_hawksmont.abr
			'email': 'ir@hawksmont.com',
			'url': 'http://hawksmont.com/',
			'name': 'hawksmont'
		},
		'Footprints': { //Chain - Footprints.abr
			'email': 'jonas@rognemedia.no',
			'url': 'http://chain.deviantart.com/art/Footprint-brushes-60108797',
			'name': 'chain'
		},
		'Leafs': { //leaves_brushes_by hawksmont.abr
			'email': 'ir@hawksmont.com',
			'url': 'http://hawksmont.com/',
			'name': 'hawksmont'
		},
		'Retro': { //retro1.abr
			'email': 'vivekdhage@gmail.com',
			'url': 'http://vwake.deviantart.com/art/Retr0-Brushes-1689219',
			'name': 'vwake'
		},
		'Simple Smudges': { //SimpleSmudges__1__Brushes_Pack_by_env1ro.abr
			'email': 'p.szczepanski@gmail.com',
			'url': 'http://env1ro.deviantart.com/art/SimpleSmudges-1-Brushes-Pack-52089760',
			'name': 'env1ro'
		},
/*		'Splatter': { //SM_SplatterisM1_Low.abr
			'email': 'dan@fictionalhead.com',
			'url': 'http://smashmethod.deviantart.com/art/SM-splatterisM-1-ps7-repack-10351757',
			'name': 'smashmethod'
		},
*/
		'Tizzape': { //tizzape_brushes.abr
			'email': 'services@dirt2.com',
			'url': 'http://keepwaiting.deviantart.com/art/Tizzape-Tape-Brushes-29459997',
			'name': 'keepwaiting'
		},
		'Typogrunge': { //JS_Scully7491_Typogrungebrushessm.abr
			'email': '',
			'url': 'http://scully7491.deviantart.com/art/Typographic-Grunge-Brushes-30565921',
			'name': 'scully7491'
		},
		'Water Colors': { //watercolors_env1ro.abr
			'email': 'p.szczepanski@gmail.com',
			'url': 'http://env1ro.deviantart.com/art/WaterColor-Reloaded-98294189',
			'name': 'env1ro'
		}
	},
	'PT': { 
		'Ava7': { 
			'email': 'domains.hr@gmail.com',
			'url': 'http://patterns.ava7.com/',
			'name': 'Ava7'
		},
		'DinPattern': { 
			'email': 'http://www.dinpattern.com/pages/contact.php',
			'url': 'http://www.dinpattern.com/',
			'name': 'DinPattern'
		},
		'Gaudi': { 
			'email': 'http://www.donbarnett.com/mail/index.php3',
			'url': 'http://www.donbarnett.com/tilesets/set3.htm',
			'name': 'Don Barnett'
		},
		'Headlock': { 
			'email': 'dns@jpmax.net',
			'url': 'http://www.headlock.ws/patterns.html',
			'name': 'Headlock'
		},
		'HG - Patterns': { 
			'email': 'hybridgenesis@gmail.com',
			'url': 'http://www.hybrid-genesis.com/freebies.html',
			'name': 'Hybrid Genesis'
		},
		'HGX - Patterns': { 
			'email': 'hybridgenesis@gmail.com',
			'url': 'http://www.hybrid-genesis.com/hgx_intro.html',
			'name': 'Hybrid Genesis'
		},
		'Squidfingers': { 
			'email': 'Travis@squidfingers.com',
			'url': 'http://www.squidfingers.com/patterns/',
			'name': 'Travis'
		}
	},
	'GD': { 
		'Web v2.0': { 
			'email': '',
			'url': 'http://gimp-tutorials.net/30-Ultimate-Web-20%20Layer-Styles-for%20-Gimp',
			'name': 'Navdeep & Navin'
		},
		'Web v2.1': { 
			'email': '',
			'url': 'http://gimp-tutorials.net/130-UltimateWeb20-Gradients-for-Gimp',
			'name': 'Navdeep & Navin'
		}
	},
	'CO': { 
		'Oxygen': { 
			'email': '',
			'url': 'http://techbase.kde.org/Development/Guidelines/CIG/Colors',
			'name': 'KDE'
		},
		'Cranes': { 
			'email': '',
			'url': 'http://Carol.gimp.org/gimp2/resources/default/palettes.html',
			'name': 'Carol'
		},
		'Grays': { 
			'email': '',
			'url': 'http://Carol.gimp.org/gimp2/resources/default/palettes.html',
			'name': 'Carol'
		},
		'Named Colors': { 
			'email': '',
			'url': 'http://Carol.gimp.org/gimp2/resources/default/palettes.html',
			'name': 'Carol'
		},
		'Pastels': { 
			'email': '',
			'url': 'http://Carol.gimp.org/gimp2/resources/default/palettes.html',
			'name': 'Carol'
		},
		'Reds and Purples': { 
			'email': '',
			'url': 'http://Carol.gimp.org/gimp2/resources/default/palettes.html',
			'name': 'Carol'
		},
		'Web': { 
			'email': '',
			'url': 'http://Carol.gimp.org/gimp2/resources/default/palettes.html',
			'name': 'Carol'
		},
		'Fruity': { 
			'email': 'mud@visc.us',
			'url': 'http://mudcu.be/',
			'name': 'MUD'
		}
	}
};

/* GRADIENTS */

Q={}; Q.GD={};

Q.GD['Nature']=[

	[[0,[86,3,0,1]],[0.11,[153,0,0,1]],[0.36,[205,112,19,1]],[0.7,[219,181,82,1]],[1,[115,94,41,1]]],
	[[0,[0,0,0,1]],[1,[153,0,0,1]]],
	[[0,[41,68,99,1]],[0.57,[254,221,144,1]],[1,[41,68,99,1]]],
	[[0,[39,73,105,1]],[0.27,[98,122,155,1]],[0.61,[156,171,204,1]],[1,[215,220,254,1]]],
	[[0,[40,66,82,1]],[0.09,[206,213,254,1]],[0.28,[41,68,99,1]],[0.39,[65,79,92,1]],[0.57,[119,151,215,1]],[0.68,[70,103,145,1]],[0.81,[89,122,157,1]],[0.91,[41,68,99,1]],[1,[2,33,54,1]]],
	[[0,[87,42,0,1]],[0.2,[69,46,2,1]],[0.66,[190,160,88,1]],[1,[142,121,64,1]]],
	[[0,[177,128,152,1]],[0.36,[95,52,71,1]],[0.36,[99,58,75,1]],[1,[248,210,236,1]]],
	[[0,[139,9,3,1]],[1,[254,221,144,1]]],
	[[0,[139,9,3,1]],[1,[234,85,78,1]]],
	[[0,[16,40,65,1]],[1,[89,122,157,1]]],
	[[0,[168,183,240,1]],[0.32,[214,163,126,1]],[1,[120,124,148,1]]],
	[[0,[254,248,188,1]],[1,[254,228,170,1]]],
	[[0,[52,7,1,1]],[1,[177,128,152,1]]],
	[[0,[95,52,71,1]],[0.5,[254,246,185,1]],[1,[95,52,71,1]]],
	[[0,[136,113,86,1]],[1,[229,205,152,1]]],
	[[0,[205,135,85,1]],[1,[89,38,12,1]]],
	[[0,[83,58,7,1]],[1,[254,241,185,1]]],
	[[0,[33,43,52,1]],[1,[198,167,127,1]]],
	[[0,[2,33,54,1]],[0.11,[57,23,36,1]],[0.24,[95,52,71,1]],[0.6,[40,66,82,1]],[1,[2,33,54,1]]],
	[[0,[142,121,64,1]],[1,[41,68,99,1]]]]

Q.GD['Web v2.0']=[
	
	[[0,[16,16,16,1]],[1,[124,125,124,1]]],
	[[0,[43,43,43,1]],[0.51,[1,1,1,1]],[0.51,[61,61,61,1]],[1,[77,77,77,1]]],
	[[0,[128,224,248,1]],[1,[205,248,255,1]]],
	[[0,[65,151,238,1]],[1,[121,187,255,1]]],
	[[0,[66,145,219,1]],[0.13,[75,184,240,1]],[0.62,[58,134,197,1]],[0.62,[99,154,200,1]],[1,[205,219,232,1]]],
	[[0,[191,110,78,1]],[0.29,[118,34,1,1]],[0.54,[118,34,1,1]],[0.54,[141,53,18,1]],[1,[240,183,160,1]]],
	[[0,[109,0,25,1]],[1,[168,3,41,1]]],
	[[0,[255,116,0,1]],[1,[255,116,0,1]]],
	[[0,[254,191,5,1]],[1,[255,214,93,1]]],
	[[0,[202,38,0,1]],[0.18,[255,102,0,1]],[0.18,[255,101,0,1]],[0.59,[246,41,12,1]],[0.59,[233,116,96,1]],[1,[241,186,176,1]]],
	[[0,[225,233,160,1]],[1,[234,239,181,1]]],
	[[0,[171,220,40,1]],[1,[182,224,38,1]]],
	[[0,[63,76,107,1]],[1,[96,107,136,1]]],
	[[0,[98,145,192,1]],[1,[204,230,249,1]]],
	[[0,[128,171,203,1]],[1,[206,224,236,1]]],
	[[0,[215,236,251,1]],[0.29,[138,195,235,1]],[0.54,[138,195,235,1]],[0.54,[171,211,238,1]],[1,[234,241,246,1]]],
	[[0,[125,227,31,1]],[0.25,[160,240,38,1]],[1,[199,254,48,1]]],
	[[0,[125,227,31,1]],[1,[166,242,39,1]]],
	[[0,[255,123,215,1]],[0.18,[253,137,215,1]],[0.54,[253,137,215,1]],[0.54,[251,167,225,1]],[1,[251,231,251,1]]],
	[[0,[251,224,151,1]],[0.27,[248,182,3,1]],[0.56,[248,182,3,1]],[0.56,[252,205,79,1]],[1,[252,234,185,1]]],
	[[0,[255,126,2,1]],[0.51,[255,124,0,1]],[0.51,[255,167,61,1]],[1,[255,160,61,1]]],
	[[0,[169,73,164,1]],[1,[229,112,231,1]]],
	[[0,[216,49,162,1]],[0.51,[169,0,119,1]],[0.51,[193,70,161,1]],[1,[193,65,164,1]]],
	[[0,[198,232,250,1]],[1,[235,247,253,1]]],
	[[0,[209,20,20,1]],[1,[254,27,1,1]]],
	[[0,[234,58,42,1]],[0.18,[216,41,21,1]],[0.54,[246,41,12,1]],[0.54,[238,85,67,1]],[1,[254,88,55,1]]],
	[[0,[209,211,96,1]],[1,[229,230,149,1]]],
	[[0,[255,228,0,1]],[0.57,[255,136,0,1]],[1,[230,109,29,1]]],
	[[0,[241,244,246,1]],[0.51,[216,223,227,1]],[0.51,[229,235,238,1]],[1,[246,248,249,1]]],
	[[0,[252,181,54,1]],[1,[239,228,91,1]]]];

Q.GD['Web v2.1']=[
	
	[[0,[76,76,76,1]],[0.25,[102,102,102,1]],[0.5,[44,44,44,1]],[0.5,[0,0,0,1]],[0.76,[43,43,43,1]],[1,[19,19,19,1]]],
	[[0,[135,224,253,1]],[1,[5,171,224,1]]],
	[[0,[240,249,255,1]],[1,[161,219,255,1]]],
	[[0,[122,188,255,1]],[1,[64,150,238,1]]],
	[[0,[0,183,234,1]],[1,[0,158,195,1]]],
	[[0,[136,191,232,1]],[1,[112,176,224,1]]],
	[[0,[254,255,255,1]],[1,[160,216,239,1]]],
	[[0,[37,141,200,1]],[1,[37,141,200,1]]],
	[[0,[64,150,238,1]],[1,[64,150,238,1]]],
	[[0,[184,225,252,1]],[0.25,[144,186,228,1]],[0.5,[144,191,240,1]],[0.5,[107,168,229,1]],[1,[189,243,253,1]]],
	[[0,[59,103,158,1]],[0.5,[43,136,217,1]],[0.5,[32,124,202,1]],[1,[125,185,232,1]]],
	[[0,[109,179,242,1]],[0.5,[84,163,238,1]],[0.5,[54,144,240,1]],[1,[30,105,222,1]]],
	[[0,[235,241,246,1]],[0.5,[171,211,238,1]],[0.5,[137,195,235,1]],[1,[213,235,251,1]]],
	[[0,[228,245,252,1]],[0.5,[191,232,249,1]],[0.5,[159,216,239,1]],[1,[42,176,237,1]]],
	[[0,[206,219,233,1]],[0.5,[97,153,199,1]],[0.5,[58,132,195,1]],[0.71,[75,184,240,1]],[1,[38,85,139,1]]],
	[[0,[167,199,220,1]],[1,[133,178,211,1]]],
	[[0,[63,76,107,1]],[1,[63,76,107,1]]],
	[[0,[208,228,247,1]],[0.5,[10,119,213,1]],[1,[135,188,234,1]]],
	[[0,[225,255,255,1]],[0.12,[225,255,255,1]],[0.12,[253,255,255,1]],[0.54,[200,238,251,1]],[1,[177,216,245,1]]],
	[[0,[179,220,237,1]],[0.5,[41,184,229,1]],[1,[188,224,238,1]]],
	[[0,[213,206,166,1]],[1,[183,173,112,1]]],
	[[0,[240,183,161,1]],[0.5,[140,51,16,1]],[0.5,[117,34,1,1]],[1,[191,110,78,1]]],
	[[0,[169,3,41,1]],[1,[109,0,25,1]]],
	[[0,[254,252,234,1]],[1,[241,218,54,1]]],
	[[0,[180,221,180,1]],[0.33,[82,177,82,1]],[0.67,[0,138,0,1]],[1,[0,36,0,1]]],
	[[0,[205,235,142,1]],[1,[165,201,86,1]]],
	[[0,[201,222,150,1]],[1,[57,130,53,1]]],
	[[0,[248,255,232,1]],[1,[183,223,45,1]]],
	[[0,[169,219,128,1]],[1,[150,197,111,1]]],
	[[0,[180,227,145,1]],[0.5,[97,196,25,1]],[1,[180,227,145,1]]],
	[[0,[41,154,11,1]],[1,[41,154,11,1]]],
	[[0,[143,200,0,1]],[1,[143,200,0,1]]],
	[[0,[0,110,46,1]],[1,[0,110,46,1]]],
	[[0,[107,186,112,1]],[1,[107,186,112,1]]],
	[[0,[205,235,139,1]],[1,[205,235,139,1]]],
	[[0,[143,196,0,1]],[1,[143,196,0,1]]],
	[[0,[182,224,38,1]],[1,[171,220,40,1]]],
	[[0,[157,213,58,1]],[0.5,[161,213,79,1]],[0.5,[128,194,23,1]],[1,[124,188,10,1]]],
	[[0,[230,240,163,1]],[0.5,[210,230,56,1]],[0.5,[195,216,37,1]],[1,[219,240,67,1]]],
	[[0,[191,210,85,1]],[0.5,[142,185,42,1]],[0.5,[114,170,0,1]],[1,[158,203,45,1]]],
	[[0,[180,223,91,1]],[1,[180,223,91,1]]],
	[[0,[238,238,238,1]],[1,[204,204,204,1]]],
	[[0,[206,220,231,1]],[1,[89,106,114,1]]],
	[[0,[96,108,136,1]],[1,[63,76,107,1]]],
	[[0,[176,212,227,1]],[1,[136,186,207,1]]],
	[[0,[242,245,246,1]],[1,[200,215,220,1]]],
	[[0,[216,224,222,1]],[0.33,[153,175,171,1]],[0.67,[130,157,152,1]],[1,[14,14,14,1]]],
	[[0,[181,189,200,1]],[1,[40,52,59,1]]],
	[[0,[184,198,223,1]],[1,[109,136,183,1]]],
	[[0,[207,231,250,1]],[1,[99,147,193,1]]],
	[[0,[210,223,237,1]],[0.51,[190,208,234,1]],[0.51,[166,192,227,1]],[0.75,[186,208,239,1]],[1,[121,155,200,1]]],
	[[0,[238,238,238,1]],[1,[238,238,238,1]]],
	[[0,[226,226,226,1]],[0.5,[219,219,219,1]],[0.5,[209,209,209,1]],[1,[254,254,254,1]]],
	[[0,[242,246,248,1]],[0.5,[216,225,231,1]],[0.5,[181,198,208,1]],[1,[224,239,249,1]]],
	[[0,[212,228,239,1]],[1,[134,174,204,1]]],
	[[0,[245,246,246,1]],[0.49,[184,186,198,1]],[1,[245,246,246,1]]],
	[[0,[243,226,199,1]],[0.5,[193,158,103,1]],[0.5,[182,141,76,1]],[1,[233,212,179,1]]],
	[[0,[249,252,247,1]],[1,[245,249,240,1]]],
	[[0,[195,217,255,1]],[1,[152,176,217,1]]],
	[[0,[210,255,82,1]],[1,[145,232,66,1]]],
	[[0,[254,254,253,1]],[1,[174,191,118,1]]],
	[[0,[228,239,192,1]],[1,[171,189,115,1]]],
	[[0,[164,179,87,1]],[1,[117,137,12,1]]],
	[[0,[98,125,77,1]],[1,[31,59,8,1]]],
	[[0,[115,136,10,1]],[1,[115,136,10,1]]],
	[[0,[255,175,75,1]],[1,[255,146,10,1]]],
	[[0,[250,198,149,1]],[1,[239,141,49,1]]],
	[[0,[255,197,120,1]],[1,[251,157,35,1]]],
	[[0,[249,198,103,1]],[1,[247,150,33,1]]],
	[[0,[252,234,187,1]],[0.5,[252,205,77,1]],[0.5,[248,181,0,1]],[1,[251,223,147,1]]],
	[[0,[255,168,76,1]],[1,[255,123,13,1]]],
	[[0,[255,103,15,1]],[1,[255,103,15,1]]],
	[[0,[255,116,0,1]],[1,[255,116,0,1]]],
	[[0,[255,183,107,1]],[0.5,[255,167,61,1]],[0.5,[255,124,0,1]],[1,[255,127,4,1]]],
	[[0,[255,93,177,1]],[1,[239,1,124,1]]],
	[[0,[251,131,250,1]],[1,[233,60,236,1]]],
	[[0,[229,112,231,1]],[1,[168,73,163,1]]],
	[[0,[203,96,179,1]],[0.5,[173,18,131,1]],[1,[222,71,172,1]]],
	[[0,[255,0,132,1]],[1,[255,0,132,1]]],
	[[0,[252,236,252,1]],[0.5,[251,166,225,1]],[0.5,[253,137,215,1]],[1,[255,124,216,1]]],
	[[0,[203,96,179,1]],[0.5,[193,70,161,1]],[0.5,[168,0,119,1]],[1,[219,54,164,1]]],
	[[0,[235,233,249,1]],[0.5,[216,208,239,1]],[0.5,[206,199,236,1]],[1,[193,191,234,1]]],
	[[0,[137,137,186,1]],[1,[137,137,186,1]]],
	[[0,[254,187,187,1]],[1,[255,92,92,1]]],
	[[0,[242,130,91,1]],[0.5,[229,91,43,1]],[1,[240,113,70,1]]],
	[[0,[255,48,25,1]],[1,[207,4,4,1]]],
	[[0,[255,26,0,1]],[1,[255,26,0,1]]],
	[[0,[204,0,0,1]],[1,[204,0,0,1]]],
	[[0,[248,80,50,1]],[0.5,[241,111,92,1]],[0.5,[246,41,12,1]],[1,[231,56,39,1]]],
	[[0,[254,204,177,1]],[0.5,[241,116,50,1]],[0.5,[234,85,7,1]],[1,[251,149,94,1]]],
	[[0,[239,197,202,1]],[0.5,[210,75,90,1]],[0.5,[186,39,55,1]],[1,[241,142,153,1]]],
	[[0,[243,197,189,1]],[0.5,[232,108,87,1]],[0.5,[234,40,3,1]],[0.75,[255,102,0,1]],[1,[199,34,0,1]]],
	[[0,[183,222,237,1]],[0.5,[113,206,239,1]],[0.5,[33,180,226,1]],[1,[183,222,237,1]]],
	[[0,[224,243,250,1]],[0.5,[216,240,252,1]],[0.5,[184,226,246,1]],[1,[182,223,253,1]]],
	[[0,[254,255,232,1]],[1,[214,219,191,1]]],
	[[0,[252,255,244,1]],[1,[233,233,206,1]]],
	[[0,[252,255,244,1]],[1,[179,190,173,1]]],
	[[0,[229,230,150,1]],[1,[209,211,96,1]]],
	[[0,[234,239,181,1]],[1,[225,233,160,1]]],
	[[0,[69,72,77,1]],[1,[0,0,0,1]]],
	[[0,[125,126,125,1]],[1,[14,14,14,1]]],
	[[0,[149,149,149,1]],[0.5,[1,1,1,1]],[0.76,[78,78,78,1]],[1,[27,27,27,1]]],
	[[0,[174,188,191,1]],[0.5,[110,119,116,1]],[0.5,[10,14,10,1]],[1,[10,8,9,1]]],
	[[0,[197,222,234,1]],[1,[6,109,171,1]]],
	[[0,[247,251,252,1]],[1,[173,217,228,1]]],
	[[0,[214,249,255,1]],[1,[158,232,250,1]]],
	[[0,[233,246,253,1]],[1,[211,238,251,1]]],
	[[0,[99,182,219,1]],[1,[48,157,207,1]]],
	[[0,[44,83,158,1]],[1,[44,83,158,1]]],
	[[0,[169,228,247,1]],[1,[15,180,231,1]]],
	[[0,[178,225,255,1]],[1,[102,182,252,1]]],
	[[0,[79,133,187,1]],[1,[79,133,187,1]]],
	[[0,[147,206,222,1]],[1,[73,165,191,1]]],
	[[0,[222,239,255,1]],[1,[152,190,222,1]]],
	[[0,[73,192,240,1]],[1,[44,175,227,1]]],
	[[0,[254,255,255,1]],[1,[210,235,249,1]]],
	[[0,[167,207,223,1]],[1,[35,83,138,1]]],
	[[0,[73,155,234,1]],[1,[32,124,229,1]]],
	[[0,[53,106,160,1]],[1,[53,106,160,1]]],
	[[0,[255,255,255,1]],[1,[237,237,237,1]]],
	[[0,[242,249,254,1]],[1,[214,240,253,1]]],
	[[0,[255,255,255,1]],[1,[229,229,229,1]]],
	[[0,[255,255,255,1]],[0.5,[241,241,241,1]],[0.5,[225,225,225,1]],[1,[246,246,246,1]]],
	[[0,[255,255,255,1]],[0.5,[243,243,243,1]],[0.5,[237,237,237,1]],[1,[255,255,255,1]]],
	[[0,[246,248,249,1]],[0.5,[229,235,238,1]],[0.5,[215,222,227,1]],[1,[245,247,249,1]]],
	[[0,[246,230,180,1]],[1,[237,144,23,1]]],
	[[0,[234,185,45,1]],[1,[199,152,16,1]]],
	[[0,[255,214,94,1]],[1,[254,191,4,1]]],
	[[0,[241,231,103,1]],[1,[254,182,69,1]]],
	[[0,[255,255,136,1]],[1,[255,255,136,1]]],
	[[0,[254,191,1,1]],[1,[254,191,1,1]]]];


/* COLORS */

Q.CO={};

Q.CO['Fruity']=[

	[252,233,79,1],[237,212,0,1],[196,160,0,1],[138,226,52,1],[115,210,22,1],[78,154,6,1],[78,154,6,1],
	[252,175,62,1],[245,121,0,1],[206,92,0,1],[114,159,207,1],[52,101,164,1],[32,74,135,1],[32,74,135,1],
	[233,185,110,1],[193,125,17,1],[143,89,2,1],[173,127,168,1],[117,80,123,1],[92,53,102,1],[92,53,102,1],
	[164,0,0,1],[204,0,0,1],[239,41,41,1],[248,46,0,1],[255,142,3,1],[255,213,3,1],[189,248,0,1]];
	
Q.CO['Oxygen']=[[56,37,9,1],[87,64,30,1],[117,81,26,1],[143,107,50,1],[179,146,93,1],[222,188,133,1],[156,15,15,1],[191,3,3,1],[226,8,0,1],[232,87,82,1],[240,134,130,1],[249,204,202,1],[156,15,86,1],[191,3,97,1],[226,0,113,1],[232,82,144,1],[240,130,176,1],[249,202,222,1],[106,0,86,1],[133,2,108,1],[160,39,134,1],[177,79,154,1],[193,115,176,1],[232,183,215,1],[29,1,85,1],[52,23,110,1],[70,40,134,1],[100,74,155,1],[142,121,165,1],[195,180,218,1],[0,49,110,1],[0,67,138,1],[0,87,174,1],[44,114,199,1],[97,147,207,1],[164,192,228,1],[0,72,77,1],[0,96,102,1],[0,120,128,1],[0,167,179,1],[0,196,204,1],[168,221,224,1],[0,8,63,1],[0,115,77,1],[0,153,102,1],[0,179,119,1],[0,204,136,1],[153,220,198,1],[0,110,41,1],[0,137,44,1],[55,164,44,1],[119,183,83,1],[177,210,143,1],[216,232,194,1],[227,173,0,1],[243,195,0,1],[255,221,0,1],[255,235,85,1],[255,242,153,1],[255,246,200,1],[172,67,17,1],[207,73,19,1],[235,115,49,1],[242,155,104,1],[242,187,136,1],[255,217,176,1],[46,52,54,1],[85,87,83,1],[136,138,133,1],[186,189,182,1],[211,215,207,1],[238,238,236,1],[77,38,0,1],[128,63,0,1],[191,94,0,1],[255,126,0,1],[255,191,128,1],[255,223,191,1],[89,0,0,1],[140,0,0,1],[191,0,0,1],[255,0,0,1],[255,128,128,1],[255,191,191,1],[115,0,85,1],[163,0,123,1],[204,0,154,1],[255,0,191,1],[255,128,223,1],[255,191,240,1],[44,0,89,1],[64,0,128,1],[90,0,179,1],[128,0,255,1],[192,128,255,1],[223,191,255,1],[0,0,128,1],[0,0,191,1],[0,0,255,1],[0,102,255,1],[128,179,255,1],[191,217,255,1],[0,77,0,1],[0,140,0,1],[0,191,0,1],[0,255,0,1],[128,255,128,1],[191,255,191,1],[99,128,0,1],[139,179,0,1],[191,245,0,1],[229,255,0,1],[240,255,128,1],[248,255,191,1],[255,170,0,1],[255,191,0,1],[255,213,0,1],[255,255,0,1],[255,255,153,1],[255,255,191,1],[50,50,50,1],[85,85,85,1],[136,136,136,1],[187,187,187,1],[221,221,221,1],[238,238,238,1]];

Q.CO['Cranes']=[[8,8,8,1],[192,176,144,1],[192,164,128,1],[80,72,68,1],[112,140,88,1],[104,132,96,1],[92,104,84,1],[24,8,12,1],[96,108,92,1],[128,104,76,1],[44,28,24,1],[156,140,116,1],[156,148,116,1],[68,68,60,1],[212,196,148,1],[144,136,108,1],[160,148,128,1],[216,220,216,1],[44,28,40,1],[68,12,16,1],[12,8,32,1],[56,8,48,1],[48,8,40,1],[44,20,40,1],[104,104,92,1],[52,28,48,1],[156,176,124,1],[124,160,96,1],[44,40,24,1],[168,168,172,1],[196,188,168,1],[8,28,20,1],[160,60,60,1],[180,144,100,1],[192,144,112,1],[132,40,60,1],[48,8,24,1],[156,176,144,1],[80,84,64,1],[136,148,136,1],[120,160,116,1],[44,20,24,1],[128,84,80,1],[128,20,56,1],[168,68,76,1],[64,56,44,1],[56,40,40,1],[156,172,164,1],[100,92,72,1],[124,116,88,1],[32,8,28,1],[176,180,180,1],[156,44,68,1],[172,176,156,1],[176,168,144,1],[188,8,88,1],[148,8,52,1],[20,48,52,1],[68,60,68,1],[216,204,160,1],[140,160,112,1],[112,116,88,1],[152,160,164,1],[116,156,104,1],[192,192,184,1],[80,84,72,1],[56,24,24,1],[24,36,40,1],[48,56,52,1],[92,84,64,1],[180,160,120,1],[112,100,76,1],[156,120,88,1],[96,96,84,1],[148,160,152,1],[192,200,144,1],[68,56,56,1],[100,48,96,1],[108,144,100,1],[188,180,160,1],[60,48,68,1],[80,48,52,1],[188,56,72,1],[196,176,128,1],[220,180,164,1],[132,116,100,1],[68,36,52,1],[168,132,100,1],[180,168,132,1],[148,156,140,1],[120,128,108,1],[108,12,48,1],[108,80,68,1],[44,40,56,1],[140,124,104,1],[24,28,20,1],[204,172,120,1],[164,156,132,1],[116,104,96,1],[144,152,148,1],[84,92,72,1],[120,148,100,1],[120,112,108,1],[60,64,56,1],[204,176,140,1],[212,140,128,1],[80,56,64,1],[152,140,108,1],[72,72,76,1],[20,12,48,1],[140,152,128,1],[208,100,88,1],[8,36,48,1],[80,104,68,1],[44,40,8,1],[76,60,84,1],[56,40,24,1],[112,52,64,1],[212,124,112,1],[152,72,76,1],[212,124,128,1],[88,88,96,1],[16,68,8,1],[36,8,40,1],[128,168,112,1],[208,212,196,1],[216,208,176,1],[76,68,56,1],[40,44,28,1],[32,20,80,1],[136,64,68,1],[60,76,64,1],[200,104,96,1],[92,84,80,1],[164,148,116,1],[116,108,84,1],[148,128,100,1],[80,80,84,1],[136,140,128,1],[88,16,76,1],[124,12,8,1],[100,96,104,1],[132,52,36,1],[56,28,40,1],[196,96,88,1],[64,84,44,1],[164,188,132,1],[204,196,168,1],[216,220,208,1],[136,172,100,1],[148,188,124,1],[52,72,32,1],[108,108,116,1],[136,172,132,1],[60,60,64,1],[108,92,92,1],[216,156,144,1],[104,96,84,1],[172,180,172,1],[140,108,88,1],[160,164,156,1],[148,132,108,1],[128,140,120,1],[40,84,56,1],[132,56,108,1],[96,32,56,1],[164,88,84,1],[96,104,72,1],[172,192,152,1],[192,196,172,1],[208,108,100,1],[68,84,72,1],[140,128,96,1],[224,228,224,1],[128,128,132,1],[76,96,56,1],[88,76,100,1],[72,12,52,1],[56,44,56,1],[68,48,68,1],[60,20,64,1],[44,12,56,1],[84,68,76,1],[44,8,8,1],[148,136,100,1],[148,180,108,1],[184,52,20,1],[0,0,0,1],[40,0,0,1],[0,56,92,1],[0,0,4,1],[0,0,0,1],[60,56,56,1],[56,20,40,1],[192,28,36,1],[72,80,60,1],[72,92,68,1],[88,100,76,1],[88,116,72,1],[0,0,0,1],[56,40,56,1],[72,76,68,1],[128,48,52,1],[168,36,60,1],[76,96,76,1],[80,108,80,1],[128,120,108,1],[184,72,80,1],[104,128,84,1],[136,132,108,1],[148,140,116,1],[112,152,92,1],[208,184,132,1],[104,116,100,1],[132,128,116,1],[136,120,88,1],[0,0,0,1],[116,124,120,1],[136,152,152,1],[144,156,160,1],[156,168,176,1],[184,184,176,1],[152,172,184,1],[184,188,188,1],[196,200,196,1],[212,212,208,1],[148,164,172,1],[84,96,84,1],[96,8,20,1],[100,116,80,1],[96,120,92,1],[164,176,184,1],[204,188,152,1],[128,128,100,1],[44,40,40,1],[204,204,188,1],[92,128,80,1],[100,140,88,1],[56,48,44,1],[128,148,112,1],[36,28,40,1],[196,160,112,1],[124,116,96,1],[104,104,84,1],[132,140,140,1],[140,132,116,1],[204,208,200,1],[196,80,84,1],[116,112,96,1],[88,116,84,1],[64,44,56,1],[44,24,8,1],[196,116,116,1],[180,148,116,1],[136,176,116,1],[148,140,124,1]];

Q.CO['Grays']=[[0,0,0,1],[7,7,7,1],[15,15,15,1],[23,23,23,1],[31,31,31,1],[39,39,39,1],[47,47,47,1],[55,55,55,1],[63,63,63,1],[71,71,71,1],[79,79,79,1],[87,87,87,1],[95,95,95,1],[103,103,103,1],[111,111,111,1],[119,119,119,1],[127,127,127,1],[135,135,135,1],[143,143,143,1],[151,151,151,1],[159,159,159,1],[167,167,167,1],[175,175,175,1],[183,183,183,1],[191,191,191,1],[199,199,199,1],[207,207,207,1],[215,215,215,1],[223,223,223,1],[231,231,231,1],[239,239,239,1],[247,247,247,1]];

Q.CO['Named Colors']=[[255,250,250,1],[248,248,255,1],[245,245,245,1],[220,220,220,1],[255,250,240,1],[253,245,230,1],[250,240,230,1],[250,235,215,1],[255,239,213,1],[255,235,205,1],[255,228,196,1],[255,218,185,1],[255,222,173,1],[255,228,181,1],[255,248,220,1],[255,255,240,1],[255,250,205,1],[255,245,238,1],[240,255,240,1],[245,255,250,1],[240,255,255,1],[240,248,255,1],[230,230,250,1],[255,240,245,1],[255,228,225,1],[255,255,255,1],[0,0,0,1],[47,79,79,1],[105,105,105,1],[112,128,144,1],[119,136,153,1],[190,190,190,1],[211,211,211,1],[25,25,112,1],[0,0,128,1],[100,149,237,1],[72,61,139,1],[106,90,205,1],[123,104,238,1],[132,112,255,1],[0,0,205,1],[65,105,225,1],[0,0,255,1],[30,144,255,1],[0,191,255,1],[135,206,235,1],[135,206,250,1],[70,130,180,1],[176,196,222,1],[173,216,230,1],[176,224,230,1],[175,238,238,1],[0,206,209,1],[72,209,204,1],[64,224,208,1],[0,255,255,1],[224,255,255,1],[95,158,160,1],[102,205,170,1],[127,255,212,1],[0,100,0,1],[85,107,47,1],[143,188,143,1],[46,139,87,1],[60,179,113,1],[32,178,170,1],[152,251,152,1],[0,255,127,1],[124,252,0,1],[0,255,0,1],[127,255,0,1],[0,250,154,1],[173,255,47,1],[50,205,50,1],[154,205,50,1],[34,139,34,1],[107,142,35,1],[189,183,107,1],[240,230,140,1],[238,232,170,1],[250,250,210,1],[255,255,224,1],[255,255,0,1],[255,215,0,1],[238,221,130,1],[218,165,32,1],[184,134,11,1],[188,143,143,1],[205,92,92,1],[139,69,19,1],[160,82,45,1],[205,133,63,1],[222,184,135,1],[245,245,220,1],[245,222,179,1],[244,164,96,1],[210,180,140,1],[210,105,30,1],[178,34,34,1],[165,42,42,1],[233,150,122,1],[250,128,114,1],[255,160,122,1],[255,165,0,1],[255,140,0,1],[255,127,80,1],[240,128,128,1],[255,99,71,1],[255,69,0,1],[255,0,0,1],[255,105,180,1],[255,20,147,1],[255,192,203,1],[255,182,193,1],[219,112,147,1],[176,48,96,1],[199,21,133,1],[208,32,144,1],[255,0,255,1],[238,130,238,1],[221,160,221,1],[218,112,214,1],[186,85,211,1],[153,50,204,1],[148,0,211,1],[138,43,226,1],[160,32,240,1],[147,112,219,1],[216,191,216,1],[255,250,250,1],[238,233,233,1],[205,201,201,1],[139,137,137,1],[255,245,238,1],[238,229,222,1],[205,197,191,1],[139,134,130,1],[255,239,219,1],[238,223,204,1],[205,192,176,1],[139,131,120,1],[255,228,196,1],[238,213,183,1],[205,183,158,1],[139,125,107,1],[255,218,185,1],[238,203,173,1],[205,175,149,1],[139,119,101,1],[255,222,173,1],[238,207,161,1],[205,179,139,1],[139,121,94,1],[255,250,205,1],[238,233,191,1],[205,201,165,1],[139,137,112,1],[255,248,220,1],[238,232,205,1],[205,200,177,1],[139,136,120,1],[255,255,240,1],[238,238,224,1],[205,205,193,1],[139,139,131,1],[240,255,240,1],[224,238,224,1],[193,205,193,1],[131,139,131,1],[255,240,245,1],[238,224,229,1],[205,193,197,1],[139,131,134,1],[255,228,225,1],[238,213,210,1],[205,183,181,1],[139,125,123,1],[240,255,255,1],[224,238,238,1],[193,205,205,1],[131,139,139,1],[131,111,255,1],[122,103,238,1],[105,89,205,1],[71,60,139,1],[72,118,255,1],[67,110,238,1],[58,95,205,1],[39,64,139,1],[0,0,255,1],[0,0,238,1],[0,0,205,1],[0,0,139,1],[30,144,255,1],[28,134,238,1],[24,116,205,1],[16,78,139,1],[99,184,255,1],[92,172,238,1],[79,148,205,1],[54,100,139,1],[0,191,255,1],[0,178,238,1],[0,154,205,1],[0,104,139,1],[135,206,255,1],[126,192,238,1],[108,166,205,1],[74,112,139,1],[176,226,255,1],[164,211,238,1],[141,182,205,1],[96,123,139,1],[198,226,255,1],[185,211,238,1],[159,182,205,1],[108,123,139,1],[202,225,255,1],[188,210,238,1],[162,181,205,1],[110,123,139,1],[191,239,255,1],[178,223,238,1],[154,192,205,1],[104,131,139,1],[224,255,255,1],[209,238,238,1],[180,205,205,1],[122,139,139,1],[187,255,255,1],[174,238,238,1],[150,205,205,1],[102,139,139,1],[152,245,255,1],[142,229,238,1],[122,197,205,1],[83,134,139,1],[0,245,255,1],[0,229,238,1],[0,197,205,1],[0,134,139,1],[0,255,255,1],[0,238,238,1],[0,205,205,1],[0,139,139,1],[151,255,255,1],[141,238,238,1],[121,205,205,1],[82,139,139,1],[127,255,212,1],[118,238,198,1],[102,205,170,1],[69,139,116,1],[193,255,193,1],[180,238,180,1],[155,205,155,1],[105,139,105,1],[84,255,159,1],[78,238,148,1],[67,205,128,1],[46,139,87,1],[154,255,154,1],[144,238,144,1],[124,205,124,1],[84,139,84,1],[0,255,127,1],[0,238,118,1],[0,205,102,1],[0,139,69,1],[0,255,0,1],[0,238,0,1],[0,205,0,1],[0,139,0,1],[127,255,0,1],[118,238,0,1],[102,205,0,1],[69,139,0,1],[192,255,62,1],[179,238,58,1],[154,205,50,1],[105,139,34,1],[202,255,112,1],[188,238,104,1],[162,205,90,1],[110,139,61,1],[255,246,143,1],[238,230,133,1],[205,198,115,1],[139,134,78,1],[255,236,139,1],[238,220,130,1],[205,190,112,1],[139,129,76,1],[255,255,224,1],[238,238,209,1],[205,205,180,1],[139,139,122,1],[255,255,0,1],[238,238,0,1],[205,205,0,1],[139,139,0,1],[255,215,0,1],[238,201,0,1],[205,173,0,1],[139,117,0,1],[255,193,37,1],[238,180,34,1],[205,155,29,1],[139,105,20,1],[255,185,15,1],[238,173,14,1],[205,149,12,1],[139,101,8,1],[255,193,193,1],[238,180,180,1],[205,155,155,1],[139,105,105,1],[255,106,106,1],[238,99,99,1],[205,85,85,1],[139,58,58,1],[255,130,71,1],[238,121,66,1],[205,104,57,1],[139,71,38,1],[255,211,155,1],[238,197,145,1],[205,170,125,1],[139,115,85,1],[255,231,186,1],[238,216,174,1],[205,186,150,1],[139,126,102,1],[255,165,79,1],[238,154,73,1],[205,133,63,1],[139,90,43,1],[255,127,36,1],[238,118,33,1],[205,102,29,1],[139,69,19,1],[255,48,48,1],[238,44,44,1],[205,38,38,1],[139,26,26,1],[255,64,64,1],[238,59,59,1],[205,51,51,1],[139,35,35,1],[255,140,105,1],[238,130,98,1],[205,112,84,1],[139,76,57,1],[255,160,122,1],[238,149,114,1],[205,129,98,1],[139,87,66,1],[255,165,0,1],[238,154,0,1],[205,133,0,1],[139,90,0,1],[255,127,0,1],[238,118,0,1],[205,102,0,1],[139,69,0,1],[255,114,86,1],[238,106,80,1],[205,91,69,1],[139,62,47,1],[255,99,71,1],[238,92,66,1],[205,79,57,1],[139,54,38,1],[255,69,0,1],[238,64,0,1],[205,55,0,1],[139,37,0,1],[255,0,0,1],[238,0,0,1],[205,0,0,1],[139,0,0,1],[255,20,147,1],[238,18,137,1],[205,16,118,1],[139,10,80,1],[255,110,180,1],[238,106,167,1],[205,96,144,1],[139,58,98,1],[255,181,197,1],[238,169,184,1],[205,145,158,1],[139,99,108,1],[255,174,185,1],[238,162,173,1],[205,140,149,1],[139,95,101,1],[255,130,171,1],[238,121,159,1],[205,104,137,1],[139,71,93,1],[255,52,179,1],[238,48,167,1],[205,41,144,1],[139,28,98,1],[255,62,150,1],[238,58,140,1],[205,50,120,1],[139,34,82,1],[255,0,255,1],[238,0,238,1],[205,0,205,1],[139,0,139,1],[255,131,250,1],[238,122,233,1],[205,105,201,1],[139,71,137,1],[255,187,255,1],[238,174,238,1],[205,150,205,1],[139,102,139,1],[224,102,255,1],[209,95,238,1],[180,82,205,1],[122,55,139,1],[191,62,255,1],[178,58,238,1],[154,50,205,1],[104,34,139,1],[155,48,255,1],[145,44,238,1],[125,38,205,1],[85,26,139,1],[171,130,255,1],[159,121,238,1],[137,104,205,1],[93,71,139,1],[255,225,255,1],[238,210,238,1],[205,181,205,1],[139,123,139,1],[169,169,169,1],[169,169,169,1],[0,0,139,1],[0,139,139,1],[139,0,139,1],[139,0,0,1],[144,238,144,1]];

Q.CO['Pastels']=[[226,145,145,1],[153,221,146,1],[147,216,185,1],[148,196,211,1],[148,154,206,1],[179,148,204,1],[204,150,177,1],[204,164,153,1],[223,229,146,1],[255,165,96,1],[107,255,99,1],[101,255,204,1],[101,196,255,1],[101,107,255,1],[173,101,255,1],[255,101,244,1],[255,101,132,1],[255,101,101,1]];

Q.CO['Reds And Purples']=[[205,92,92,1],[178,34,34,1],[165,42,42,1],[233,150,122,1],[250,128,114,1],[255,160,122,1],[255,127,80,1],[240,128,128,1],[255,99,71,1],[255,69,0,1],[255,0,0,1],[255,105,180,1],[255,20,147,1],[255,192,203,1],[255,182,193,1],[219,112,147,1],[176,48,96,1],[199,21,133,1],[208,32,144,1],[255,0,255,1],[238,130,238,1],[221,160,221,1],[218,112,214,1],[186,85,211,1],[153,50,204,1],[148,0,211,1],[138,43,226,1],[160,32,240,1],[147,112,219,1],[216,191,216,1]];

Q.CO['Web']=[[255,255,255,1],[255,255,204,1],[255,255,153,1],[255,255,102,1],[255,255,51,1],[255,255,0,1],[255,204,255,1],[255,204,204,1],[255,204,153,1],[255,204,102,1],[255,204,51,1],[255,204,0,1],[255,153,255,1],[255,153,204,1],[255,153,153,1],[255,153,102,1],[255,153,51,1],[255,153,0,1],[255,102,255,1],[255,102,204,1],[255,102,153,1],[255,102,102,1],[255,102,51,1],[255,102,0,1],[255,51,255,1],[255,51,204,1],[255,51,153,1],[255,51,102,1],[255,51,51,1],[255,51,0,1],[255,0,255,1],[255,0,204,1],[255,0,153,1],[255,0,102,1],[255,0,51,1],[255,0,0,1],[204,255,255,1],[204,255,204,1],[204,255,153,1],[204,255,102,1],[204,255,51,1],[204,255,0,1],[204,204,255,1],[204,204,204,1],[204,204,153,1],[204,204,102,1],[204,204,51,1],[204,204,0,1],[204,153,255,1],[204,153,204,1],[204,153,153,1],[204,153,102,1],[204,153,51,1],[204,153,0,1],[204,102,255,1],[204,102,204,1],[204,102,153,1],[204,102,102,1],[204,102,51,1],[204,102,0,1],[204,51,255,1],[204,51,204,1],[204,51,153,1],[204,51,102,1],[204,51,51,1],[204,51,0,1],[204,0,255,1],[204,0,204,1],[204,0,153,1],[204,0,102,1],[204,0,51,1],[204,0,0,1],[153,255,255,1],[153,255,204,1],[153,255,153,1],[153,255,102,1],[153,255,51,1],[153,255,0,1],[153,204,255,1],[153,204,204,1],[153,204,153,1],[153,204,102,1],[153,204,51,1],[153,204,0,1],[153,153,255,1],[153,153,204,1],[153,153,153,1],[153,153,102,1],[153,153,51,1],[153,153,0,1],[153,102,255,1],[153,102,204,1],[153,102,153,1],[153,102,102,1],[153,102,51,1],[153,102,0,1],[153,51,255,1],[153,51,204,1],[153,51,153,1],[153,51,102,1],[153,51,51,1],[153,51,0,1],[153,0,255,1],[153,0,204,1],[153,0,153,1],[153,0,102,1],[153,0,51,1],[153,0,0,1],[102,255,255,1],[102,255,204,1],[102,255,153,1],[102,255,102,1],[102,255,51,1],[102,255,0,1],[102,204,255,1],[102,204,204,1],[102,204,153,1],[102,204,102,1],[102,204,51,1],[102,204,0,1],[102,153,255,1],[102,153,204,1],[102,153,153,1],[102,153,102,1],[102,153,51,1],[102,153,0,1],[102,102,255,1],[102,102,204,1],[102,102,153,1],[102,102,102,1],[102,102,51,1],[102,102,0,1],[102,51,255,1],[102,51,204,1],[102,51,153,1],[102,51,102,1],[102,51,51,1],[102,51,0,1],[102,0,255,1],[102,0,204,1],[102,0,153,1],[102,0,102,1],[102,0,51,1],[102,0,0,1],[51,255,255,1],[51,255,204,1],[51,255,153,1],[51,255,102,1],[51,255,51,1],[51,255,0,1],[51,204,255,1],[51,204,204,1],[51,204,153,1],[51,204,102,1],[51,204,51,1],[51,204,0,1],[51,153,255,1],[51,153,204,1],[51,153,153,1],[51,153,102,1],[51,153,51,1],[51,153,0,1],[51,102,255,1],[51,102,204,1],[51,102,153,1],[51,102,102,1],[51,102,51,1],[51,102,0,1],[51,51,255,1],[51,51,204,1],[51,51,153,1],[51,51,102,1],[51,51,51,1],[51,51,0,1],[51,0,255,1],[51,0,204,1],[51,0,153,1],[51,0,102,1],[51,0,51,1],[51,0,0,1],[0,255,255,1],[0,255,204,1],[0,255,153,1],[0,255,102,1],[0,255,51,1],[0,255,0,1],[0,204,255,1],[0,204,204,1],[0,204,153,1],[0,204,102,1],[0,204,51,1],[0,204,0,1],[0,153,255,1],[0,153,204,1],[0,153,153,1],[0,153,102,1],[0,153,51,1],[0,153,0,1],[0,102,255,1],[0,102,204,1],[0,102,153,1],[0,102,102,1],[0,102,51,1],[0,102,0,1],[0,51,255,1],[0,51,204,1],[0,51,153,1],[0,51,102,1],[0,51,51,1],[0,51,0,1],[0,0,255,1],[0,0,204,1],[0,0,153,1],[0,0,102,1],[0,0,51,1],[0,0,0,1]];


/* PATTERNS */

Q.PT={};

Q.PT['DinPattern']=new Array(38);

Q.PT['Gaudi']=new Array(31);

Q.PT['Headlock']=new Array(141);

Q.PT['Squidfingers']=new Array(158);


/* CURRENT */

vars.CO=Q.CO['Oxygen'];
vars.GD=Q.GD['Web v2.0'];
vars.PT=Q.PT['Squidfingers'];
canvas = {
	'history_n': 25,
	'history_mk': function () {
		var z = '';
		canvas.history_r = {
			'n': 0,
			'r': {
				0: 0
			},
			'data': {
				0: [canvas.W, canvas.H]
			},
			'a': 0,
			'z': 0
		};
		for (var i = 0; i <= canvas.history_n; i++) {
			z += '<canvas id="history_' + i + '" width="' + canvas.W + '" height="' + canvas.H + '">' + "<\/canvas>";
		}
		$('cHistory').innerHTML = z;
		$2D('history_0').drawImage($('ctx_box'), 0, 0, canvas.W, canvas.H);
		var o = gui.Y;
		$C('MM', 'history')[0].innerHTML = '<div class="z"></div>' + o.kontrol('hi');
		$S('hiKontrol').display = 'none';
		o.cur[o.id] = 0;
		o.hi(1);
	},
	'history_set': function (v, s) {
		if ($('history_0')) {
			var o = gui.Y,
			r = canvas.history_r;
			if (isNaN(v)) { // SAVE
				var z = r.z;
				r.r[r.n + 1] = (r.n % canvas.history_n) + 1;
				r.z = ++r.n;
				r.r[r.a = Math.max(0, r.n - canvas.history_n)] = 0;
				r.data[r.n] = [canvas.W, canvas.H, vars.type, vars.typeImg, v];
				if (r.r[r.n + 1]) {
					for (var i = r.n + 1; i <= z; i++) {
						r.data[i] = [];
					}
				}
				var a = 'history_' + r.r[r.n],
				b = 'ctx_box';
				$(a).width = canvas.W;
				$(a).height = canvas.H;
			} else { // UNDO + REDO
				var a = 'ctx_box',
				b = 'history_' + r.r[r.n];
				canvas.W = r.data[r.n][0];
				canvas.H = r.data[r.n][1];
				$('ctx_box').width = canvas.W;
				$('ctx_box').height = canvas.H;
				crop.resize();
			}
			var c = $2D(a);
			co.del(c);
			c.drawImage($(b), 0, 0, canvas.W, canvas.H);
			if (b == 'ctx_box') {
				c.drawImage($('ctx_temp'), 0, 0, canvas.W, canvas.H);
			}
			if (!s) {
				o.cur[o.id] = r.z - 5;
			}
			o.hi(1);
			gui.Y.kontrol_update('hi');
		}
	},
	'close': function () {
		$S('canvas').display = 'none';
		$('history').innerHTML = '';
	},
	'load': function (u, w, h) {
		gui_swatch.id = 'CO';
		$('ctx_box').width = canvas.W = w;
		$('ctx_box').height = canvas.H = h;
		crop.apply({ X: 0, Y: 0 }, { X: w, Y: h });
		if (u) {
			jelly.src = u;
			jelly.onload = function () {
				var c = $2D('ctx_box');
				co.del(c);
				c.drawImage(jelly, 0, 0, canvas.W, canvas.H);
			}
		} else {
			var a = {
					X: 0,
					Y: 0
				},
				b = {
					X: w,
					Y: h
				},
				c = $2D('ctx_box');
			c.rect(a.X, a.Y, b.X, b.Y);
			co.style[vars[vars.id]](a, b, c, 'fill', 1);
		}
	},
	'resize': function (w, h) {
		$C('TM', 'canvas')[0].style.width = w + 'px';
		$C('MM', 'canvas')[0].style.width = w + 'px';
		$C('BM', 'canvas')[0].style.width = w + 'px';
		$C('ML', 'canvas')[0].style.height = h + 'px';
		$C('MM', 'canvas')[0].style.height = h + 'px';
		$C('MR', 'canvas')[0].style.height = h + 'px';
		$S('cBound').width = w + 'px';
		$S('cBound').height = h + 'px';
	},
	'mode_sw': function (o) {
		var r = {
				'paint': 'source-over',
				'light': 'lighter'
			},
			d = $C('cur', $(o).parentNode);
		if (d[0]) d[0].className = '';
		$(o).className = 'cur';
		vars.mode = $(o).innerHTML;
		vars.cache(1);
		$2D('ctx_temp').globalCompositeOperation = canvas.mode = r[$(o).innerHTML];
	},
	'mode': 'source-over'

};
