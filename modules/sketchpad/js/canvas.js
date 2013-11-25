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
