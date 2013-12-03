<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>window.template.php</template>
	<title>awareNet - %%abuse reports%%</title>
	<content>
		<form name='reportAbuse'>
		<input type='hidden' name='refModule' value='%%refModule%%' />
		<input type='hidden' name='refModel' value='%%refModel%%' />
		<input type='hidden' name='refUID' value='%%refUID%%' />
		<input type='hidden' name='fromUrl' value='unknown' id='hdnFromUrl' />
		[[:editor::add::name=report::width=548::height=400:]]
		<div id='divWndBtn' style='position: absolute;'>
		<input type='button' value='Cancel' onClick='kwnd.closeWindow();' />
		<input type='button' value='Submit Abuse Report' onClick='submitReport();' />
		</div>
		</form>
	</content>
	<nav1></nav1>
	<nav2></nav2>
	<script>

		klive = new Live_Pump(jsPageUID, jsServerPath);

		if ((window.parent) && (window.parent.kwindowmanager)) {
			kwindowmanager = window.parent.kwindowmanager;
		}

		//------------------------------------------------------------------------------------------
		//	handle load event
		//------------------------------------------------------------------------------------------

		kwnd.onLoad = function() {
			//if (kwnd.kwm) { kwnd.kwm.windows[kwnd.hWnd].disableResize(); }
			kwnd.onResize();
			if (window.parent) {
				var txtFromurl = document.getElementById('hdnFromUrl');
				txtFromurl.value = window.parent.location;
			}

			if ((window.parent) && (window.parent.khta)) {
				var phta = window.parent.khta;
				phta.areas[phta.areas.length] = khta.getArea('report');
			}
			
		}

		//------------------------------------------------------------------------------------------
		//	handle close event
		//------------------------------------------------------------------------------------------

		kwnd.onResize = function() {
			var hta = khta.areas[0];
			var cbHeight = $('#chkSrc' + hta.name).height();
			var btnHeight = $('#divWndBtn').height();
			var ifTop = $('#ifHt' + hta.name).offset().top;
			var wH = $(window).height();
			var wW = $(window).width();

			var ifHeight = wH - ifTop - btnHeight - cbHeight + 10;

			$('#divWndBtn').css('top', wH - btnHeight);
			$('#divWndBtn').css('left', wW - $('#divWndBtn').width());

			$('#ifHt' + hta.name).width($(window).width() - 2);
			$('#ifHt' + hta.name).height(ifHeight);
		}

		//------------------------------------------------------------------------------------------
		//	submit abuse report
		//------------------------------------------------------------------------------------------

		function submitReport() {
			var report = khta.getContent('report');

			document.body.innerHTML = '' 
			 + "<div class='chatmessageblack'><h2>ABUSE REPORT</h2><br/>"
			 + '<b>refUID:</b> %%refUID%%<br/>'
			 + '<b>refModel:</b> %%refModel%%<br/>'
			 + '<b>refModule:</b> %%refModule%%<br/></div>'
		     + "<div class='chatmessageblack'>" + report + "<br/></div>"
		     + "<div class='chatmessageblack'>Please wait...<br/></div>"
			;

			params = ''
			 + 'action=newReport&'
			 + 'refModule=%%refModule%%&'
			 + 'refModel=%%refModel%%&'
			 + 'refUID=%%refUID%%&'
			 + 'fromUrl=' + (window.parent ? window.parent.location : 'unknown') + '&'
			 + 'comment=' + encodeURIComponent(report) + ''
			;

			var cbFn = function(responseText, status) {							
				kwnd.setBanner('Report sent.');
				kwnd.setStatus('Done.');
				document.innerHTML = document.innerHTML + "<div class='chatmessagegreen'>Sent</div>";
				alert('Report sent, thank you for letting us know.');
				kwnd.closeWindow();
			}

			kutils.httpPost('%%serverPath%%abuse/newreport/', params, cbFn);

			kwnd.setBanner('Submitting report...');

		}

</script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:abuse::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
