<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>%%pageTitle%%</title>

	<link href='%%serverPath%%home/css/windows.css' rel='stylesheet' type='text/css' />

	<script language='javascript'>
		var kapentaLoaded = false;
		var jsServerPath = '%%serverPath%%';
		var jsPageUID = '%%pageInstanceUID%%';
		var jsUserUID = '%%jsUserUID%%';
		var jsUserName = '%%jsUserName%%';
		var jsTheme = '%%jsTheme%%';
		var ifMaxHeight = -1;
		var isMobile = %%jsMobile%%;
	</script>

	<script src='%%serverPath%%core/utils.js'></script>
	<script src='%%serverPath%%themes/%%defaultTheme%%/js/jquery.js'></script>
	<script src='%%serverPath%%modules/live/js/genericwindow.js'></script>

	<!-- TODO: conditional include via $page->requireJs() -->
	<!-- <script src='%%serverPath%%modules/editor/js/HyperTextArea.js'></script> -->

	%%head%%

	<script language='javascript'>

		//------------------------------------------------------------------------------------------
		//	set up generic window object
		//------------------------------------------------------------------------------------------

		var kwnd = new Live_GenericWindow();

		//------------------------------------------------------------------------------------------
		//	additional Javascript from page template goes here
		//------------------------------------------------------------------------------------------
		%%script%%

	</script>

</head>

<body onLoad='kwnd.onLoad();' onResize='kwnd.onResize();'> 
%%content%%
</body>
</html>
*/ ?>
