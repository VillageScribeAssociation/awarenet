<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>%%title%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%home/css/iframe.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
.style1 {font-size: 9px}
</style>

<script language='javascript'>
	var kapentaLoaded = false;
	var jsServerPath = '%%serverPath%%';
	var jsPageUID = '%%pageInstanceUID%%';
	var jsUserUID = '%%jsUserUID%%';
	var formChecks = new Array();
	var kchatclient = 0;
	var ifMaxHeight = -1;
	var isMobile = %%jsMobile%%;
</script>

<script src='%%serverPath%%core/utils.js'></script>
<script src='%%serverPath%%themes/%%defaultTheme%%/js/jquery.js'></script>
<script src='%%serverPath%%modules/editor/js/HyperTextArea.js'></script>
<script language='javascript'>

	function kPageInit() {
		kapentaLoaded = true;
		kutils.resizeIFrame();
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

<body onLoad='kPageInit();'> 
<div id='divMain'>
<i>%%sMessage%%</i>
%%content%%
%%nav1%%
%%nav2%%
</div>
</body>
</html>
*/ ?>
