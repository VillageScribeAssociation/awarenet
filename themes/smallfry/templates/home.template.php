<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>%%title%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%home/css/default.css' rel='stylesheet' type='text/css' />
<link href='%%serverPath%%themes/%%defaultTheme%%/images/icons/favicon.ico' rel='shortcut icon' type='image/x-icon' />

<style type='text/css'>

  .frametable {
	background: transparent;
	background-color: transparent;
	border: 0px;
	color: #aaaaaa;
  }

  input {
	border-radius: 7px;   			/* css 3 */
	-moz-border-radius: 7px;		/* mozilla */
	-webkit-border-radius: 7px;		/* webkit */
  }

</style>

<script language='javascript'>

	var kapentaLoaded = false;
	var hasParentFrame = false;
	var awareNetChat = true;
	var jsServerPath = '%%serverPath%%';
	var jsPageUID = 'axkg3672wqixfxpztg';
	var jsUserUID = '%%jsUserUID%%';
	var jsTheme = 'smallfry';
	var formChecks = new Array();
	var kchatclient = 0;
	var isMobile = false;
	%%script%%
</script>

<script src='%%serverPath%%themes/smallfry/js/jquery.js'></script>
<script src='%%serverPath%%core/utils.js'></script>
<script src='%%serverPath%%themes/smallfry/js/jquery.js'></script>
<script src='%%serverPath%%modules/live/js/live.js'></script>
<script src='%%serverPath%%modules/live/js/windows.js'></script>
<script src='%%serverPath%%modules/editor/js/HyperTextArea.js'></script>
<script src='%%serverPath%%modules/live/js/chat.js'></script>
<script src='%%serverPath%%modules/like/js/like.js'></script>
<script src='%%serverPath%%modules/images/js/slideshow.js'></script>
<script src='%%serverPath%%modules/tags/js/search.js'></script>
<script src='%%serverPath%%modules/images/js/jquery.pikachoose.full.js'></script>

<script language='javascript'>

	//----------------------------------------------------------------------------------------------
	//	initialise page (called at end of HTML but before page is fully loaded)
	//----------------------------------------------------------------------------------------------

	function kPageInit() {

		//------------------------------------------------------------------------------------------
		//	create global objects
		//------------------------------------------------------------------------------------------
		//klive.start();											// start the message pump
		kwindowmanager = new Live_WindowManager();				// create window manager
		kmouse = new Live_Mouse();								// ... used by window manager

		kapentaLoaded = true;
	}	

	//---------------------------------------------------------------------------------------------
	//	defined by page tempate:
	//---------------------------------------------------------------------------------------------

	$(document).ready(
		function() {
			kPageInit();
		}
	);

</script>

</head>

<body id='jqBody'>
<br/>
<table class='frametable' style='width: 100%;'>
  <tr class='frametable'>
    <td width='50px' valign='top'><!-- spacer --></td>
    <td class='frametable' valign='top'>
		<!-- left column, transparent, elastic -->
		<div id='divLeftContent' style='position: relative; top: -20px; color: #ffffff;'></div>
    </td>
    <td width='50px' valign='top'><!-- spacer --></td>
    <td width='300px' valign='top'>
		<!-- center column contains page content -->

		%%content%%

    </td>
    <td width='50px' valign='top'><!-- spacer --></td>
    <td width='150px' valign='top'>
		<!-- rightmost column contains logo -->
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/awareNetLogo-home.png' />

		<br/><br/>

		[[:theme::keywords:]]

    </td>
    <td width='50px' valign='top'><!-- spacer --></td>
</div>
  </tr>
</table>

<br/><br/><br/>

<hr style='border-top: 0px; border-bottom: 0px; height: 1px; background-color: #555555;' />
<span style='margin-left: 50px;'>
	<a href='%%serverPath%%awareNet-Privacy-Policy' class='footer'>privacy policy</a>&nbsp; 
	<a href='%%serverPath%%awareNet-Copyright-Policy' class='footer'>copyright</a>&nbsp;
	<a href='%%serverPath%%wiki/' class='footer'>help</a>&nbsp;
	<a href='%%serverPath%%Contact-' class='footer'>contact</a>&nbsp;
</span>
</body>
</html>
*/ ?>
