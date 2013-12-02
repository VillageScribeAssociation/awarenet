<?php

//--------------------------------------------------------------------------------------------------
//*	expample page to test startup of KapentaJS
//--------------------------------------------------------------------------------------------------

	$html = "<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8' />
		<title>KapentaJS</title>
		<meta name='viewport' content='width=device-width, initial-scale=1'> 
		<link rel='stylesheet' href='http://code.jquery.com/mobile/1.3.0-rc.1/jquery.mobile-1.3.0-rc.1.min.css' />
		<script src='http://code.jquery.com/jquery-1.9.0.min.js'></script>
		<script src='http://code.jquery.com/mobile/1.3.0-rc.1/jquery.mobile-1.3.0-rc.1.min.js'></script>
		<script src='%%serverPath%%kjs/build/'></script>
		<script src='%%serverPath%%modules/kjs/js/ydn.db-dev.js'></script>
		<script language='Javascript'>
			$(function() {
				kapenta.db.init();
				kapenta.actions.run(kapenta.defaultController, kapenta.defaultAction);
			});
		</script>
	</head>
	<body>
	<div id='divPage' data-role='page'>
		<!-- start header -->
		<div id='divPageHeader' data-role='header'>
			<b>KapentaJS</b>
			<span id='spanPageMenu'></span>
		</div>
		<!-- end header -->

		<!-- message block -->
		<div id='divSystemMessages' data-role='content'></div>
		<!-- end block -->

		<!-- start content -->
		<div id='divPageContent' data-role='content'>	
			<p>Loading, please wait...</p>		
		</div>
		<!-- end content -->

		<!-- start footer -->
		<div id='divPageFooter' data-role='footer'>	
			<small>Hello down here.</small>
		</div>
		<!-- end footer -->

	</div>
	</body>
</html>";

	$html = str_replace('%%serverPath%%', $kapenta->serverPath, $html);

	echo $html;

?>
