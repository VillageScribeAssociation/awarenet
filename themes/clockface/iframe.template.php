<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>%%title%%</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='%%serverPath%%themes/%%defaultTheme%%/css/iframe.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
.style1 {font-size: 9px}
</style>

<script src='%%serverPath%%core/utils.js'></script>
<script language='javascript'>

	var kapentaLoaded = false;

	function kPageInit() {
		resizeFrame();
		kapentaLoaded = true;
	}

	ifMaxHeight = -1;

	function resizeFrame() {
		frameObj = window.parent.document.getElementsByName(window.name);
		if (frameObj[0]) {
			if (-1 == ifMaxHeight) {
				// iframe height same as content height
				frameObj[0].height = document.body.offsetHeight + 40;

			} else {
				// iframe height same as content height unless greater than maxHeight
				if ((document.body.offsetHeight + 40) > ifMaxHeight) {
					frameObj[0].height = ifMaxHeight;
				} else {
					frameObj[0].height = document.body.offsetHeight + 40;
				}
			}
		}
	}

%%script%%
</script>
</head>

<body onLoad='kPageInit();'> 
<i>%%sMessage%%</i>
%%content%%
%%nav1%%
%%nav2%%
</body>
</html>
*/ ?>
