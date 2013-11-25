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