<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>%%title%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%themes/%%defaultTheme%%/css/clockface.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
.style1 {font-size: 9px}
</style>

<script src='%%serverPath%%modules/chat/chat.js'></script>

<script language='javascript'>

	var hasParentFrame = false;
	var awareNetChat = true;
	var jsServerPath = '%%serverPath%%';

	%%script%%

	//----------------------------------------------------------------------------------------------
	//	initialise page
	//----------------------------------------------------------------------------------------------

	function kPageInit() {
		chatInit();
	}

</script>
</head>

<body onLoad="kPageInit();"> 
<center>
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
			<small>&nbsp;</small>
			%%menu2%%
		</td>
	  </tr>	
	  <tr><td><br/></td></tr>

	  <tr>
	    <td>%%breadcrumb%%</td>
	  </tr>	

	  <tr>
	    <td>
	
		<table border='0' width='100%' cellpadding='0' cellspacing='0' valign='top'>
		  <tr>

		    <td valign='top' align='left'>
				<br/>
				<i>%%sMessage%%</i>
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
<div id='msgDiv'></div>
<div id='pumpDiv' name='xPump'></div>
<br/>
<br/><br/>
</center>
</body>
</html>
*/ ?>
