<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" manifest="sketchpad.appcache">
<head>
	<title>Sketchpad - Online Paint/Drawing application</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="Sketchpad is an online drawing application -- written in &lt;canvas&gt;." />
	<meta name="keywords" content="" />
	<link rel="shortcut icon" href="/media/gui/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="media/gui/favicon.png" type="image/x-icon" />
	<link rel="chrome-application-definition" href="%%serverPath%%modules/sketchpad/assets/sketchpad.json" />
	<link href="%%serverPath%%sketchpad/css/" rel="stylesheet" type="text/css" />

	<!--[if IE]>
	<OBJECT ID="canvasFactory"  CLASSID="clsid:785740C4-DD04-4B91-8AD7-44CC9FFB4984"></OBJECT>
	<style>
		canvas {
			behavior: url(#canvasFactory);
			width: 300px; height: 150px; display: block;
			margin: 0; border: 0; background: transparent!important;
		}
	</style>
	<canvas id="CanvasRenderingContext2D" style="display: none" />
	<script>
		// CanvasRenderingContext2D.prototype = CanvasRenderingContext2D.getContext('2d').prototype;
		if(0) document._createElement = document.createElement;
		if(0) document.createElement = function(tagName) {
			var tag = document._createElement(tagName);
			if(tagName.toUpperCase() == 'CANVAS') tag.addBehavior("#canvasFactory");
			return tag;
		};
	</script>
	<![endif]-->

	<script type="text/javascript">

		//	important kapenta globals

		jsServerPath = '%%serverPath%%';			//	important kapenta globals
		jsDefaultTheme = '%%defaultTheme%%';

		marqueeID = false;							//	(DOCUMENTME)

		mixin = function(o) {						//	dojo.mixin (DOCUMENTME)
			var len = arguments.length;
			for(var i = 1; i < len; i++) {
				for(var j in arguments[i]) {
					o[j] = arguments[i][j];
				}
			}
			return o;
		};

		var dtx2D = document.createElement("canvas");					//	global pointer to canvas
		var ctx2D; try { ctx2D = dtx2D.getContext('2d'); } catch(e) {};	//	global pointer to 2d CTX

		//------------------------------------------------------------------------------------------
		//	creates an array of canvas patterns in obj from an array of image urls
		//------------------------------------------------------------------------------------------
		//arg: obj - an array of canvas patterns [array:object]
		//arg: obj - an array of image URL/DATAURLs [array:string]

		var data2pattern = function(obj, data) {
			// if(dtx2D.parentNode === null) document.body.appendChild(dtx2D);
			if(ctx2D == null) { ctx2D = dtx2D.getContext('2d'); }

			//--------------------------------------------------------------------------------------
			//	create a pattern on the 2D context of the canvas
			//--------------------------------------------------------------------------------------
			//arg: src - URL/DATAURL of an image (CHECKME) [string]
			//arg: id - index of the new pattern in a obj [int]

			function createPattern(src, id) {
				//alert('id: ' + id + ' url: ' + src);

				var image = new Image();
				image.onload = function() {
					obj[id] = ctx2D.createPattern(image, "repeat");
				};
				image.src = src;
			};

			for(var key in data) {
				createPattern(data[key], key);
			}
		};

	 </script>
	 <!-- Sketchpad -->

	<script src="%%serverPath%%modules/sketchpad/js/gui.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/Canvas2D.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/dump.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/global.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/canvas.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/gui-interface.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/keypress.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/tools.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/tools-crop.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/tools-draw.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/Media.js" type="text/javascript"></script>
	<script src="%%serverPath%%modules/sketchpad/js/Sketchpad.js" type="text/javascript"></script>
	<!-- VersionManager -->
</head>
<body onmousedown="if(false && preventDefault) event.preventDefault(); preventDefault = true;">

<!-- top menu of application -->
<div id="top">
	<div class="center">
		<div class="west" style="font-variant: small-caps">
			<div
				style="left: 12px; top: 10px"
				onmousedown="canvas.mode_sw('paint');"
				ontouchstart="canvas.mode_sw('paint');"
				id="paint"
				class="cur">
			paint
			</div>
			<div
				style="left: 24px; top: 10px"
				onmousedown="canvas.mode_sw('light');"
				ontouchstart="canvas.mode_sw('light');"
				id="light">
			light
			</div>
		</div>
		<div class="east">
			<div
				style="
					background: none;	
					border-left: none;
					border-right:1px solid #161616;
					height: 22px;
					padding: 0;
					cursor: default">
			</div>
			<div
				id="isolid"
				onmousedown="win.tab(this,'solid','solid'); if(this.className != 'cur') { gui_swatch.toType('CO'); }"
				ontouchstart="win.tab(this,'solid','solid'); if(this.className != 'cur') { gui_swatch.toType('CO'); }"
			>color</div>

			<div
				id="igradient"
				onmousedown="win.tab(this,'gradient','gradient'); if(this.className != 'cur') gui_swatch.toType('GD');"
				ontouchstart="win.tab(this,'gradient','gradient'); if(this.className != 'cur') gui_swatch.toType('GD');"
			>gradient</div>

			<div
				id="ipattern"
				onmousedown="win.tab(this,'pattern','pattern'); if(this.className != 'cur') gui_swatch.toType('PT');"
				ontouchstart="win.tab(this,'pattern','pattern'); if(this.className != 'cur') gui_swatch.toType('PT');"
			>pattern</div>

			<div
				id="iswatch"
				onmousedown="win.tab(this,'swatch',vars[vars.id])"
				ontouchstart="win.tab(this,'swatch',vars[vars.id])"
			>swatch</div>
			<div
				id="ihistory"
				onmousedown="win.tab(this,'history',vars[vars.id])"
				ontouchstart="win.tab(this,'history',vars[vars.id])"
			>history</div>
			<div
				id="ioptions"
				onmousedown="win.tab(this,'options',vars[vars.id])"
				ontouchstart="win.tab(this,'options',vars[vars.id])"
			>options</div>
			<div
				onmousedown="canvas.open();"
				ontouchstart="canvas.open();"
				class="cur open"
				style="display:none"
			><span></span></div>

			<a href="#" onclick="window.saveDrawing(); return false;" target="_blank">
				<div class="cur save"><span></span></div>
			</a>
			<div
				style="
					background: none;
					border-left: none;
					border: none;
					border-left:1px solid #3e3e3e;
					height: 22px;
					padding: 0;
					cursor: default">
			</div>
		</div>
	</div>
</div>

<!-- tools window -->
<div id="tools" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM"><div class="TMM">tools</div></div>
		<div class="TR"></div>
		<br/>
		<div class="ML"></div>

		<div class="MM" onmousedown="noMove();">
			<div class="z">
				<div class="tools">

					<!-- marquee tool -->
					<div onclick="gui_tools.imageCurrent('Marquee_'+vars.marquee)">
						<img src="%%serverPath%%modules/sketchpad/assets/media/gui/sw_add.png" class="plus" alt="Marquee" />
						<img
							src=""
							class="Marquee_lasso Marquee_ellipses Marquee_polygon Marquee_star Marquee_burst Marquee_gear"
							alt="Marquee"
						/>
					</div>

					<!-- crop tool -->
					<div onclick="gui_tools.imageCurrent('Crop')">
						<img src="" class="Crop" alt="Crop" />
					</div>
					<br />

					<div class="hr"></div>

					<!-- text pattern tool -->
					<div onclick="gui_tools.imageCurrent('Text')">
						<img src="" class="Text" alt="Text" />
					</div>

					<!-- shape tool -->
					<div onclick="gui_tools.imageCurrent('Shape_'+vars.shape)">
						<img src="%%serverPath%%modules/sketchpad/assets/media/gui/sw_add.png" class="plus" alt="Shape" />
						<img src="" class="Shape_ellipses Shape_polygon Shape_star Shape_burst Shape_gear" alt="Shape" />
					</div>

					<!-- spirograph tool -->
					<div onclick="gui_tools.imageCurrent('Spirograph')">
						<img src="" class="Spirograph" alt="Spirograph" />
					</div>

					<!-- draw tool -->
					<div onclick="gui_tools.imageCurrent(vars.draw)">
						<img src="%%serverPath%%modules/sketchpad/assets/media/gui/sw_add.png" class="plus" alt="Draw" />
						<img src="" class="Brush Calligraphy Pencil" alt="Draw" />
					</div>

					<!-- fill tool -->
					<div onclick="gui_tools.imageCurrent('Fill')">
						<img src="" class="Fill" alt="Fill" />
					</div>

					<!-- stamp tool -->
					<div onclick="gui_tools.imageCurrent('Stamp')">
						<img src="" class="Stamp" alt="Stamp" />
					</div>

					<!-- eraser tool -->
					<div onclick="gui_tools.imageCurrent('Eraser')">
						<img src="" class="Eraser" alt="Eraser" />
					</div>

					<!-- picker tool -->
					<div onclick="gui_tools.imageCurrent('Picker')">
						<img src="" class="Picker" alt="Picker" />
					</div>

					<br/>

					<div class="hr"></div>
				</div>

				<!-- fill / stroke colors control -->
				<div class="ss">
					<div id="swap">F</div>
					<div class="fill">
						<canvas onmousedown="gui_palette.click('fill')" id="fill" title="Fill"></canvas>
					</div>
					<div class="stroke">
						<canvas onmousedown="gui_palette.click('stroke')" id="stroke" title="Stroke"></canvas>
						<div onmousedown="gui_palette.click('stroke')">
							<div></div>
						</div>
						<img
							src="%%serverPath%%modules/sketchpad/assets/media/gui/sw_switch.png"
							style="display: block"
							onmousedown="gui_palette.current(); return false"
							alt="Switch Stroke with Fill"
						/>
					</div>
				</div>
				<!-- end fill /stroke colors control -->
			</div>
		</div>

		<div class="MR"></div>
		<br />
		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>
<!-- end of tools window -->

<!-- tool options window -->
<div id="options" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span class="TML"></span>
			<div class="TRx">
				<img
					onclick=";win.close('options',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>
		<div class="TR"></div>
		<br/>
		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"></div>
		<div class="MR"></div>
		<br/>
		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>
<!-- end tool options window -->

<!-- history window -->
<div id="history" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span class="TML">history</span>
			<div class="TRx">
				<img
					onclick="win.close('history',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>

		<div class="TR"></div>
		<br/>

		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"></div>
		<div class="MR"></div>
		<br/>

		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>

<!-- swatch window -->
<div id="swatch" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span class="TML">swatch</span>
			<div class="TRx">
				<img
					onclick="win.close('swatch',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>
		<div class="TR"></div>
		<br/>

		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"></div>
		<div class="MR"></div>
		<br/>

		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>

<!-- pattern window -->
<div id="pattern" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span class="TML">pattern</span>
   			<div class="TRx">
				<img
					onclick="win.close('pattern',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>
		<div class="TR"></div>
		<br/>

		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"><div class="z"></div></div>
		<div class="MR"></div>
		<br/>

		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>

<!-- gradient window -->
<div id="gradient" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span class="TML">gradient</span>
			<div class="TRx">
				<img
					onclick="win.close('gradient',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>
		<div class="TR"></div>
		<br/>

		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"></div>
		<div class="MR"></div>
		<br/>

		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>

<!-- solid color details window -->
<div id="solid" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span
				id="HEX"
				class="TML"
				title="HEX"
				onmousedown="preventDefault = false;"
				style="cursor: text; text-transform:uppercase">FFFFFF</span>

			<div class="TRx">
				<img
					onclick="win.close('solid',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>
		<div class="TR"></div>
		<br/>

		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"></div>
		<div class="MR"></div>
		<br/>

		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>


<!-- export window -->
<div id="export" class="gui">
	<div class="bounds">
		<div class="TL"></div>
		<div class="TM">
			<span class="TML">pattern</span>
   			<div class="TRx">
				<img
					onclick="win.close('export',1)"
					onmousedown="return(noMove())"
					src="%%serverPath%%modules/sketchpad/assets/media/gui/win_close.png"
					title="Close"
				/>
			</div>
		</div>
		<div class="TR"></div>
		<br/>

		<div class="ML"></div>
		<div class="MM" onmousedown="noMove()"><div class="z"></div></div>
		<div class="MR"></div>
		<br/>

		<div class="BL"></div>
		<div class="BM"></div>
		<div class="BR"></div>
	</div>
</div>
<!-- main canvas window TODO: document this -->
<div
	id="canvas"
	onmousedown="core.fu('canvas',event,{fu:core.win,Y1:19})"
	ontouchstart="core.fu('canvas',event,{fu:core.win,Y1:19})"
	class="gui"
	style="width: %%width%%px; height: %%height%%px;">

	<div class="TL"></div>
	<div class="TM" ondblclick="win_size.tog(vars.winMax?'min':'max')">
		<div class="center"><span id="cTitle">%%title%% (imported from awareNet)</span></div>
		<div class="east" id="cZoom"></div>
	</div>
	<div class="TR"></div>
	<br/>

	<div class="ML"></div>
	<div class="MM">
		<div class="z">
			<div id="cBound" onmousedown="return(noMove())">
				<canvas id="ctx_box" style="z-index: 1"></canvas>
				<canvas id="ctx_temp" style="z-index: 2"></canvas>
				<canvas id="ctx_active" style="z-index: 5"></canvas>
				<canvas id="ctx_marquee" style="z-index: 6"></canvas>
				<canvas id="ctx_mouse" style="z-index: 7"></canvas>
			</div>
			<canvas id="ctx_brush" width="200" height="200"></canvas>
			<canvas id="ctx_stamp" width="200" height="200"></canvas>
			<canvas id="ctx_eraser" width="200" height="200"></canvas>
			<canvas id="ctx_picker" height="1" width="1"></canvas>
			<div id="cHistory"></div>
		</div>
	</div>
	<div class="MR"></div>
	<br/>

	<div class="BL"></div>
	<div class="BM"></div>
	<div class="BR" onmousedown="vars.winMax='min'; win_size.core(event)"></div>
</div>

<div style="display: none" id="canvas_open">
	<div style="padding: 15px 0;">
		My Computer&hellip;<br/>
		<input type="file" id="local" size="17"><br>
		<br>
		The Internet&hellip;<br>
		<input type="datafile_url" id="server" size="27"><br>
		<br>
		<input onclick="canvas.open_event()" type="submit" value="open" />
		<div id="canvas_open_error" style="padding: 4px 0 0 0; color: #ef3333"></div>
	</div>
</div>

<div id="TEST" style="color: #fff; position: absolute; top: 40px; z-index: 100000"></div>

<script type="text/javascript">

	var preventDefault = true;					//%	(DOCUMENTME) [bool]

	//----------------------------------------------------------------------------------------------
	//	object which initializes the canvas? (CHECKME)
	//----------------------------------------------------------------------------------------------

	var init = {

		//------------------------------------------------------------------------------------------
		//	initialize the canvas
		//------------------------------------------------------------------------------------------
		//arg: w - initial width of the canvas [int]
		//arg: h - initial height of the canvas [int]
		//arg: u - url of default image (TODO: rename)

		'canvas': function (w, h, u) {
			gui_swatch.id = 'CO';

			u = jsServerPath + '%%imageUrl%%';

			function z(n) { return (Math.floor(Math.random() * n)); }		//	TODO: remove

			$('ctx_box').width = canvas.W = w;
			$('ctx_box').height = canvas.H = h;
			crop.apply({ X: 0, Y: 0 }, { X: w, Y: h });

			if (isNaN(vars.winW) || isNaN(vars.winH)) {
				vars.winW = parseInt(canvas.W);
				vars.winH = parseInt(canvas.H);
			}

			if (u) {
				//----------------------------------------------------------------------------------
				//	load a preset image
				//----------------------------------------------------------------------------------
				var img = new Image();
				img.src = u;
				img.onload = function () {
	                var c = $2D('ctx_box');
	                co.del(c);
	                c.drawImage(img, 0, 0, canvas.W, canvas.H);
	            }

			} else {
				//----------------------------------------------------------------------------------
				//	chose and apply a random gradient
				//----------------------------------------------------------------------------------
	            var a = { X: 0, Y: 0 }, b = { X: w, Y: h }, c = $2D('ctx_box');
				c.rect(0, 0, w, h);
				co.gradient(a, b, c, vars.GD[z(vars.GD.length)], 'fill', 1);

			} 

	    }, // end this.canvas();

		//------------------------------------------------------------------------------------------
		//.	Initialize conntent
		//------------------------------------------------------------------------------------------

		'content': function () {
			//Windows
			if (vars.winMax == 1) {
				win_size.max();
			} else if (!isNaN(vars.winW)) {
				win_size.fu({
                	W: zero(vars.winW),
                	H: zero(vars.winH)
            	},
				win_size.construct({}));
			} else {
				canvas.resize(%%width%%, %%height%%);	// IMAGE DIMENSIONS INJECTED HERE **********
			}

			init.canvas(%%width%%, %%height%%);			// IMAGE DIMENSIONS INJECTED HERE **********
			gui.options();
			gui_tools.imageMap();			//	(DOCUMENTME)

			//Interface
			gui_palette.update('stroke');	//	(DOCUMENTME)
			gui_palette.update('fill');		//	(DOCUMENTME)
			gui_palette.zindex(vars.id);	
			crop.ratio_mk();
			gui_color.mk();
			gui_gradient.mk();
			gui_pattern.mk();
			gui_swatch.mk();
			win.feed();
			gui_tools.imageCurrent(vars.tool);
			canvas.mode_sw(vars.mode = vars.mode ? vars.mode : 'paint');
			canvas.history_mk();
			init.events();
		},

		//------------------------------------------------------------------------------------------
		//.	attach mouse events to canvas div
		//------------------------------------------------------------------------------------------

		'events': function () {
			//Canvas
			var o = $('cBound');					//	div containing canvases

			o.oncontextmenu = function (e) {
				if ({
					'zoom': 1,
					'path': 1,
					'shape': 1,
					'marquee': 1
				} [vars.type]) return false;
			};

			o.ondblclick = function (e) {
				if (vars.type == 'text') {
	            	noMove()
					//co.core(e, text.core);
				}
			};

			o.onmousemove = function (e) {
				if (stop) {

					if (
						{
							'marquee': 1,
							'text': 1,
							'crop': 1
						} [vars.type]			//	(DOCUMENTME)
					) {
						mouse.cursor(e, this);
					}

					if (vars.type == 'picker') {
						var a = XY(e);
						a.X -= abPos(this).X;
						a.Y -= abPos(this).Y;
						a.X = Math.max(0, Math.min(canvas.W - 1, a.X));
						a.Y = Math.max(0, Math.min(canvas.H - 1, a.Y));
						picker.core(a, '', 'move');
					}
				}
			};

			o.ontouchmove = o.onmousemove;

			o.onmousedown = function (e) {

				if (e.touches) {
					e.clientX = e.touches[0].clientX;
					e.clientY = e.touches[0].clientY;
				}

				if (vars.type == 'crop') {
					co.core(e, crop.core);
				} else if (vars.type == 'fill') {
					co.core(e, draw.fill);
				} else if (vars.type == 'marquee') {
					co.core(e, marquee.core);
				} else if (vars.type == 'picker') {
					var a = XY(e);	
					a.X -= abPos(this).X;
					a.Y -= abPos(this).Y;
					a.X = Math.max(0, Math.min(canvas.W - 1, a.X));
					a.Y = Math.max(0, Math.min(canvas.H - 1, a.Y));
					picker.core(a, a, 'down', e);
				} else if (vars.type == 'shape') {
					co.core(e, draw.shape);
				} else if (vars.type == 'text') {
					co.core(e, draw.text);
				} else if ({
					'calligraphy': 1,
					'stamp': 1
				} [vars.type]) {
					if (stamp.loaded) {
						co.core(e, draw[vars.type]);
					} else {
						noMove();
					}
				} else if(vars.type == 'spirograph') {
					co.core(e, draw.spirograph);
				} else if ({
					'brush': 1,
					'pencil': 1,
					'eraser': 1
				} [vars.type]) {
					co.core(e, draw[vars.type]);
				} else {
					return noMove();
				}
				return false;
			};

			o.ontouchstart = o.onmousedown;

			o.onmouseout = function (e) {
				if (stop) {
					if (vars.type == 'picker') {
						var a = XY(e);	
						a.X -= abPos(this).X;
						a.Y -= abPos(this).Y;
						a.X = Math.max(0, Math.min(canvas.W - 1, a.X));
						a.Y = Math.max(0, Math.min(canvas.H - 1, a.Y));
						picker.core(a);
					}
				}
			};

			o.ontouchend = o.onmouseout;

			//	mouse wheel - note that this reassigns 'o', which was previously a div element
			var o = {
				'stamp': $C('MM', 'options'),
				'hi': $C('MM', 'history'),
				'CO': $C('CO', 'swatch'),
				'GD': $C('GD', 'swatch'),
				'PT': $C('PT', 'swatch')
			};

			function addWheel(id) {
				Event.add(
					o[id][0],
					{
						el: 'DOMMouseScroll',
						e: 'onmousewheel'
					},
					function (event) {
						gui.Y.id = id;
						gui.Y.wheel(event);
						event.preventDefault();
					}
				);
	        };

			//	attach mousewheel event to window divs
			for (var i in o) { addWheel(i); }

			//	window CoreXY - note that this reassigns 'o' yet again
			var o = $C('gui', document.body);

			for (var i = 0; i < o.length; i++) {
				if (o[i].onmousedown) { continue; }		//	do not replace existing event handler
				Event.add(
					o[i],
					{
						el: 'mousedown',
						e: 'onmousedown'
					},
					function (event) {
						core.fu(
							this.id,
							event,
							{
								fu: core.win,
								Y1: 19,
								z: true
							}
						);
	            	}
				);
				Event.add(
					o[i],
					{
						el: 'mousedown',
						e: 'ontouchstart'
					},
					function (event) {
						core.fu(
							this.id,
							event,
							{
								fu: core.win,
								Y1: 19,
								z: true
							}
						);
	            	}
				);
			}
		},	//	end events()

		//------------------------------------------------------------------------------------------
		//.	set images used in path selection - called by this.swatch() below
		//------------------------------------------------------------------------------------------

		'images': function () {
			var dir = jsServerPath + 'modules/sketchpad/assets/media/gui/';
			op_8x8 = new Image();
			op_8x8.src = dir + 'op_8x8.gif';							//	transparency tile
			path = {
				point: new Image(),
				node_select: new Image()
			}
			path.point.src = dir + 'point.png';							//	path node
			path.node_select.src = dir + 'node_select.png';				//	selected path node
		},

		//------------------------------------------------------------------------------------------
		//.	appears to set up the swatch by loading a random pattern and random colors?
		//------------------------------------------------------------------------------------------

		'swatch': function () {
			var rand = N.rand;
			init.images();
			if(typeof ScreenMetrics == 'function') {		//	(DOCUMENTME)
				$.metrics = ScreenMetrics();
			}

			//--------------------------------------------------------------------------------------
			//|	helper method to add a pattern to vars, gui_pattern and gui_swatch structures
			//--------------------------------------------------------------------------------------
			//assumes that vars['PT*'] is set to the name of a pattern library
			//arg: v - index number of a pattern [int]
			//arg: n - appears unused, assume DEPRECATED [int]

			function PT(v, n) {
				var n = vars.PT.length;						//%	number of patterns in this library
				var random;									//%	initially selected pattern

				if(vars['PT*'] == 'Squidfingers') {
					//	special case for this pattern library, 82: blue and white 105: gold leaves
					random = Math.random() > .5 ? '82' : '105';
	    		} else {
					random = rand(n);
				}

				src = gui_pattern.dir + vars['PT*'] + '/' + (gui_swatch.n[v + 'PT'] = random) + '-live.jpg';
				gui_pattern.o[v].src = src;
				vars[v + 'PT'].src = src;
				gui_swatch.n[v + 'PT'] = n - gui_swatch.n[v + 'PT'];
			}

			//--------------------------------------------------------------------------------------
			//|	set fill and stroke colors to random values? (CHECKME)
			//--------------------------------------------------------------------------------------
			//arg: v - (DOCUMENTME)

			function CO(v) {
				var n = vars[v].length;
				var a = rand(n);			//%	(DOCUMENTME) [float]
				var z = rand(n);			//%	(DOCUMENTME) [float]

				vars['fill' + v] = vars[v][a];
				gui_swatch.n['fill' + v] = a + 1;
				vars['stroke' + v] = vars[v][z];
				gui_swatch.n['stroke' + v] = z + 1;
			}

			vars.CO = Q.CO[vars['CO*']];	//	(DOCUMENTME)
			vars.GD = Q.GD[vars['GD*']];	//	(DOCUMENTME)
			vars.PT = Q.PT[vars['PT*']];	//	(DOCUMENTME)

			CO('CO');						//	stroke color? (CHECKME)
			CO('GD');						//	fill / ground color?	(CHECKME)
			PT('fill');
			PT('stroke');

			// setTimeout( init.content, 1000);
        
			gui_pattern.o.fill.onload = function () {
				if (gui_pattern.o.stroke.loaded) init.content();
				gui_pattern.o.fill.loaded = 1;
			};

			gui_pattern.o.stroke.onload = function () {
				if (gui_pattern.o.fill.loaded) init.content();
				gui_pattern.o.stroke.loaded = 1;
			};
		} // end swatch()

	}; // end init object

	var ants = [ ];						//%	(DOCUMENTME) [array]
	var ants_n = 0;						//%	(DOCUMENTME) [int]

	window.onresize = win.feed;			//%	(DOCUMENTME) [function]
	window.currentBlob = null;			//%	(DOCUMENTME)
	window.currentPort = null;			//%	(DOCUMENTME)

	//----------------------------------------------------------------------------------------------
	//|	overridden in awareNet to allow custom save method with AJAX to store in user gallery
	//----------------------------------------------------------------------------------------------

	window.saveDrawing = function() {

		//TODO: confirm with user
		//alert('Saving...');

		//------------------------------------------------------------------------------------------
		//	clear editor display
		//------------------------------------------------------------------------------------------
		
		//TODO: 

		//------------------------------------------------------------------------------------------
		//	create XMLHttpRequest object
		//------------------------------------------------------------------------------------------
		var req = new XMLHttpRequest();  

		var sendUrl = jsServerPath + 'sketchpad/save/';
		var params = ''
		 + 'action=save'
		 + '&img64=' + $('ctx_box').toDataURL().replace(/^data:image\/(png|jpg);base64,/, "")
		 + '&title64=%%title64%%';

		//alert(params);

		req.open('POST', sendUrl, true);  
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		req.setRequestHeader('Content-length', params.length);
		req.setRequestHeader('Connection', 'close');

		//------------------------------------------------------------------------------------------
		//	create handler for returned content
		//------------------------------------------------------------------------------------------ 
		req.onreadystatechange = function (aEvt) {  
			var theDiv = document.getElementById('divSaveNotice');
			//alert('sending: ' + req.status);

			if (4 == req.readyState) {
				//TODO: detect failures and redirect to 'my sketches' or somewhere sensible

				if (-1 == req.responseText.indexOf('</error>')) {
					theDiv.innerHTML = ''
					 + "<h1 style='color: #aaaaaa;'>Redirecting...</h1>"
					 + "<pre style='color: #aaaaaa;'>" + req.responseText + "</pre>";

					window.location = jsServerPath + req.responseText;

				} else {
					theDiv.innerHTML = ''
					 + "<h1 style='color: #aaaaaa;'>Upload failed, redirecting...</h1>"
					 + "<pre style='color: #aaaaaa;'>" + req.responseText + "</pre>";

					alert('Unable to save sketch, sorry :-(');
					window.location = jsServerPath + 'gallery/list/';

				}

			}
		}

		//------------------------------------------------------------------------------------------
		//	send the request
		//------------------------------------------------------------------------------------------ 
		req.send(params);

		document.body.innerHTML = ''
		 + "<div id='divSaveNotice'>"
		 + "<h1 style='color: #aaaaaa;'>"
		 + "<img src='%%serverPath%%themes/clockface/images/throbber-window.gif'>&nbsp;"
		 + "Saving to your gallery, please wait...</h1>"
		 + "<p style='color: #aaaaaa;'>"
		 + "This may take a few minutes on slower internet connections."
		 + "</p>"
		 + "</div>";

		document.body.style.background = '#090909';

		//	original method:
		//	window.location.href=$('ctx_box').toDataURL();

	};

	window.onload = function() {

		//------------------------------------------------------------------------------------------
		//#	not used in awareNET - TODO: discover is this has any potential use for us
		//------------------------------------------------------------------------------------------

		if(document.location.hash == '#snapshot') {
			document.body.style.background = 'transparent';

			// FIXME: Waiting on toBlob for Canvas
			window.saveDrawing = function(e) {
				if(!window.currentPort) { window.href=$('ctx_box').toDataURL(); }

				var n = $('ctx_box').toDataURL();
				window.currentPort.postMessage("send", '*');
				window.currentPort.postMessage(n, '*');
				return false;
   			};

			window.onmessage = function(event) {
				if (
					(event.origin.indexOf('chrome-extension:') == -1) &&
					(event.origin != 'http://mugtug.com') &&
					(event.origin != 'https://mugtugdarkroom.appspot.com') &&
					(event.origin != 'https://mugtugsketchpad.appspot.com')
				) { return; }

				if(event.source != window.self) { event.source.postMessage("recv", '*'); }

				var data = event.data;
				if(typeof(data) == 'object' && data.type) {
					if (window.currentBlob) { webkitURL.revokeObjectURL(window.currentBlob); }
					window.currentBlob = webkitURL.createObjectURL(data);
					data = window.currentBlob;
					window.currentPort = event.source;
				}

				if(data.indexOf(':') > -1) {		//	event.data is a DATAURL?
					var a = document.createElement('img');

					a.onload = function() {
						var width = a.naturalWidth;
						var height = a.naturalHeight;
						win_size.fu({W: width, H: height}, win_size.construct({}));
						canvas.resize(width, height);
						init.canvas(width, height, data);
					}

					a.src = data;
				}
			} // end window.onmessage()

			//--------------------------------------------------------------------------------------
			//	check if this app is in an iframe
			//--------------------------------------------------------------------------------------

			if(window.parent && window.parent != window.self) {
				// FIXME: Don't ack until load, otherwise we'll overwrite the image.
				setTimeout(
					function() {
						if(window.parent.postMessage) window.parent.postMessage('ack', '*');
					},
					300
				);
			}
		} // end if document.location.hash == '#snapshot'

		//------------------------------------------------------------------------------------------
		//	WARNING: possible race condition on slow clients
		//------------------------------------------------------------------------------------------

		setTimeout(
			function() {
				dtx2D = document.createElement("canvas");
				ctx2D = dtx2D.getContext('2d');
			},
			100
		);

		setTimeout( init.swatch, 250 );

		setTimeout(
			function() {
				data2pattern(ants,
					[
					"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAMklEQVQY"
					 + "lYXOtw0AMAzEQO6/NN04QAlW+cdCAALmOzsftGjAHGRUX9DhDSbcNmMJuocXA4afYTYwTaEAAAA"
					 + "ASUVORK5CYII=",
					"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAMklEQVQY"
					 + "lYXPsQ0AMAjEQO+/tFOkIALxQaI6FwDgu334YAUL3iCgQ/tNJFQr2L4hoeoBA4afYdiStBMAAAA"
					 + "ASUVORK5CYII=",
					"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAMklEQVQY"
					 + "lWP4DwUMDAwoGC6OTxIqh1sSQwEWSYQCHJIQBXgk/2PIoruJAZ/k/////wMAA4afYVpnmEkAAAA"
					 + "ASUVORK5CYII=",
					"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAALUlEQVQY"
					 + "lWP4jwYYGBhQMT7J////IxRgk4QrwCUJlcMtiaEAh4PxSkIU4PMqAAOGn2Gql3FAAAAAAElFTkS"
					 + "uQmCC"
					]
				);
			},
			200
		);
	} // end window.onload()

//==================================================================================================
///------  PARTS OF LIBRARIES
//==================================================================================================

Color = { };

//--------------------------------------------------------------------------------------------------
//.	(DOCUMENTME)
//--------------------------------------------------------------------------------------------------
//arg: o - object? 

Color.HEX_STRING = function(o) { 
	var z = o.toString(16);
	var n = z.length; 
	while(n < 6) { z = '0' + z; n++; }
	return z;
}; 

//--------------------------------------------------------------------------------------------------
//.	convert RGB to HSV color space
//--------------------------------------------------------------------------------------------------
//see: http://en.wikipedia.org/wiki/HSL_and_HSV
//arg: o - an RGB color object (CHECKME) [object]

Color.RGB_HSV = function(o) { //- RGB from 0 to 255

	//	0': red primary 120': blue primary 240': green primary 360': red primary

	var _R = o.R / 255;					//%	red [int]
	var _G = o.G / 255;					//%	green [int]
	var _B = o.B / 255;					//% blue [int]

	var H;								//%	hue, (0 - 360 degrees) [float]
	var S;								//%	saturation (0 - 100 percent) [float]
	var V;								//%	value / brightness (0 - 100 percent) [float]
	
	var min = Math.min(_R, _G, _B);		//%	minimum of all color channels [int]
	var max = Math.max(_R, _G, _B);		//%	maximum of all color channels [int]
	var D = max - min; 					//%	(DOCUMENTME)
	
	V = max;
	
	if (D == 0) {						//	no chroma
		H = 0;
		S = 0;
	} else { 							// 	chromatic data

		S = D / max;

		var DR = ( ((max - _R) / 6) + (D / 2) ) / D; 
		var DG = ( ((max - _G) / 6) + (D / 2) ) / D; 
		var DB = ( ((max - _B) / 6) + (D / 2) ) / D; 

		if (_R == max) { H = DB - DG; }
		else if (_G == max) { H = (1 / 3) + DR - DB; }
		else if (_B == max) { H = (2 / 3) + DG - DR; }

		if (H < 0) { H += 1; }
		if (H > 1) { H -= 1; }

	}
	
	return { H: H * 360, S: S * 100, V: V * 100 }; 

}; 

//--------------------------------------------------------------------------------------------------
//.	convert HSV color to RGB
//--------------------------------------------------------------------------------------------------
//TODO: replace single letter variable names with something readable
//arg: o - an HSV color [object]

Color.HSV_RGB = function(o) { 

	var H = o.H / 360;			//%	hue (0 - 1) [float]
	var S = o.S / 100;			//%	saturation (0 - 1) [float]
	var V = o.V / 100;			//%	value / brightness (0 - 1) [float]

	var R;						//%	red (0 - 255) [int]
	var G;						//%	green (0 - 255) [int]
	var B; 						//%	blue (0 - 255) [int]
	
	if (S == 0) {
		R = Math.round(V * 255);
		G = Math.round(V * 255);
		B = Math.round(V * 255);

	} else { 

		if (H >= 1) { H = 0; }	//	invalid hue?

		//see: http://en.wikipedia.org/wiki/File:Hsv-hexagons-to-circles.svg

		H = 6 * H;
		D = H - Math.floor(H); 
		A = Math.round(255 * V * (1 - S)); 
		B = Math.round(255 * V * (1 - (S * D))); 
		C = Math.round(255 * V * (1 - (S * (1 - D)))); 
		V = Math.round(255 * V); 

		switch(Math.floor(H)) {

			case 0:	R = V;	G = C;	B = A;	break; 
			case 1:	R = B;	G = V;	B = A;	break; 
			case 2:	R = A;	G = V;	B = C;	break; 
			case 3:	R = A;	G = B;	B = V;	break; 
			case 4:	R = C;	G = A;	B = V;	break; 
			case 5:	R = V;	G = A;	B = B;	break; 

		}
	}

	return { R: R, G: G, B: B };

};

</script>
</body>
</html>
