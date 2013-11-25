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
	'constrain':function(a,b) {

		if (!gui.menu.key.crop) { alert('no gui.menu.key.crop'); }
		if (!crop.ratio[gui.menu.key.crop]) { crop.ratio[gui.menu.key.crop] = 1; }

		var r = crop.ratio[gui.menu.key.crop]; //- JUNK
	
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
	
};
