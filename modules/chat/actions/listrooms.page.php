<? /*
<page>
	<template>twocol-rightnav.template.php</template>
	<title>%%websiteName%% - Chat (all rooms)</title>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:chat::menu:]]</menu2>
	<content>
		[[:theme::navtitlebox::label=Global Rooms:]]
		[[:chat::listrooms::pageNo=%%pageNo%%::pageSize=%%pageSize%%::orderBy=%%orderBy%%::state=global:]]
		<br/>
		[[:theme::navtitlebox::label=Local Rooms:]]
		[[:chat::listrooms::pageNo=%%pageNo%%::pageSize=%%pageSize%%::orderBy=%%orderBy%%::state=local:]]

	</content>
	<head></head>
	<script></script>
	<nav1>
		[[:chat::unreadnav:]]

		[[:chat::addroomnav:]]
		<br/>

		[[:theme::navtitlebox::label=Peers::toggle=divChatPeers:]]
		<div id='divChatPeers'>
		[[:chat::listpeersnav:]]
		</div>
		<br/>

	</nav1>
	<nav2></nav2>
	<nav2></nav2>
	<banner></banner>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
