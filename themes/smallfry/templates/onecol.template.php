<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>%%title%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%home/css/windows.css' rel='stylesheet' type='text/css' />
<link href='%%serverPath%%home/css/default.css' rel='stylesheet' type='text/css' />
<link href='%%serverPath%%themes/%%defaultTheme%%/images/icons/favicon.ico' rel='shortcut icon' type='image/x-icon' />

<link href='%%serverPath%%themes/%%defaultTheme%%/css/jquery-ui.custom.css' rel='stylesheet' type='text/css' />
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
	var jsTheme = '%%defaultTheme%%';
	var formChecks = new Array();
	var kchatclient = 0;
	var isMobile = false;
</script>

<script src='%%serverPath%%themes/%%defaultTheme%%/js/jquery.js'></script>
<script src='%%serverPath%%core/utils.js'></script>
<script src='%%serverPath%%themes/%%defaultTheme%%/js/jquery.js'></script>
<script src='%%serverPath%%modules/live/js/live.js'></script>
<script src='%%serverPath%%modules/live/js/windows.js'></script>
<script src='%%serverPath%%modules/editor/js/HyperTextArea.js'></script>
<script src='%%serverPath%%modules/live/js/chat.js'></script>
<script src='%%serverPath%%modules/like/js/like.js'></script>
<script src='%%serverPath%%modules/images/js/slideshow.js'></script>
<script src='%%serverPath%%modules/tags/js/search.js'></script>
<script src='%%serverPath%%modules/images/js/jquery.pikachoose.full.js'></script>
<!-- <script src='%%serverPath%%themes/vega/js/jquery-ui-1.8.20.custom.min.js'></script> -->

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

<body id='jqBody'>

<!-- hidden by default -->
<div class='overlay' id='divModal'></div>
<div id='msgDiv'></div>

[[:theme::usermenu:]]

<br/><br/>

<div class='mainTable' id='mainTable'>

<table class='tableborder' cellspacing='0' cellpadding='0' height='100%'>

  <tr>
	<!-- white border -->
	<td width='40px'>&nbsp;</td>
	<!-- center column -->
	<td valign=top >
	<table border=0 cellspacing=0 cellpadding=0 valign='top' width='920'>

	  <tr>
	    <td height='25' width='940' align='left'>
			<br/>
		  <div class='submenu' style='align: left;'>
			<small>&nbsp;</small>
			%%menu2%%
		  </div>
		</td>
	  </tr>	

	  <tr><td><br/></td></tr>

	  <tr>
	    <td>
			<div class='block'><span style='position: relative;'>%%breadcrumb%%</span></div>
		</td>
	  </tr>	

	  <tr>
	    <td>
	
		<table border='0' width='100%' cellpadding='0' cellspacing='0' valign='top'>
		  <tr>

		    <td valign='top' align='left'>
				<br/>
				%%sMessage%%
				%%content%%
				<div class='spacer'></div>
				%%nav1%%
				%%nav2%%
		    </td>			
			
		  </tr>
		</table>

	    </td>
	  </tr>	
	  <tr>
			<td></td>
	  </tr>
	</table>
	<br/><br/>
    </td>
	<!-- white border -->
	<td width='40px' valign='top'>&nbsp;</td>
  </tr>	
</table>
</div>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>

<div id='pumpDiv' name='xPump'></div>
<br/>
<br/><br/>

%%debug%%

<div
	id='footer'
	style="background-color: #3d4246; height: 25px; border-top: 1px solid #aaaaaa; border-bottom: 1px solid #aaaaaa;'"
>
	<div class='spacer'></div>
		<span style='color: #eee;'>
			<small>
				&nbsp; 
				<a href='%%serverPath%%awareNet-Privacy-Policy' class='footer'>Privacy</a> | 
				<a href='%%serverPath%%awarenet-Copyright-Policy' class='footer'>Copyright</a> |
				<a href='#' class='footer'>Abuse</a> |  
				<a href='%%serverPath%%' class='footer'>Contact</a> &nbsp;
			</small>			  
		</span>
</div>

<script language='Javascript'>kPageInit();</script>
<div id='debugger'></div>

</body>
</html>

*/ ?>
