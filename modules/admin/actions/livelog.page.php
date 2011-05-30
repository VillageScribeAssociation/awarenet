<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - live log (admin)</title>
	<content>[[:theme::navtitlebox::label=System Log (live)::width=570:]]
[`|lt]div id=[`|sq]divLiveLog[`|sq][`|gt][`|lt]/div[`|gt]
[`|lt]br/[`|gt]
</content>
	<nav1>[[:admin::subnav:]]</nav1>
	<nav2></nav2>
	<script>
[`|tb]function msgh[`|us]sysPageLog(channel, event, msg) {
[`|tb][`|tb]logDebug([`|sq]msgh[`|us]sysPageLog: [`|sq] + channel + [`|sq] -- [`|sq] + event + [`|sq] -- [`|sq] + msg)[`|sc]
[`|tb][`|tb]var theDiv = document.getElementById([`|sq]divLiveLog[`|sq])[`|sc]
[`|tb][`|tb]theDiv.innerHTML = theDiv.innerHTML + msg + [`|dq][`|lt]br/[`|gt]n[`|dq][`|sc]
[`|tb]}
</script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:admin::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Administration - ::link=/admin/:]]
[[:theme::breadcrumb::label=Console::link=/admin/:]]</breadcrumb>
</page>

*/ ?>