<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>%%title%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%themes/%%defaultTheme%%/css/clockface.css' rel='stylesheet' type='text/css' />
<link href='%%serverPath%%themes/%%defaultTheme%%/css/windows.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
.style1 {font-size: 9px}
</style>

<script src='%%serverPath%%core/utils.js'></script>
<script src='%%serverPath%%modules/live/js/live.js'></script>
<script src='%%serverPath%%modules/live/js/windows.js'></script>

%%head%%

<script language='javascript'>

	var hasParentFrame = false;
	var awareNetChat = true;
	var jsServerPath = '%%serverPath%%';
	var jsPageUID = '%%pageInstanceUID%%';
	var jsUserUID = '%%jsUserUID%%';
	var formChecks = new Array();

	//----------------------------------------------------------------------------------------------
	//	initialise page
	//----------------------------------------------------------------------------------------------

	function kPageInit() {
		%%jsinit%%

		//------------------------------------------------------------------------------------------
		//	create global objects
		//------------------------------------------------------------------------------------------
		klive = new Live_Pump(jsPageUID, jsServerPath);			// create the message pump
		klive.start();											// start the message pump

		kwindowmanager = new Live_WindowManager();
		kmouse = new Live_Mouse();

		//if (true == awareNetChat) { chatInit(); }

		// set checks for form completion, TODO: try to avoid this closure
		window.onbeforeunload = function() {
			testResult = formCheckExecuteAll();
			if (testResult != false) { return testResult; }
		}
	}

	//---------------------------------------------------------------------------------------------
	//	defined by page tempate:
	//---------------------------------------------------------------------------------------------

	%%script%%

</script>
</head>

<body onLoad="kPageInit();" > 
<center>
<div id='msgDiv'></div>
<table class='tableborder' cellspacing='0' cellpadding='0' height='100%' class='table_main' >

  <tr>
	<!-- white border -->
	<td width='40'></td>
	<!-- center column -->
	<td valign=top >
	<table border=0 cellspacing=0 cellpadding=0 valign='top' width='900'>

	  <tr>
	    <td width='900' height='41' background='/themes/%%defaultTheme%%/images/menuTop.png'>
			%%menu1%%
		</td>
	  </tr>

	  <tr>
	    <td width='900' height='28' background='%%serverPath%%themes/clockface/images/menuBottom.png'>
			<span style='float: left;'><small>&nbsp;</small>%%menu2%%</span>
		</td>
	  </tr>	
	  <tr><td><br/></td></tr>

	  <tr>
	    <td><span style='float: left;'>%%breadcrumb%%</span></td>
	  </tr>	

	  <tr>
	    <td>
	
		<table border='0' width='100%' cellpadding='0' cellspacing='0' valign='top'>
		  <tr>

		    <td valign='top' align='left'>
				<br/>
				%%sMessage%%
				%%content%%
				<br/><br/><br/>
		    </td>

		    <td valign='top' align='left' width='30'></td>

		    <td valign='top' align='left' width='300'>
			<br/>
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
	<td width='40'></td>
  </tr>	
  <tr>
	<!-- white border -->
	<td width='40'></td>
	<!-- footer -->
    <td bgcolor='%%clrMenu1bg%%' height='%%pxxMenu2height%%' align='center'>
		<small><span style='color: #eee;'>awareNet is developed by <a href='http://eckayaICT.com' class='menu'>eKhaya ICT</a>
				building bridges across the digital divide.</span>
		</small>
    </td>
	<!-- white border -->
	<td width='40'></td>
  </tr>	
</table>
<div id='pumpDiv' name='xPump'></div>
<br/>
<br/><br/>
%%debug%%
</center>
<div id='debugger'></div>
</body>
</html>
*/ ?>
