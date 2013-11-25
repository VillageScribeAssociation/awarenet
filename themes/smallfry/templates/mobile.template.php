<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>%%title%%</title>
<!-- <meta name="viewport" content="width=320"> -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%home/css/mobile.css' rel='stylesheet' type='text/css' />
<link href='%%serverPath%%home/css/windows.css' rel='stylesheet' type='text/css' />
<link href='%%serverPath%%themes/%%defaultTheme%%/images/icons/faviconpink.ico' rel='shortcut icon' type='image/x-icon' />
<style type='text/css'>
.style1 {font-size: 9px}
html {width: 320px}
</style>

<script language='javascript'>
	var kapentaLoaded = false;
	var hasParentFrame = false;
	var awareNetChat = true;
	var jsServerPath = '%%serverPath%%';
	var jsPageUID = '%%pageInstanceUID%%';
	var jsUserUID = '%%jsUserUID%%';
	var jsTheme = '%%defaultTheme%%';
	var formChecks = new Array();
	var kchatclient = 0;
	var isMobile = true;
</script>

<script src='%%serverPath%%themes/%%defaultTheme%%/js/jquery.js'></script>
<script src='%%serverPath%%core/utils.js'></script>
<script src='%%serverPath%%modules/live/js/live.js'></script>
<script src='%%serverPath%%modules/live/js/windows.js'></script>
<script src='%%serverPath%%modules/editor/js/HyperTextArea.js'></script>
<script src='%%serverPath%%modules/live/js/chat.js'></script>
<script src='%%serverPath%%modules/like/js/like.js'></script>

%%head%%

<script language='javascript'>

	//----------------------------------------------------------------------------------------------
	//	initialise page (called at end of HTML but before page is fully loaded)
	//----------------------------------------------------------------------------------------------

	function kPageInit() {

		//------------------------------------------------------------------------------------------
		//	create global objects
		//------------------------------------------------------------------------------------------
		//kutils = new Kapenta_Utility(); 						// create utility object
		//klive = new Live_Pump(jsPageUID, jsServerPath);			// create the message pump
		klive.start();											// start the message pump
		kwindowmanager = new Live_WindowManager();				// create window manager
		kmouse = new Live_Mouse();								// ... used by window manager

		if (true == awareNetChat) { kchatclient = new Live_ChatClient(); }

		// set checks for form completion, TODO: try to avoid this closure
		window.onbeforeunload = function() {
			testResult = formCheckExecuteAll();
			if (testResult != false) { return testResult; }
		}

		kapentaLoaded = true;
	}

	//---------------------------------------------------------------------------------------------
	//	defined by page tempate:
	//---------------------------------------------------------------------------------------------

	$(document).ready(
		function() {
			%%jsinit%%
		}
	);

	%%script%%

</script>
</head>

<body onLoad="" >
<div id='msgDiv'></div>

	%%menu1%%

	<div style='background-color: #aaaaaa;'>%%menu2%%</div>
	<span style='float: left;'>%%breadcrumb%%</span><br/><br/>
	<div id='divPageContent' style='background-color: #ffffff; margin-left:3px;'>
	%%sMessage%%
	%%content%%

	<br/>
	%%nav1%%
	%%nav2%%
	</div>

	<br/>
	<table noborder cellpadding='0' cellspacing='0' width='100%'>
	  <tr>
	    <td bgcolor='%%clrMenu1bg%%' height='%%pxxMenu2height%%' align='center'>
				<span style='color: #eee;'>
					<small>
					&nbsp; 
					<a href='%%serverPath%%awareNet-Privacy-Policy' class='footer'>Privacy</a> | 
					<a href='%%serverPath%%awarenet-Copyright-Policy' class='footer'>Copyright</a> |
					<a href='#' class='footer'>Abuse</a> |  
					<a href='%%serverPath%%' class='footer'>Contact</a> &nbsp;
				</small>			  
			</span>
		</td>
	  </tr>
	  <tr>
		<td bgcolor='%%clrMenu1bg%%'>
			<span style='color: #eee;'>
				<small>&nbsp;awareNet is developed by <a href='http://ekhayaICT.com' class='menu'>eKhaya ICT</a>
				building bridges across the digital divide.</small>
			</span>
		</td>
	  </tr>
	</table>

	<div id='pumpDiv' name='xPump'></div>
	%%debug%%
	<script language='Javascript'>kPageInit();</script>
	<div id='debugger'></div>
</body>
</html>

*/ ?>
