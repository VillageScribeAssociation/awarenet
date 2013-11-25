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

