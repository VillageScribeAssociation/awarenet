<? /*

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>%%pageTitle%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%themes/clockface/css/iframe.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
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

	function closeWindow() { window.parent.kwindowmanager.closeWindow(windowUID); }

	//----------------------------------------------------------------------------------------------
	//	scroll to bottom of display
	//----------------------------------------------------------------------------------------------

	function scrollToEnd() { 
		divSC = document.getElementById('divScrollContent');
		divSC.scrollTop = divSC.scrollHeight;
	}

	//----------------------------------------------------------------------------------------------
	//	remove 'px' from numbers //TODO: add this to /core/utils.js
	//----------------------------------------------------------------------------------------------	
	function extractNumberCW(value) { 
		var n = parseInt(value); 
		return n == null || isNaN(n) ? 0 : n; 
	} 

	//----------------------------------------------------------------------------------------------
	//	resize this window
	//----------------------------------------------------------------------------------------------
	function window_onResize() {
		//------------------------------------------------------------------------------------------
		//	resize controls
		//------------------------------------------------------------------------------------------
		var divSC = document.getElementById('divScrollContent');			//%	div

		if (window.innerWidth) {
			vpWidth = window.innerWidth;				// modern browsers
			vpHeight = window.innerHeight;
		}

		if ((document.documentElement) && (document.documentElement.clientWidth)) {
			vpWidth = document.documentElement.clientWidth;		// available on old IE and Opera
			vpHeight = document.documentElement.clientHeight;
		}

		if (-1 == vpWidth) { alert('Cannot get viewport size'); }

		divSC.style.top = '0px';
		divSC.style.left = '5px';
		divSC.style.height = (vpHeight) + 'px';
		divSC.style.width = (vpWidth - 5) + 'px';
		//alert(divSC.style.width + 'x' + divSC.style.height + "\n" + divSC.innerHTML);

	}

</script>
<script src='%%serverPath%%core/utils.js'></script>

<script language='javascript'>

	var kapentaLoaded = false;

	function kPageInit() {
		resizeFrame();
		kapentaLoaded = true;
		//TODO: start throbber here
	}

	ifMaxHeight = -1;

	//----------------------------------------------------------------------------------------------
	//	called by window manager -> window template
	//----------------------------------------------------------------------------------------------

</script>
</head>
<body onResize='window_onResize();'> 
<div id='divScrollContent' style='width: 100%; height: 100%; position: absolute; overflow: auto'>
<script language='Javascript'>resizeWindow();</script>
*/ ?>
