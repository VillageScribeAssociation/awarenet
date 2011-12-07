<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>window.template.php</template>
	<title>awareNet - [`|pc][`|pc]shell[`|pc][`|pc]</title>
	<content>
	<div id='divSessionSummary' style='background-color: #ffffff;'>
	</div>
	<hr/>
	<div 
		id='divHistory' 
		style='width: 200px; height: 60px; position: absolute; overflow: auto;'
	>
	<div id='histWelcome' class='chatmessagered'>
	<b>Kapenta Web Shell v0.0 alpha</b><br/>
	Type <tt>live.help</tt> for more a list of available commands and utilities.<br/>
	</div>
	</div>

	<textarea 
		name='content' 
		id='content'
		style='width: 200px; height: 60px; left: 0px; top: 100px; position:absolute;'
	></textarea>
	<br/>
</content>
	<nav1></nav1>
	<nav2></nav2>
	<script>[`|tb]//----------------------------------------------------------------------------------------------
[`|tb]//[`|tb]handle load event
[`|tb]//----------------------------------------------------------------------------------------------

[`|tb]function window[`|us]onLoad() {
[`|tb][`|tb]//TODO: more error checking here
		kshellwindow = new Live_ShellWindow(
			jsServerPath, 
			jsUserName, 
			windowIdx
		);
[`|tb]}

[`|tb]//--------------------------------------------------------------------------------------------
[`|tb]//[`|tb]handle close event
[`|tb]//--------------------------------------------------------------------------------------------

[`|tb]function window[`|us]onResize() {
		//------------------------------------------------------------------------------------------
		//	resize controls
		//------------------------------------------------------------------------------------------
		var divCPS = document.getElementById('divSessionSummary');			//%	div
		var divH = document.getElementById('divHistory');					//%	div
		var txtBox = document.getElementById('content');					//% textarea
		var vpWidth = -1;
		var vpHeight = -1;

		if (window.innerWidth) {
			vpWidth = window.innerWidth;				// modern browsers
			vpHeight = window.innerHeight;
		}

		if ((document.documentElement) && (document.documentElement.clientWidth)) {
			vpWidth = document.documentElement.clientWidth;		// available on old IE and Opera
			vpHeight = document.documentElement.clientHeight;
		}

		if (-1 == vpWidth) { alert('Cannot get vieport size'); }

		txtBox.cHeight = extractNumberCW(txtBox.style.height)
		txtBox.style.top = (vpHeight - txtBox.cHeight - 8) + 'px';
		txtBox.style.width = (vpWidth - 6) + 'px';

		divH.style.height = (vpHeight - divCPS.clientHeight - txtBox.cHeight - 25) + 'px';
		divH.style.width = (vpWidth - 6) + 'px';
[`|tb]}

[`|tb]//----------------------------------------------------------------------------------------------
[`|tb]//[`|tb]submit abuse report
[`|tb]//----------------------------------------------------------------------------------------------

[`|tb]function submitReport() {
[`|tb][`|tb]var report = khta.getContent([`|sq]report[`|sq])[`|sc]
[`|tb][`|tb]document.body.innerHTML = [`|sq][`|sq] 
[`|tb][`|tb] + [`|dq][`|lt]div class=[`|sq]chatmessageblack[`|sq][`|gt][`|lt]h2[`|gt]ABUSE REPORT[`|lt]/h2[`|gt][`|lt]br/[`|gt][`|dq]
[`|tb][`|tb] + [`|sq][`|lt]b[`|gt]refUID:[`|lt]/b[`|gt] [`|pc][`|pc]refUID[`|pc][`|pc][`|lt]br/[`|gt][`|sq]
[`|tb][`|tb] + [`|sq][`|lt]b[`|gt]refModel:[`|lt]/b[`|gt] [`|pc][`|pc]refModel[`|pc][`|pc][`|lt]br/[`|gt][`|sq]
[`|tb][`|tb] + [`|sq][`|lt]b[`|gt]refModule:[`|lt]/b[`|gt] [`|pc][`|pc]refModule[`|pc][`|pc][`|lt]br/[`|gt][`|lt]/div[`|gt][`|sq]
[`|tb]     + [`|dq][`|lt]div class=[`|sq]chatmessageblack[`|sq][`|gt][`|dq] + report + [`|dq][`|lt]br/[`|gt][`|lt]/div[`|gt][`|dq]
[`|tb]     + [`|dq][`|lt]div class=[`|sq]chatmessageblack[`|sq][`|gt]Please wait...[`|lt]br/[`|gt][`|lt]/div[`|gt][`|dq]
[`|tb][`|tb][`|sc]

[`|tb][`|tb]params = [`|sq][`|sq]
[`|tb][`|tb] + [`|sq]action=newReport&[`|sq]
[`|tb][`|tb] + [`|sq]refModule=[`|pc][`|pc]refModule[`|pc][`|pc]&[`|sq]
[`|tb][`|tb] + [`|sq]refModel=[`|pc][`|pc]refModel[`|pc][`|pc]&[`|sq]
[`|tb][`|tb] + [`|sq]refUID=[`|pc][`|pc]refUID[`|pc][`|pc]&[`|sq]
[`|tb][`|tb] + [`|sq]comment=[`|sq] + encodeURIComponent(report) + [`|sq][`|sq]
[`|tb][`|tb][`|sc]

[`|tb][`|tb]var submitCallback = function(responseText, status) {[`|tb][`|tb][`|tb]
[`|tb][`|tb][`|tb]window.parent.kwindowmanager.windows[windowIdx].setStatus([`|sq]Done.[`|sq])[`|sc]
[`|tb][`|tb][`|tb]document.innerHTML = document.innerHTML + [`|dq][`|lt]div class=[`|sq]chatmessagegreen[`|sq][`|gt]Sent[`|lt]/div[`|gt][`|dq][`|sc]
[`|tb][`|tb][`|tb]alert([`|sq]Report sent, thank you for letting us know.[`|sq])[`|sc]
[`|tb][`|tb][`|tb]closeWindow()[`|sc]
[`|tb][`|tb]}

[`|tb][`|tb]kutils.httpPost([`|sq][`|pc][`|pc]serverPath[`|pc][`|pc]abuse/newreport/[`|sq], params, submitCallback)[`|sc]

[`|tb][`|tb]window.parent.kwindowmanager.windows[windowIdx].setStatus([`|sq]Submitting report...[`|sq])[`|sc]

[`|tb]}

[`|tb]//----------------------------------------------------------------------------------------------
[`|tb]//[`|tb]remove [`|sq]px[`|sq] from numbers //TODO: add this to /core/utils.js
[`|tb]//----------------------------------------------------------------------------------------------
[`|tb]
[`|tb]function extractNumberCW(value) { 
[`|tb][`|tb]var n = parseInt(value)[`|sc] 
[`|tb][`|tb]return n == null || isNaN(n) ? 0 : n[`|sc] 
[`|tb]} 
</script>
	<jsinit></jsinit>
	<banner></banner>
	<head><script src='%%serverPath%%modules/live/js/shellwindow.js'></script></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:abuse::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
