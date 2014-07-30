<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - chat room</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Chat Server:]]
		[`|lt]h2[`|gt]%%title%%[`|lt]/h2[`|gt]
		[`|lt]p[`|gt]%%description%%[`|lt]/p[`|gt]
		<small>
			UID: %%UID%%<br/>
			RH: %%rh%%<br/>
			RM: %%rm%%<br/>
		</small><br/>
		<hr/><br/>

		[[:theme::navtitlebox::label=Members:]]
		[[:chatserver::membership::roomUID=%%UID%%:]]

	</content>
	<nav1>
		[[:chatserver::addpeerform:]]
		<br/>

		[[:theme::navtitlebox::label=Current Hashes::toggle=divHashes:]]
		<div id='divHashes'>
		[[:chatserver::hashes:]]
		</div>
		<br/>

		[[:theme::navtitlebox::label=User Sessions:]]
		<div id='divSessions'>
		[[:chatserver::listsessionsnav:]]
		</div>

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
