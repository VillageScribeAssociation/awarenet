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

					image.style.backgroundImage = ''
					 + 'url('
						 + jsServerPath + 'modules/sketchpad/assets/media/gui/tools/Tools.png'
					 + ')';

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
				o.src = jsServerPath + 'modules/sketchpad/assets/media/gui/tools/' + v + '_2.png';
				d.backgroundImage = '';
			} else if (i == 0) {
				o.src = '';
				o.style.display = "none";
				d.backgroundImage = 'url(' + jsServerPath + 'modules/sketchpad/assets/media/gui/tools/Tools.png)';
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
				img1.src = jsServerPath + 'modules/sketchpad/assets/media/gui/tools/' + prev + '.png';
				for (var i = 25; i >= 0; i--) {
					zoom(prev, 0);
				}
			}
			var timer = 0,
			img2 = new Image();
			img2.src = jsServerPath + 'modules/sketchpad/assets/media/gui/tools/' + v + '_2.png';
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
				z = ''
				 + '<img'
				 + ' src="' + jsServerPath + 'modules/sketchpad/assets/media/gui/loupe.png"'
				 + ' onmousedown="return(noMove())"'
				 + ' class="loupe" alt="...">'
				 + '<canvas'
				 + ' id="picker"'
				 + ' height="106"'
				 + ' width="149"'
				 + ' style="height: 106px; width: 149px"'
				 + '></canvas>'
				 + '<div class="picker">'
				 + '<div>R<br>G<br>B<br>A</div><div id="picker_hex">0<br>0<br>0<br>0</div>'
				 + '</div>'
				 + '<canvas id="picker1x1" height="1" width="1"></canvas>';

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
			co.glyph(jsServerPath + 'modules/sketchpad/assets/media/glyph/Calligraphy/0-live.png');
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
	'dir': jsServerPath + 'modules/sketchpad/assets/media/patterns/'
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
