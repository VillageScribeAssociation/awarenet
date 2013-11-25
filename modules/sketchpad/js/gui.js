
//--------------------------------------------------------------------------------------------------
//* GUI - apparent window manager for sketchpad
//--------------------------------------------------------------------------------------------------

gui = {

	//----------------------------------------------------------------------------------------------
	//.	options window?
	//----------------------------------------------------------------------------------------------
	//	assumes stamps global object is set up

	'options': function () {
		var r = [];					//%	(DOCUMENTME) [array]
		var fu = N.format;			//%	(DOCUMENTME)

		//------------------------------------------------------------------------------------------
		//	reassign global object 'cf'?
		//------------------------------------------------------------------------------------------
		//	this appears to create controls for all tools, and set default values for them.
		//	control types: 'x' (slider), 'menu' (drop-down), 'check' (checkbox), 'radio' (option)

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
				val: [
					'Display (' + fu(screen.width) + 'x' + fu(screen.height) + ')',
					'Original (' + fu(canvas.W) + 'x' + fu(canvas.H) + ')',
					'2x3',
					'3x5',
					'4x3 (DVD)',
					'4x3 (Book)',
					'4x6 (Postcard)',
					'5x7 (L, 2L)',
					'8x10',
					'16x9 (HD)',
					'16x20',
					'20x30 (Poster)',
					'Square'
				]
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

		};	// end assignment of 'cF'

		//------------------------------------------------------------------------------------------
		//	(DOCUMENTME) what is global array 'r'?
		//------------------------------------------------------------------------------------------
		//	appears to define those operations which involve dragging something on thr canvas from
		//	a starting position - definition of mouse behavior for tools?

		r = [
			"marquee",
			"text",
			"line",
			"ellipses",
			"polygon",
			"star",
			"burst",
			"gear",
			"brush",
			"calligraphy",
			"pencil",
			"stamp",
			"fill",
			"eraser"
		];

		for (var i in r) {
			cF["movement_" + r[i]] = {
				type: "radio",
				val: ["movement_" + r[i], "anchored", "freedraw", "active"]
			};
		}

		//------------------------------------------------------------------------------------------
		//	(DOCUMENTME) appears to map common elements to multiple tools
		//------------------------------------------------------------------------------------------
		//arg: r - array of tool names? (CHECKME) [array:string]
		//arg: v - control name [string]

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

		//------------------------------------------------------------------------------------------
		//	initializes drop-down menu to choose stamp set (see /assets/media/glyph/)
		//------------------------------------------------------------------------------------------

		var r = [];						//% names of glyph sets [array:string]
		var j = 0;						//%	array index in r [int]

		for (var i in stamp.r) { r[j++] = i; }

		cF.stamp = {
			type: "menu",
			val: r
		};

		//------------------------------------------------------------------------------------------
		//	(DOCUMENTME) for phones and tablets?
		//------------------------------------------------------------------------------------------

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
	}, // end gui.options()

	//----------------------------------------------------------------------------------------------
	//. checkbox object
	//----------------------------------------------------------------------------------------------

	'check': {

		//------------------------------------------------------------------------------------------
		//.	render as HTML
		//------------------------------------------------------------------------------------------
		//arg: v - label? [string]
		//arg: c - identifier [string]
		//arg: r - first element is index into vars array [array]

		'build': function (v, c, r) {
			var checkClass = (vars[r[0]] == 'true' ? ' class="cur"' : '');
			var html = ''
			 + '<div id="' + c + '_check" class="check">'
			 + ' <span>' + v + '</span><br>'
			 + ' <div onclick="gui.check.click(this,\'' + r[0] + '\')"' + checkClass + '>'
			 + vars[r[0]]
			 + '</div>'
			 + '</div>';
			return html;
		},

		//------------------------------------------------------------------------------------------
		//.	toggle value on click?
		//------------------------------------------------------------------------------------------

		'click': function (o, v) {
			function z(a, b, c, d) {
				vars[v] = c;
				o.innerHTML = c;
				o.className = d;
			}
			if (o.className == 'cur') { z('block', 'none', 'false', ''); }
			else { z('none', 'block', 'true', 'cur'); }
			crop.click();
		}
	},

	//----------------------------------------------------------------------------------------------
	//.	radio button object
	//----------------------------------------------------------------------------------------------
	'radio': {

		//------------------------------------------------------------------------------------------
		//	render as html (this is something of a mess, TODO: tidy and simplify)
		//------------------------------------------------------------------------------------------
		//arg: v - label of radio set [string]
		//arg: c - identifier [string]
		//arg: r - array, first element is an index into vars, rest are option labels [array]

		'build': function (v, c, r) {
			var b = '';									//%	radio options [string]
			var radioClass = '';						//%	css class of current option [string]
			var radioStyle = '';						//%	style of whole radio set [string]

			for (var i = 1; i < r.length; i++) {

				radioClass = '';

				if (
					(vars[r[0]] == r[i] && vars.type != 'crop') ||
					(vars[r[0]] == r[i] && vars.type == 'crop' && !crop.force(vars.crop))
				) { radioClass = 'cur'; }

				b = b + ''
				 + '<div'
				 + ' class="' + radioClass + '"'
				 + ' onclick="gui.radio.click(this,\'' + r[0] + '\')">'
				 + r[i] + '</div><br>';

			}

			if (crop.force(vars.crop) && vars.type == 'crop') {
				radioStyle = ' style="opacity: 0.6"';
			}

			var html = ''
			 + '<div' + radioStyle + ' class="radio" id="' + c + '_radio">'
			 + '<span>' + v + '</span><br>'
			 + b
			 + '</div>';

			return (html);
		},

		//------------------------------------------------------------------------------------------
		//.	handle click
		//------------------------------------------------------------------------------------------
		//arg: o - clicked DOM node [object]
		//arg: v - index into global vars array [object]

		'click': function (o, v) {
			if (!$C("cur", o.parentNode)[0]) { return; }		//	(DOCUMENTME)
			var i = o.innerHTML;								//	Option label [string]
			var cur = vars[v];									//	Radio group name [string]

			$C("cur", o.parentNode)[0].className = "";			//	Deselect previous value
			o.className = "cur";								//	Make this option selected

			vars[v] = i;										//	Change key in global vars array

			if (v == "marquee") {
				var b = $C("Marquee_" + cur, "tools")[0];
				if (i == "lasso" || cur == "lasso") {
					marquee.reset("", 1);
				}
				b.src = ''
				 + jsServerPath + "modules/sketchpad/assets/media/gui/tools/"
				 + "Marquee_" + i + "_2.png";
				alert(b.src);
				b.className = "Marquee_" + i;
				gui_tools.prev = b.className;
				vars.tool = "Marquee_" + i;
			}

			vars.cache(1);										//	save for undo?
			crop.click();										//	Update crop shape
		}

	}, // end radio object

	//----------------------------------------------------------------------------------------------
	//	drop-down menu
	//----------------------------------------------------------------------------------------------

	'menu': {

		//------------------------------------------------------------------------------------------
		//.	render to HTML
		//------------------------------------------------------------------------------------------
		//arg: c - 
		//arg: r - array of constructor arguments

		'build': function (c, r) {
			var z = '';						//%	rendered list of options [string]
			var length = 0;					//% number of entries? [int]
			var o = gui.menu;				//%	this [object]
			var position = '';				//%	css adjustment to selected item [string]

			if (typeof(r) == 'object' && !r.length) {
				for (var i in r) { length++; }
			} else {
				length = r.length;
			}

			for (var i in r) {
				//var style = (i == 0) ? 'style="border-top: none;"' : ((parseInt(i) + 1) == length ? 'style="border-bottom: none;"' : '');

				var style = '';						//%	style of current drop-down item [string]
				var className = '';					//%	'sel' for the selected item

				if (i == 0) {
					style = 'style="border-top: none;"';
				} else {
					if ((parseInt(i) + 1) == length) { style = 'style="border-bottom: none;"'; }
				}

				if (r[i].toLowerCase() == o.cur[c].toLowerCase() || (!o.cur[c] && i == 0)) {
					className = 'class="sel"';
					var position = 'style="top:-' + (o.cellHeight * i) + 'px"';
				} else {
					className = '';
				}

				z = z + ''
				 + '<li'
				 + ' onmousedown="gui.menu.toggle(this)"'
				 + ' onmouseup="gui.menu.select(this)"'
				 + ' onmouseover="gui.menu.okClose(this.parentNode.parentNode)"'
				 + ' ' + style
				 + ' ' + className
				 + '>' + r[i] + '</li>';
			}

			return ''
			 + '<div class="menuWrap" id="' + c + '_opt">'
				 + ' <div class="t">'
					 + '<div class="l"></div>'
					 + '<div class="r"></div>'
					 + '<div class="c"></div>'
				 + '</div>'
				 + ' <div class="menuBox">'
					 + '  <ul ' + position + '>'
						 + '   <li class="top">'
							 + '<div class="l"></div>'
							 + '<div class="r"></div>'
							 + '<div class="c"></div>'
						 + '</li>'
						 + z
						 + '   <li class="bottom">'
							 + '<div class="l"></div>'
							 + '<div class="c"></div>'
							 + '<div class="r"></div>'
						 + '</li>'
					 + '  </ul>'
				 + ' </div>'
				 + ' <div class="b">'
					 + '<div class="l"></div>'
					 + '<div class="r"></div>'
					 + '<div class="c"></div>'
				 + '</div>'
			 + '</div>';

		}, // end menu.build()

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME) field updated? callback function?
		//------------------------------------------------------------------------------------------

		'fu': {

			//--------------------------------------------------------------------------------------
			//.	appears to reset / re-render a menu control (CHECKME)
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global? [string]
			//arg: o - DOM node? [object]

			'z': function (c, o) {
				gui.menu.klean(c, o.innerHTML);
			},

			//--------------------------------------------------------------------------------------
			//.	set the checkbox, called when menu updated for crop tool options?
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'crop' [string]
			//arg: o - DOM node? [object]

			'crop': function (c, o) {
				gui.menu.fu.z(c, o);
				$T('div', 'constrain_check')[0].innerHTML = 'true';	//	check the checkbox
				$T('div', 'constrain_check')[0].className = 'cur';	//	show state with css

				vars.constrain = 'true';							//	update global 'vars' array
				crop.click();										//	update crop tool selection
				vars.cache(1);										//	(DOCUMENTME)
			},

			//--------------------------------------------------------------------------------------
			//.	called when 'fill' options menu is clicked / changed
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'fill' [string]
			//arg: o - DOM node? [object]			

			'fill': function (c, o) {
				gui.menu.fu.z(c, o);			//	Re-render the menu, minimized
				gui_palette.click("fill");		//	(DOCUMENTME)
				gui_swatch.cur({				//	(DOCUMENTME) updates the 'tools' swatch
					"Gradient": "GD",		
					"Color": "CO",
					"Pattern": "PT"
				}[o.innerHTML]);
			},

			//--------------------------------------------------------------------------------------
			//.	called when a stamp / glyph set is chosen, ie menu item selected
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'stamp' [string]
			//arg: o - DOM node? [object]			

			'stamp': function (c, o) {
				gui.menu.fu.z(c, o);			//	Re-render the menu, minimized
				stamp.fileNumber = 1;			//	(DOCUMENTME)
				stop = 1;						//	(DOCUMENTME)
				var o = gui.Y;					//	(DOCUMENTME)
				o.cur.stamp = 1;				//	...
				o.prev.stamp = null;			//	Guessing this all loads and renders a 
				o.id = 'stamp';					//	stamp / glyph set into the control
				o.stamp();						//	...
				o.kontrol_update('stamp');		//	...
				vars.cache(1);
			},

			//--------------------------------------------------------------------------------------
			//	called when 'Pencil'|'Brush'|'Calligraphy' chosen in draw tool menu
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'draw' [string]
			//arg: o - DOM node? [object]			

			'draw': function (c, o) {
				gui.menu.fu.z(c, o);					//	Re-render menu, minimized
				var b = o.innerHTML;					//%	(Pencil|Brush|Calligraphy) [string]
				vars.draw = b;							//	Set current brush
				$C(vars.draw, 'tools')[0].title = b;	//	Update label in 'tools' window
				gui_tools.imageCurrent(b);				//	Update icon in 'tools' window
			},

			//--------------------------------------------------------------------------------------
			//.	called when spirograph type is chosen
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'spirograph' [string]
			//arg: o - DOM node? [object]

			'spirograph': function (c, o) {
				gui.menu.fu.z(c, o);			//	Re-render the nemu, minimized
				var b = o.innerHTML;			//%	'Epitrochoid'|'Hypotrochoid' [string]
				vars.type_spirograph = b;		//	Update global 'vars' object
			},

			//--------------------------------------------------------------------------------------
			//.	called by 'CO*' and 'GD*', in response to swatch window
			//--------------------------------------------------------------------------------------
			//
			//	this seems to set state of swatch window to match fill options window
			//
			//arg: c - index into 'vars' global, may be ('CO*'|'GD*') [string]
			//arg: o - DOM node? [object]
			//arg: v1 - Color or gradient? ('CO'|'GD') [string]
			//arg: v2 - A label? ('solid'|'gradient') [string]

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

			//--------------------------------------------------------------------------------------
			//.	called when a color palette is selected in the swatch window
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'CO*' [string]
			//arg: o - DOM node? [object]

			'CO*': function (c, o) {
				gui.menu.fu.sw(c, o, 'CO', 'solid');
			},

			//--------------------------------------------------------------------------------------
			//.	called when a gradient set is selected in the swatch window
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'GD*' [string]
			//arg: o - DOM node? [object]

			'GD*': function (c, o) {
				gui.menu.fu.sw(c, o, 'GD', 'gradient');
			},

			//--------------------------------------------------------------------------------------
			//.	called when a pattern library is selected in the swatch window
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'PT*' [string]
			//arg: o - DOM node? [object]			

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
				$('author_' + gui_swatch.id).innerHTML = gui_swatch.author();
			},

			//--------------------------------------------------------------------------------------
			//.	called when a shape is chosen in the tool options window
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'shape' [string]
			//arg: o - DOM node? [object]

			'shape': function (c, o) {
				gui.menu.fu.z(c, o);
				gui_tools.imageCurrent(vars.tool = 'Shape_' + (vars.shape = vars.shape.toLowerCase()));
			},

			//--------------------------------------------------------------------------------------
			//.	called when a marquee shape is chosen in the tool options window
			//--------------------------------------------------------------------------------------
			//arg: c - index into 'vars' global, always 'marquee' [string]
			//arg: o - DOM node? [object]

			'marquee': function (c, o) {
				gui.menu.fu.z(c, o);
				gui_tools.imageCurrent(vars.tool = 'Marquee_' + (vars.marquee = vars.marquee.toLowerCase()));
			}

		}, // end menu.fu()

		//------------------------------------------------------------------------------------------
		//.	re-show the menu minimized?
		//------------------------------------------------------------------------------------------
		//arg: v - name of a menu control [string]
		//arg: o - current value? [string]

		'klean': function (v, o) {
			//alert('gui.menu.clean(' + v + ', ' + o + ')');
			o = String(o);
			gui.menu.cur[v] = o;
			vars[v] = o;
			var a = o + '';
			var b = a.substr(0, a.indexOf('(') != -1 ? a.indexOf('(') - 1 : a.length);
			gui.menu.key[v] = b;
		},

		//------------------------------------------------------------------------------------------
		// 	menu properties
		//------------------------------------------------------------------------------------------

		'cellHeight': 17,
		'parent': {},
		'prev': {},

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------
		//arg: n (DOCUMENTME)

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

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME) called in by mouse event handlers from menu li elements in the dom
		//------------------------------------------------------------------------------------------
		//NOTE: this calls gui.menu.fu methods above
		//arg: n - DOM node? [object]
		//arg: s - DOM node? [object]

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

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------

		'close': function () {
			if (gui.menu.parent.opened == true) gui.menu.doSelect(gui.menu.prev)
		},

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME) mouse event handler for li elements, onClick
		//------------------------------------------------------------------------------------------

		'select': function (n, s) {
			var p = n.parentNode.parentNode;
			if (p.okClose == true) gui.menu.doSelect(n, s);
		},

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME) mouse event handler for li elements
		//------------------------------------------------------------------------------------------

		'okClose': function (p) {
			if (p.opened) p.okClose = true;
		}
	}, // end of gui.menu
	
	//----------------------------------------------------------------------------------------------
	//.	Y-Scroll - vertical scrollbar for stamp tool options, glyph pane, swatches, etc
	//----------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//.	lookup for changed/updated event, methods with these names will be called
		//------------------------------------------------------------------------------------------

		'fu': {
			'stamp': 'gui.Y.stamp',
			'hi': 'gui.Y.hi',
			'CO': 'gui.Y.sw',
			'GD': 'gui.Y.sw',
			'PT': 'gui.Y.sw'
		},

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------

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
			'CO': {									//	color
				'Y': 106,
				'n': function () {
					return (vars['CO'].length);
				},
				'display': 28,
				'col': 7,
				'row': 4
			},
			'GD': {									//	gradient
				'Y': 106,
				'n': function () {
					return (vars['GD'].length);
				},
				'display': 28,
				'col': 7,
				'row': 4
			},
			'PT': {									//	pattern
				'Y': 106,
				'n': function () {
					return (vars['PT'].length);
				},
				'display': 28,
				'col': 7,
				'row': 4
			}
		},
		
		//------------------------------------------------------------------------------------------
		//.	mousewheel event handler
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//.	slide
		//------------------------------------------------------------------------------------------
		//arg: i - index into gui.Y.r
		//arg: n (DOCU
		//returns: html [string]
		
		'kontrol': function (i, n) {
			var o = gui.Y,
			r = o.r[i],
			H = o.height(i);
			return ''
			 + '<div'
			 + ' id="' + i + 'Kontrol"'
			 + ' onmousedown="gui.Y.slide_fu(event,\'' + i + '\')"'
			 + ' class="slideY"'
			 + ' style="'
				 + 'top: ' + (!isNaN(n) ? n : 35) + 'px; '
				 + 'height: ' + r.Y + 'px; '
				 + 'display:' + (r.n() <= r.display ? 'none' : 'block')
			 + '">'
			 + ' <span class="rT"></span>'
			 + '<span class="rB" style="top:' + r.Y + 'px;"></span>'
			 + ' <div'
			 + ' id="' + i + 'Slide"'
			 + ' class="slider"'
			 + ' style="'
				 + 'position: relative; '
				 + 'height:' + o.height(i) + 'px; '
				 + 'top:' + o.top(o, r) + 'px;'
			 + '">'
			 + '  <div class="rT"></div>'
			 + '<div class="rB" style="top:' + H + 'px"></div>'
			 + ' </div>'
			 + '</div>';
		},

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//.	called by slide_fu, which handles onClick
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//.	onClick event handler for rendered slider
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------

		'height': function (i) {
			var o = gui.Y.r[i];
			return (Math.round(o.row / Math.ceil(o.n() / o.col) * o.Y));
		},

		//------------------------------------------------------------------------------------------
		//.	(DOCUMENTME)
		//------------------------------------------------------------------------------------------

		'top': function (o, r) {
			var cur = o.cur[o.id],
			n = r.row,
			H = o.height(o.id);
			if (o.id == 'hi') cur -= canvas.history_r.a - 6;
			return (cur <= 1 ? 0 : Math.round((cur / ((r.n() - r.display) + n)) * (r.Y - H)));
		},

		//------------------------------------------------------------------------------------------
		//.	active scroll (DOCUMENTME)
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//	called when swatches scrolled vertically
		//------------------------------------------------------------------------------------------
		//arg: s - seems to be undefined, DOM node? [object]

		'sw': function (s) {
			var r = gui.Y,
			c = r.cur;

			//--------------------------------------------------------------------------------------
			//	(DOCUMENTME)
			//--------------------------------------------------------------------------------------
			//arg: i - index into pattern array? (CHECKME) [int]
			//arg: r - array of patterns in this control? (CHECKME) [array]

			function fu(i, r) {
				if (i <= r.length) {
					if (gui_swatch.id == 'PT') {
						vars.PT[i - 1] = new Image();
						vars.PT[i - 1].id = i;
						vars.PT[i - 1].src = gui_pattern.dir + vars['PT*'] + '/' + String(vars.PT.length - i) + '-live.jpg';
					}
					return (''
					 + '<canvas'
					 + ' id="' + vars.id + gui_swatch.id + i + '"'
					 + ' height="16"'
					 + ' width="16"'
					 + (r.n == i ? ' class="cur"' : '')
					 + ' onmousedown="gui_swatch.click(this)"'
					 + ' title="' + String(vars.PT.length - i) + '">'
					 + '</canvas>');
				}
			}

			//TODO: tidy this mess

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
		}, // end gui.Y.sw()


		//------------------------------------------------------------------------------------------
		//.	called when stamp/glyph set is scrolled
		//------------------------------------------------------------------------------------------

		'stamp': function (s) {
			var r = gui.Y,
			c = r.cur;

			//--------------------------------------------------------------------------------------
			//	(DOCUMENTME)
			//--------------------------------------------------------------------------------------
			//arg: i - index into current stamp/glyph set? (CHECKME) [int]
			//arg: r - array of glyphs in current set? (CHECKME) [array]

			function fu(i, r) {
				if (i <= r.length) {
					stamp.src[i] = new Image();

					stamp.src[i].src = ''
					 + jsServerPath + 'modules/sketchpad/assets/media/glyph/'
					 + vars.stamp + '/'
					 + (i - 1) + '-thumb.png';

					stamp.src[i].id = i;

					return (''
					 + '<canvas'
					 + ' width="34"'
					 + ' height="34"'
					 + ' onmousedown="if(this.id.substr(5)!=stamp.fileNumber) { stamp.current(this); co.glyph(stamp.uri(\'live\'),this.id); }"' 
					 + ' id="stamp' + i + '"'
					 + (stamp.fileNumber == i ? ' class="cur"' : '') + '>'
					 + '</canvas>');
				}
			}

			if($('brush_author')) {
				var stampSet = Resources.Brushes[vars.stamp];
				if(!stampSet) {
					$('brush_author').innerHTML = '';				
				} else {
					$('brush_author').innerHTML = ''
					 + '<i'
					 + ' style="-moz-user-select: none; -khtml-user-select: none; user-select: none;"'
					 + '>by:&nbsp; '
					 + '<a href="'+stampSet.url+'" target="_blank">'+stampSet.name+'</a>'
					 + '</i>'
					 + '<div style="background: #555; height: 1px; margin: 6px 0 2px; "></div>';
				}
			}

			if (
				r.active(
					$('stamp'),
					4,
					c[r.id], c[r.id] + 11,
					{
						'change': function () { stamp.src = []; },
						'each': fu,
						'vars': { 'length': stamp.r[vars.stamp] }
					},
					s
				)
			) {
				for (var i in stamp.src) stamp.src[i].onload = function () {
					stamp.preview(this.id);
				};
			}
		},

		//------------------------------------------------------------------------------------------
		//.	called when history is scrolled
		//------------------------------------------------------------------------------------------

		'hi': function (s) {
			var r = gui.Y;
			var c = r.cur;

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
						return (''
						 + '<div'
						 + ' onmousedown="gui.Y.prev[gui.Y.id]=null; canvas.history_set(canvas.history_r.n=' + i + ',1)"'
						 + ' class="' + (r.n == i ? 'cur' : (i <= r.n ? 'keep' : 'discard')) + '"'
						 + '>'
						 + ' <div style="float: left">'
						 + '  <div'
						 + ' style="'
							 + 'background: url(' + jsServerPath + 'modules/sketchpad/assets/media/gui/tools/' + v.img + '.png); '
							 + 'opacity: ' + (i <= r.n ? 1.00 : 0.65)
							 + '"'
						 + '>'
						 + '</div>'
						 + ' </div>'
						 + ' <div style="float: left; margin-left: 3px;">' + v.type + '</div>'
						 + ' <span>' + (v.n ? '#' + i : '') + '</span>'
						 + '</div>');
					}
				}
			}
			r.active($C('z', 'history')[0], 1, c[r.id], c[r.id] + 7, { 'each': fu }, s);
		} // end gui.Y.hi()

	}, // end gui.Y
	
	//----------------------------------------------------------------------------------------------
	//.	horizontal scrollbar
	//----------------------------------------------------------------------------------------------
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

				a = ''
				 + '<div'
				 + ' id="' + o + i + 'Cur"'
				 + ' class="' + i + '"'
				 + ' style="'
					 + 'left: ' + Math.round(vars[o + i] / (i ? cF[o].val[i] : cF[o].val)[1] * 112) + 'px'
				 + '">'
				 + '</div>' + a;

				b = ''
				 + '<div id="' + o + i + 'CurV" class="v">'
				 + Math.round(vars[o + i])
				 + '</div>'
				 + (b ? '<div class="u">/</div>' : '') + b;
			}

			return (''
			 + '<div class="' + d + '">'
			 + '<span>' + v + '</span><br>'
			 + '<div'
			 + ' onmousedown="gui.X.' + (n > 1 ? 'xxSwitch' : 'xRun') + '(\'' + o + '\',event,this)"'
			 + ' class="slide_div"'
			 + '>'
			 + (n > 1 ? '<div id="' + o + 'cCur" class="slide_center"></div>' : '') + a
			 + '</div>'
			 + b + '<br>'
			 + '</div>'
			 + (d == 'slideXX' ? '<br style="margin-top: 26px">' : '')
			);
		}, // end gui.X.build()

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

//==================================================================================================
// WINDOW OBJECT
//==================================================================================================

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
};
