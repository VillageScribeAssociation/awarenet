// DRAW

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
		if(m == "down" || m == "move") {
			c.beginPath();
			c.font = vars.fontSize + "px Liberation Sans, sans-serif";
			if(c.setFont) c.setFont(vars.fontSize + "px Liberation Sans, sans-serif");
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
		return jsServerPath + 'modules/sketchpad/assets/media/glyph/' + vars.stamp + '/' + (stamp.fileNumber - 1) + '-' + v + '.png';
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
