<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>:: kapenta :: live :: shell</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%themes/clockface/css/windows.css' rel='stylesheet' type='text/css' />
<style type='text/css'>

body {
	padding: 0;
	margin: 0;
	background: #eeeeee;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: smaller;
	color: #303030;
}

a { text-decoration: none; color: #365a10 }

a h1 {
	color: #303030;
}

a h2 {
	color: #303030;
}

.style1 {font-size: 9px}
</style>

<script language='javascript'>
	var kapentaLoaded = false;
	var hasParentFrame = false;
	var awareNetChat = true;
	var jsServerPath = '%%serverPath%%';
	var jsPageUID = '%%pageInstanceUID%%';
	var jsUserUID = '%%jsUserUID%%';
	var jsUserName = '%%jsUserName%%';
	var formChecks = new Array();
	var kchatclient = 0;

	var windowUID = '';
	var windowIdx = 0;
	var kshellwindow = 0;

</script>

<script language='javascript'>

	//----------------------------------------------------------------------------------------------
	//	onLoad
	//----------------------------------------------------------------------------------------------

	function kPageInit() {
		//------------------------------------------------------------------------------------------
		//	create shell window object
		//------------------------------------------------------------------------------------------
		windowUID = window.name.replace('ifc', '');
		windowIdx = window.parent.kwindowmanager.getIndex(windowUID)

		//window.parent.kwindowmanager.windows[windowIdx].disableResize();
		window.parent.kwindowmanager.windows[windowIdx].setStatus('');

		//------------------------------------------------------------------------------------------
		//	register events (TODO: should ony be registered during drag)
		//------------------------------------------------------------------------------------------
		document.onmousemove = function(e) {
			var e = e || window.event;
			var wndThis = window.parent.kwindowmanager.windows[windowIdx];
			var ifThis = window.parent.document.getElementsByName(window.name);	//%	this iframe
			var txtBox = document.getElementById('content');					//% textarea

			docX = e.clientX + ifThis[0].offsetLeft + wndThis.left;
			docY = e.clientY + ifThis[0].offsetTop + wndThis.top;

			// only these properties neede by the window manager
			var newEvt = function() { 
				this.clientX = 0;	
				this.clientY = 0; 
			}

			newEvt.clientX = docX;
			newEvt.clientY = docY;

			//txtBox.value = (docX) + ', ' + (docY) + ' -- ' + wndThis.top + ', ' + wndThis.left;
			window.parent.kmouse.onMouseMove(newEvt);
		}

		//------------------------------------------------------------------------------------------
		//	resize controls
		//------------------------------------------------------------------------------------------
		resizeWindow();
		if (window_onLoad) { window_onLoad(); }
	}

	//----------------------------------------------------------------------------------------------
	//	re/scale controls to fit inside iFrame
	//----------------------------------------------------------------------------------------------

	function resizeWindow() {
		//------------------------------------------------------------------------------------------
		//	resize controls
		//------------------------------------------------------------------------------------------
		if (window_onResize) { window_onResize(); }		// user defined 
	}

	//----------------------------------------------------------------------------------------------
	//	close this window
	//----------------------------------------------------------------------------------------------

	function closeWindow() {
		window.parent.kwindowmanager.closeWindow(windowUID);
	}

	//----------------------------------------------------------------------------------------------
	//	remove 'px' from numbers //TODO: add this to /core/utils.js
	//----------------------------------------------------------------------------------------------
	
	function extractNumberCW(value) { 
		var n = parseInt(value); 
		return n == null || isNaN(n) ? 0 : n; 
	} 

	%%script%%

</script>
<script src='%%serverPath%%core/utils.js'></script>
<script src='%%serverPath%%modules/editor/js/HyperTextArea.js'></script>
%%head%%
</head>

<body onLoad='kPageInit();' onResize='resizeWindow();'> 
%%content%%
</body>
</html>
*/ ?>
