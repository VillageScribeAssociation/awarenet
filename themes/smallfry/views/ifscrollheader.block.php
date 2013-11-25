<? /*
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
	<title>%%pageTitle%%</title>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />

	<script src='%%serverPath%%themes/%%defaultTheme%%/js/jquery.js'></script>

	<style type='text/css'>
	%%css%%
	.style1 {font-size: 9px}
	</style>

	<script>

		//------------------------------------------------------------------------------------------
		//	resize this window
		//------------------------------------------------------------------------------------------

		function window_onResize() {
			$('#divScrollContent').width($(window).width());
			$('#divScrollContent').height($(window).height());
		}

		//------------------------------------------------------------------------------------------
		//	scroll to bottom of window
		//------------------------------------------------------------------------------------------

		function scrollToBottom() {
			$("#divScrollContent").prop({ scrollTop: $("#divScrollContent").prop("scrollHeight") });
			$("#divScrollContent").animate(
				{ scrollTop: $('#divScrollContent').prop("scrollHeight") }, "fast"
			);
		}

		function startThrobber(onoff) {

			if ((window.parent) && (window.parent.kwindowmanager)) {
				thisUID = window.name.replace('ifc', '');							//	has a UID		
				thisHWnd = window.parent.kwindowmanager.getIndex(thisUID);
				window.parent.kwindowmanager.windows[thisHWnd].setThrobber(onoff);
			}

		}

		startThrobber(true);

	</script>

</head>
<body onResize="window_onResize();" id='jqBody'>

<div 
	id='divScrollContent' 
	style='
		width: 100%;
		height: 100%;
		position: absolute;
		overflow: auto;
		z-index: 500
	'>

	<script>window_onResize();</script>
<br/>

*/ ?>
