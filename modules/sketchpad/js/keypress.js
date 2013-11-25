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
