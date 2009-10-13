<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>[[:theme::navtitlebox::label=System Log (live)::width=570:]]
[`|lt]div id='divLiveLog'[`|gt][`|lt]/div[`|gt]
[`|lt]br/[`|gt]
</content>
<title>:: awareNet :: admin :: console ::</title>
<script>
	function msgh_sysPageLog(channel, event, msg) {
		logDebug('msgh_sysPageLog: ' + channel + ' -- ' + event + ' -- ' + msg);
		var theDiv = document.getElementById('divLiveLog');
		theDiv.innerHTML = theDiv.innerHTML + msg + "[`|lt]br/[`|gt]\n";
	}
</script>
<nav1>[[:admin::subnav:]]</nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:admin::menu:]]</menu2>
<section></section>
<subsection></subsection>
<breadcrumb>[[:theme::breadcrumb::label=Administration - ::link=/admin/:]]
[[:theme::breadcrumb::label=Console::link=/admin/:]]</breadcrumb>
</page>\n*/ ?>
